<?php
// api/lists.php - List Management & Data Upload Endpoint
require_once __DIR__ . '/../db.php';

// Ensure user is logged in
checkAuth();

$action = $_GET['action'] ?? '';

// Helper to convert Excel letters to column indices
function letterToColumnIndex($letter) {
    $idx = 0;
    $len = strlen($letter);
    for ($i = 0; $i < $len; $i++) {
        $idx = $idx * 26 + (ord($letter[$i]) - 64);
    }
    return $idx - 1;
}

// Convert uploaded audio to Asterisk-compliant mono 8kHz WAV using ffmpeg or sox
function transcodeToWav($source, $target) {
    // Try ffmpeg: 8000Hz, mono, PCM 16-bit wav
    $cmd = "ffmpeg -y -i " . escapeshellarg($source) . " -ar 8000 -ac 1 -c:a pcm_s16le " . escapeshellarg($target) . " 2>&1";
    $output = [];
    $returnVar = -1;
    @exec($cmd, $output, $returnVar);
    
    if ($returnVar === 0 && file_exists($target) && filesize($target) > 0) {
        return true;
    }
    
    // Fallback try sox: 8000Hz, mono, 16-bit wav
    $cmdSox = "sox " . escapeshellarg($source) . " -r 8000 -c 1 -b 16 " . escapeshellarg($target) . " 2>&1";
    $output = [];
    $returnVar = -1;
    @exec($cmdSox, $output, $returnVar);
    
    if ($returnVar === 0 && file_exists($target) && filesize($target) > 0) {
        return true;
    }
    
    return false;
}

// Lightweight native XLSX reader
function parseXLSX($filePath) {
    if (!class_exists('ZipArchive')) {
        return false;
    }
    
    $zip = new ZipArchive;
    if ($zip->open($filePath) !== TRUE) {
        return false;
    }
    
    $sharedStrings = [];
    $sharedStringsEntry = $zip->getFromName('xl/sharedStrings.xml');
    if ($sharedStringsEntry) {
        $xml = @simplexml_load_string($sharedStringsEntry);
        if ($xml) {
            foreach ($xml->si as $si) {
                if (isset($si->t)) {
                    $sharedStrings[] = (string)$si->t;
                } elseif (isset($si->r)) {
                    $text = '';
                    foreach ($si->r as $r) {
                        $text .= (string)$r->t;
                    }
                    $sharedStrings[] = $text;
                } else {
                    $sharedStrings[] = '';
                }
            }
        }
    }
    
    $sheetEntry = $zip->getFromName('xl/worksheets/sheet1.xml');
    if (!$sheetEntry) {
        $zip->close();
        return false;
    }
    
    $xml = @simplexml_load_string($sheetEntry);
    if (!$xml) {
        $zip->close();
        return false;
    }
    
    $rows = [];
    foreach ($xml->sheetData->row as $row) {
        $rowData = [];
        foreach ($row->c as $c) {
            $val = isset($c->v) ? (string)$c->v : '';
            $type = isset($c['t']) ? (string)$c['t'] : '';
            
            if ($type === 's') {
                $val = $sharedStrings[intval($val)] ?? '';
            }
            
            $r = (string)$c['r'];
            preg_match('/([A-Z]+)/', $r, $matches);
            $colLetter = $matches[1] ?? 'A';
            $colIndex = letterToColumnIndex($colLetter);
            $rowData[$colIndex] = $val;
        }
        
        if (!empty($rowData)) {
            $maxIndex = max(array_keys($rowData));
            for ($i = 0; $i <= $maxIndex; $i++) {
                if (!isset($rowData[$i])) {
                    $rowData[$i] = '';
                }
            }
            ksort($rowData);
            $rows[] = $rowData;
        }
    }
    
    $zip->close();
    return $rows;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'list') {
        try {
            // Get list details along with lead counts
            $query = "SELECT l.*, 
                      COUNT(le.id) as total_leads,
                      SUM(CASE WHEN le.status = 'completed' THEN 1 ELSE 0 END) as answered_leads,
                      SUM(CASE WHEN le.status != 'pending' THEN 1 ELSE 0 END) as dialed_leads,
                      SUM(CASE WHEN le.status IN ('failed', 'not_connected') THEN 1 ELSE 0 END) as failed_leads
                      FROM lists l 
                      LEFT JOIN leads le ON l.list_id = le.list_id 
                      GROUP BY l.list_id 
                      ORDER BY l.created_at DESC";
            
            $stmt = $pdo->query($query);
            $lists = $stmt->fetchAll();
            respondSuccess(['lists' => $lists]);
        } catch (\PDOException $e) {
            respondError('Database error: ' . $e->getMessage());
        }
    }
    
    if ($action === 'download') {
        $list_id = $_GET['list_id'] ?? '';
        if (empty($list_id)) {
            respondError('List ID is required.');
        }
        
        try {
            // Check list existence
            $stmt = $pdo->prepare("SELECT name FROM lists WHERE list_id = ?");
            $stmt->execute([$list_id]);
            $listName = $stmt->fetchColumn();
            if (!$listName) {
                respondError('List not found.');
            }
            
            // Retrieve leads
            $stmt = $pdo->prepare("SELECT name, phone_number, status, dtmf_response, dial_time, duration, recording_file FROM leads WHERE list_id = ? ORDER BY id ASC");
            $stmt->execute([$list_id]);
            $leads = $stmt->fetchAll();
            
            // Set headers for file download
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $list_id . '_leads.csv"');
            
            $output = fopen('php://output', 'w');
            // Column Headers
            fputcsv($output, ['Name', 'Phone Number', 'Status', 'DTMF Digit', 'Dialed At', 'Duration (sec)', 'Recording File']);
            
            foreach ($leads as $lead) {
                fputcsv($output, [
                    $lead['name'],
                    $lead['phone_number'],
                    $lead['status'],
                    $lead['dtmf_response'] ?? '',
                    $lead['dial_time'] ?? '',
                    $lead['duration'],
                    $lead['recording_file'] ?? ''
                ]);
            }
            fclose($output);
            exit;
            
        } catch (\PDOException $e) {
            respondError('Database error: ' . $e->getMessage());
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'create') {
        // We are using multipart/form-data
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $start_time = trim($_POST['start_time'] ?? '');
        $end_time = trim($_POST['end_time'] ?? '');
        
        if (empty($name) || empty($start_time) || empty($end_time)) {
            respondError('List Name, Start Time, and End Time are required.');
        }
        
        // Validate date formats
        if (strtotime($start_time) === false || strtotime($end_time) === false) {
            respondError('Invalid Start or End time format.');
        }
        
        if (strtotime($start_time) >= strtotime($end_time)) {
            respondError('Start Time must be before End Time.');
        }
        
        // 1. Handle Audio Upload (mp3 or wav)
        if (!isset($_FILES['audio']) || $_FILES['audio']['error'] !== UPLOAD_ERR_OK) {
            respondError('Audio file is required and must upload successfully.');
        }
        
        $audioFile = $_FILES['audio'];
        $audioExt = strtolower(pathinfo($audioFile['name'], PATHINFO_EXTENSION));
        if (!in_array($audioExt, ['mp3', 'wav'])) {
            respondError('Only MP3 and WAV audio formats are allowed.');
        }
        
        // 2. Handle Lead Data File Upload (csv or xlsx) - OPTIONAL
        $hasLeadsFile = isset($_FILES['leads_file']) && $_FILES['leads_file']['error'] === UPLOAD_ERR_OK;
        $leadsExt = '';
        if ($hasLeadsFile) {
            $leadsFile = $_FILES['leads_file'];
            $leadsExt = strtolower(pathinfo($leadsFile['name'], PATHINFO_EXTENSION));
            if (!in_array($leadsExt, ['csv', 'xlsx'])) {
                respondError('Only CSV and XLSX lead files are supported.');
            }
        }
        
        try {
            // 3. Generate list_id: LIST_0001, LIST_0002, etc.
            $pdo->beginTransaction();
            
            $stmt = $pdo->query("SELECT list_id FROM lists ORDER BY list_id DESC LIMIT 1");
            $last_id = $stmt->fetchColumn();
            
            $next_num = 1;
            if ($last_id && preg_match('/LIST_(\d+)/', $last_id, $matches)) {
                $next_num = intval($matches[1]) + 1;
            }
            $list_id = sprintf('LIST_%04d', $next_num);
            
            // Create uploads directory
            $audioDir = __DIR__ . '/../uploads/audio/';
            if (!is_dir($audioDir)) {
                mkdir($audioDir, 0755, true);
            }
            
            // Save to temp path first to perform transcoding
            $tempAudioFilename = 'temp_' . $list_id . '.' . $audioExt;
            $tempAudioPath = $audioDir . $tempAudioFilename;
            
            if (!move_uploaded_file($audioFile['tmp_name'], $tempAudioPath)) {
                $pdo->rollBack();
                respondError('Failed to save uploaded audio file.');
            }
            
            // Output audio name
            $audioFilename = $list_id . '.wav';
            $audioPath = $audioDir . $audioFilename;
            
            $converted = transcodeToWav($tempAudioPath, $audioPath);
            if ($converted) {
                @unlink($tempAudioPath); // Clean up temp file
            } else {
                // If transcoding failed, use original upload directly
                $audioFilename = $list_id . '.' . $audioExt;
                $audioPath = $audioDir . $audioFilename;
                rename($tempAudioPath, $audioPath);
            }
            
            // Insert List record
            $insertListStmt = $pdo->prepare("INSERT INTO lists (list_id, name, description, audio_file, start_time, end_time) VALUES (?, ?, ?, ?, ?, ?)");
            $insertListStmt->execute([$list_id, $name, $description, 'uploads/audio/' . $audioFilename, $start_time, $end_time]);
            
            // 4. Parse leads data (only if leads file was provided)
            $rawLeads = [];
            $successCount = 0;
            $failCount = 0;
            
            if ($hasLeadsFile) {
                if ($leadsExt === 'csv') {
                    if (($handle = fopen($leadsFile['tmp_name'], 'r')) !== FALSE) {
                        $header = fgetcsv($handle, 1000, ",");
                        if ($header !== FALSE) {
                            // Detect indices
                            $nameIdx = 0;
                            $phoneIdx = 1;
                            foreach ($header as $i => $h) {
                                $cleanH = strtolower(trim($h));
                                if (strpos($cleanH, 'name') !== false) {
                                    $nameIdx = $i;
                                }
                                if (strpos($cleanH, 'phone') !== false || strpos($cleanH, 'number') !== false) {
                                    $phoneIdx = $i;
                                }
                            }
                            
                            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                                $rawLeads[] = [
                                    'name' => $data[$nameIdx] ?? '',
                                    'phone' => $data[$phoneIdx] ?? ''
                                ];
                            }
                        }
                        fclose($handle);
                    }
                } else if ($leadsExt === 'xlsx') {
                    $rows = parseXLSX($leadsFile['tmp_name']);
                    if ($rows !== false && count($rows) > 0) {
                        $header = $rows[0];
                        $nameIdx = 0;
                        $phoneIdx = 1;
                        foreach ($header as $i => $h) {
                            $cleanH = strtolower(trim($h));
                            if (strpos($cleanH, 'name') !== false) {
                                $nameIdx = $i;
                            }
                            if (strpos($cleanH, 'phone') !== false || strpos($cleanH, 'number') !== false) {
                                $phoneIdx = $i;
                            }
                        }
                        
                        for ($i = 1; $i < count($rows); $i++) {
                            $rawLeads[] = [
                                'name' => $rows[$i][$nameIdx] ?? '',
                                'phone' => $rows[$i][$phoneIdx] ?? ''
                            ];
                        }
                    } else {
                        $pdo->rollBack();
                        respondError('Failed to parse Excel file. Make sure it is not corrupted and has a correct format.');
                    }
                }
                
                // 5. Validate and Insert Leads in chunked transactions
                $seenPhones = []; // Prevent duplicates in the uploaded file itself
                $insertLeadStmt = $pdo->prepare("INSERT INTO leads (list_id, name, phone_number, status) VALUES (?, ?, ?, 'pending')");
                
                $chunkSize = 5000;
                $currentChunkCount = 0;
                
                foreach ($rawLeads as $lead) {
                    $leadName = trim($lead['name']);
                    $leadPhone = preg_replace('/[^0-9]/', '', $lead['phone']);
                    
                    if (empty($leadName)) {
                        $leadName = 'Unknown';
                    }
                    
                    if (strlen($leadPhone) < 7 || strlen($leadPhone) > 15) {
                        $failCount++;
                        continue;
                    }
                    
                    if (isset($seenPhones[$leadPhone])) {
                        $failCount++;
                        continue;
                    }
                    $seenPhones[$leadPhone] = true;
                    
                    $insertLeadStmt->execute([$list_id, $leadName, $leadPhone]);
                    $successCount++;
                    
                    $currentChunkCount++;
                    if ($currentChunkCount >= $chunkSize) {
                        $pdo->commit();
                        $pdo->beginTransaction();
                        $currentChunkCount = 0;
                    }
                }
            }
            
            // Log the upload metrics in api_logs
            $logStmt = $pdo->prepare("INSERT INTO api_logs (list_id, success_count, fail_count) VALUES (?, ?, ?)");
            $logStmt->execute([$list_id, $successCount, $failCount]);
            
            $pdo->commit();
            
            respondSuccess([
                'list_id' => $list_id,
                'total_records' => count($rawLeads),
                'uploaded' => $successCount,
                'failed' => $failCount,
                'message' => $hasLeadsFile 
                    ? "List created successfully. $successCount leads imported, $failCount invalid/duplicate skipped."
                    : "List created successfully with 0 leads. You can push leads using the JSON API."
            ]);
            
        } catch (\Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            respondError('Server error: ' . $e->getMessage());
        }
    }
    
    if ($action === 'delete') {
        $input = json_decode(file_get_contents('php://input'), true);
        $list_id = $input['list_id'] ?? '';
        
        if (empty($list_id)) {
            respondError('List ID is required.');
        }
        
        try {
            // Get audio file path to delete it
            $stmt = $pdo->prepare("SELECT audio_file FROM lists WHERE list_id = ?");
            $stmt->execute([$list_id]);
            $audio_file = $stmt->fetchColumn();
            
            $deleteStmt = $pdo->prepare("DELETE FROM lists WHERE list_id = ?");
            $deleteStmt->execute([$list_id]);
            
            if ($audio_file && file_exists(__DIR__ . '/../' . $audio_file)) {
                unlink(__DIR__ . '/../' . $audio_file);
            }
            
            respondSuccess(['message' => 'List deleted successfully.']);
        } catch (\PDOException $e) {
            respondError('Database error: ' . $e->getMessage());
        }
    }

    if ($action === 'pause') {
        $input = json_decode(file_get_contents('php://input'), true);
        $list_id = $input['list_id'] ?? '';
        
        if (empty($list_id)) {
            respondError('List ID is required.');
        }
        
        try {
            $stmt = $pdo->prepare("UPDATE lists SET is_paused = 1 WHERE list_id = ?");
            $stmt->execute([$list_id]);
            respondSuccess(['message' => 'Campaign paused successfully.']);
        } catch (\PDOException $e) {
            respondError('Database error: ' . $e->getMessage());
        }
    }
    
    if ($action === 'resume') {
        $input = json_decode(file_get_contents('php://input'), true);
        $list_id = $input['list_id'] ?? '';
        
        if (empty($list_id)) {
            respondError('List ID is required.');
        }
        
        try {
            $stmt = $pdo->prepare("UPDATE lists SET is_paused = 0 WHERE list_id = ?");
            $stmt->execute([$list_id]);
            respondSuccess(['message' => 'Campaign resumed successfully.']);
        } catch (\PDOException $e) {
            respondError('Database error: ' . $e->getMessage());
        }
    }
    
    if ($action === 'redial') {
        $input = json_decode(file_get_contents('php://input'), true);
        $list_id = $input['list_id'] ?? '';
        
        if (empty($list_id)) {
            respondError('List ID is required.');
        }
        
        try {
            // Count failed/not_connected calls
            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM leads WHERE list_id = ? AND status IN ('failed', 'not_connected')");
            $countStmt->execute([$list_id]);
            $failedCount = intval($countStmt->fetchColumn());
            
            if ($failedCount === 0) {
                respondSuccess(['message' => 'No failed or not-connected calls found to redial.']);
                exit;
            }
            
            // Revert failed/not connected calls to pending
            $stmt = $pdo->prepare("
                UPDATE leads 
                SET status = 'pending', 
                    duration = 0, 
                    dtmf_response = NULL, 
                    recording_file = NULL, 
                    error_reason = NULL, 
                    dial_time = NULL 
                WHERE list_id = ? AND status IN ('failed', 'not_connected')
            ");
            $stmt->execute([$list_id]);
            
            respondSuccess(['message' => "Successfully spooled $failedCount calls for redialing."]);
        } catch (\PDOException $e) {
            respondError('Database error: ' . $e->getMessage());
        }
    }
}

respondError('Invalid request method or action.');
