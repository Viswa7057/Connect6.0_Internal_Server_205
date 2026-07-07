<?php
// dialer.php - Asterisk Voiceblast Dialer Background Script
// This script should be run in a loop or via a systemd service / cron job.
// Example command to run in loop: php dialer.php

require_once __DIR__ . '/db.php';

// Prevent execution via web browser for security
if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the Command Line Interface (CLI).\n");
}

// Configuration
$max_concurrency = 15; // Limit active ringing/oncall channels
$asterisk_spool = '/var/spool/asterisk/outgoing'; // Asterisk outgoing directory
$local_spool_fallback = __DIR__ . '/var/spool/asterisk/outgoing'; // Fallback directory for local testing
$trunk = 'SIP/GSM'; // Asterisk Outbound Trunk name (adjust as needed)

// Ensure local fallback directory exists
if (!is_dir($local_spool_fallback)) {
    mkdir($local_spool_fallback, 0755, true);
}

// Determine which spool directory to use
$spool_dir = is_writable($asterisk_spool) ? $asterisk_spool : $local_spool_fallback;
echo "[LOG] " . date('Y-m-d H:i:s') . " - Voiceblast dialer started. Using spool directory: $spool_dir\n";

while (true) {
    try {
        // 1. Get current active lists (within start_time and end_time range)
        $now = date('Y-m-d H:i:s');
        $activeListsStmt = $pdo->prepare("SELECT * FROM lists WHERE start_time <= ? AND end_time >= ? AND is_paused = 0");
        $activeListsStmt->execute([$now, $now]);
        $activeLists = $activeListsStmt->fetchAll();
        
        if (count($activeLists) === 0) {
            echo "[LOG] " . date('Y-m-d H:i:s') . " - No active lists scheduled at this time. Sleeping...\n";
            sleep(5);
            continue;
        }
        
        $activeListIds = array_map(function($list) { return $list['list_id']; }, $activeLists);
        $activeListIdsPlaceholder = implode(',', array_fill(0, count($activeListIds), '?'));
        
        // 2. Count current concurrent active calls (ringing or answered)
        $activeCallsStmt = $pdo->query("SELECT COUNT(*) FROM leads WHERE status IN ('ringing', 'answered')");
        $activeCallsCount = intval($activeCallsStmt->fetchColumn());
        
        echo "[LOG] " . date('Y-m-d H:i:s') . " - Current concurrent calls: $activeCallsCount / $max_concurrency\n";
        
        if ($activeCallsCount >= $max_concurrency) {
            echo "[LOG] " . date('Y-m-d H:i:s') . " - Concurrency limit reached. Waiting...\n";
            sleep(3);
            continue;
        }
        
        // 3. Calculate how many calls we can place in this cycle
        $callsToPlace = $max_concurrency - $activeCallsCount;
        
        // 4. Fetch pending leads from active lists
        $query = "SELECT * FROM leads 
                  WHERE list_id IN ($activeListIdsPlaceholder) AND status = 'pending' 
                  ORDER BY id ASC LIMIT ?";
        
        $stmt = $pdo->prepare($query);
        // Bind parameters. PDO requires index binding or param type specification for LIMIT
        $paramIndex = 1;
        foreach ($activeListIds as $id) {
            $stmt->bindValue($paramIndex++, $id);
        }
        $stmt->bindValue($paramIndex, $callsToPlace, PDO::PARAM_INT);
        $stmt->execute();
        
        $pendingLeads = $stmt->fetchAll();
        
        if (count($pendingLeads) === 0) {
            echo "[LOG] " . date('Y-m-d H:i:s') . " - No pending leads in active lists. Sleeping...\n";
            sleep(5);
            continue;
        }
        
        // Map lists by ID for easy lookup of properties
        $listsLookup = [];
        foreach ($activeLists as $list) {
            $listsLookup[$list['list_id']] = $list;
        }
        
        // 5. Generate Asterisk Call Files for each lead
        foreach ($pendingLeads as $lead) {
            $leadId = $lead['id'];
            $listId = $lead['list_id'];
            $phone = $lead['phone_number'];
            $name = $lead['name'];
            $audioFile = $listsLookup[$listId]['audio_file'];
            
            // Get absolute path of audio file
            // Note: Asterisk needs access to this path. If Asterisk is on another server,
            // the audio files must be shared or stored in a shared location.
            $absoluteAudioPath = realpath(__DIR__ . '/' . $audioFile);
            if (!$absoluteAudioPath) {
                $absoluteAudioPath = __DIR__ . '/' . $audioFile;
            }
            // Strip extension for Asterisk Playback application which looks for format natively
            $audioPathWithoutExtension = preg_replace('/\.(mp3|wav)$/i', '', $absoluteAudioPath);
            
            // Generate Asterisk Call File Content
            // We set variables which our dialplan context [voiceblast] will read.
            $callFileContent = "Channel: $trunk/$phone\n";
            $callFileContent .= "MaxRetries: 0\n";
            $callFileContent .= "RetryTime: 300\n";
            $callFileContent .= "WaitTime: 45\n";
            $callFileContent .= "Context: voiceblast\n";
            $callFileContent .= "Extension: s\n";
            $callFileContent .= "Priority: 1\n";
            $callFileContent .= "Set: LEAD_ID=$leadId\n";
            $callFileContent .= "Set: LIST_ID=$listId\n";
            $callFileContent .= "Set: AUDIO_PATH=$audioPathWithoutExtension\n";
            $callFileContent .= "Set: LEAD_NAME=" . urlencode($name) . "\n";
            
            // Write Call File to a temporary file locally, then move it to the spool folder.
            // Moving is atomic, ensuring Asterisk doesn't pick up a partially written file.
            $tempFilename = "call_{$listId}_{$leadId}.tmp";
            $finalFilename = "call_{$listId}_{$leadId}.call";
            
            $tempPath = __DIR__ . "/$tempFilename";
            $finalPath = "$spool_dir/$finalFilename";
            
            file_put_contents($tempPath, $callFileContent);
            
            // Update Lead state to 'ringing' before spooling to avoid race condition
            $updateStmt = $pdo->prepare("UPDATE leads SET status = 'ringing', dial_time = ? WHERE id = ?");
            $updateStmt->execute([date('Y-m-d H:i:s'), $leadId]);
            
            if (rename($tempPath, $finalPath)) {
                echo "[DIALED] List: $listId, Lead: $name ($phone), ID: $leadId\n";
            } else {
                // If moving failed, revert lead status to pending
                $updateStmt = $pdo->prepare("UPDATE leads SET status = 'pending', dial_time = NULL WHERE id = ?");
                $updateStmt->execute([$leadId]);
                unlink($tempPath);
                echo "[ERROR] Failed to spool call file for Lead ID: $leadId\n";
            }
        }
        
    } catch (\Exception $e) {
        echo "[EXCEPT] " . date('Y-m-d H:i:s') . " - Error: " . $e->getMessage() . "\n";
    }
    
    // Check every 2 seconds
    sleep(2);
}
