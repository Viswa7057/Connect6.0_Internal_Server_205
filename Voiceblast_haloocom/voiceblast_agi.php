#!/usr/bin/php -q
<?php
require_once('/var/lib/asterisk/agi-bin/incoming/phpagi-2.20/phpagi.php');
GLOBAL $agi;
$agi = new AGI();
$agi->verbose("+++++++++++++Inside agi file+++++++++++++");

// Setup database connection
require_once('/var/www/html/Voiceblast_haloocom/db.php');

// Check if the script is invoked with standard dialer action
$first_arg = $argv[1] ?? '';

if (in_array($first_arg, ['answered', 'completed', 'failed'])) {
    // 1. Original Voiceblast Dialer Logic using global $agi
    $action = $first_arg;
    $lead_id = intval($argv[2] ?? 0);

    if ($lead_id <= 0) {
        $agi->verbose("Voiceblast AGI: Invalid Lead ID ($lead_id)", 1);
        exit(0);
    }

    $agi->verbose("Voiceblast AGI: Action: $action, Lead ID: $lead_id", 3);

    try {
        switch ($action) {
            case 'answered':
                // Call answered, update status to 'answered' (On Call)
                $stmt = $pdo->prepare("UPDATE leads SET status = 'answered' WHERE id = ?");
                $stmt->execute([$lead_id]);
                $agi->verbose("Voiceblast AGI: Lead $lead_id marked as ANSWERED (On Call)", 3);
                break;
                
            case 'completed':
                $duration = intval($argv[3] ?? 0);
                $dtmf = trim($argv[4] ?? '');
                $recording = trim($argv[5] ?? '');
                
                // Clean recording path to store relative url in database
                if (!empty($recording)) {
                    $recording = str_replace(__DIR__ . '/../', '', $recording);
                    $recording = str_replace(__DIR__ . '/', '', $recording);
                }
                
                if ($dtmf === 'TIMEOUT' || $dtmf === 'NONE') {
                    $dtmf = null;
                }
                
				$stmt = $pdo->prepare("SELECT status FROM leads WHERE id = ?");
$stmt->execute([$lead_id]);
$current_status = $stmt->fetchColumn();

if ($current_status == 'not_connected') {
    $agi->verbose("Lead $lead_id already marked as FAILED. Skipping completed update.", 3);
    exit(0);
}
                // Call completed successfully, update status, duration, DTMF digit and recording path
                $stmt = $pdo->prepare("UPDATE leads SET status = 'completed', duration = ?, dtmf_response = ?, recording_file = ? WHERE id = ?");
                $stmt->execute([$duration, $dtmf, $recording, $lead_id]);
                
                $agi->verbose("Voiceblast AGI: Lead $lead_id COMPLETED. Duration: $duration s, DTMF: $dtmf, Rec: $recording", 3);
                break;
                
            case 'failed':
                $error_reason = trim($argv[3] ?? 'NO ANSWER');
                
                // Call failed to connect, update status to 'not_connected' or 'failed'
                $stmt = $pdo->prepare("UPDATE leads SET status = 'not_connected', error_reason = ? WHERE id = ?");
                $stmt->execute([$error_reason, $lead_id]);
                
                $agi->verbose("Voiceblast AGI: Lead $lead_id FAILED. Reason: $error_reason", 3);
                break;
        }
    } catch (\Exception $e) {
        $agi->verbose("Voiceblast AGI Error: " . $e->getMessage(), 1);
    }

    exit(0);
} else {
    // 2. New IVR Rating AGI Concept using global $agi
    $calledid = $argv[1] ?? "";
    $callerid = $argv[2] ?? "";
    $ivr_name = $argv[3] ?? "";
    $uniq     = $argv[4] ?? "";
    $channel  = $argv[5] ?? "";
    $rating   = $argv[6] ?? "";

    $agi->verbose("calledid: $calledid callerid: $callerid ivr_name: $ivr_name channel: $channel uniq: $uniq rating: $rating");

    // Save to database
    try {
        $stmt = $pdo->prepare("INSERT INTO ivr_ratings (calledid, callerid, ivr_name, uniq, channel, rating) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$calledid, $callerid, $ivr_name, $uniq, $channel, $rating]);
    } catch (\Exception $e) {
        $agi->verbose("Voiceblast AGI DB Error: " . $e->getMessage(), 1);
    }
    
    exit(0);
}

