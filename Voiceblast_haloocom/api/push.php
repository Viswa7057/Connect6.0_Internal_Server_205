<?php
// api/push.php - JSON API for Pushing Leads
require_once __DIR__ . '/../db.php';

// JSON API endpoint is public but should be secured with a token, or authenticated.
// For the sake of this product, we will allow pushing data. We can check list_id validity.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondError('Invalid request method. Only POST is allowed.', 405);
}

// Set longer execution time and memory limit for large uploads
ini_set('max_execution_time', 600); // 10 minutes
ini_set('memory_limit', '512M');

// Get raw POST content
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    respondError('Invalid JSON payload: ' . json_last_error_msg());
}

// The input could be a single object or an array of objects
$leadsToProcess = [];
if (isset($input['phone_number']) || isset($input['phone'])) {
    // Single object
    $leadsToProcess[] = $input;
} else if (is_array($input)) {
    // Array of objects
    $leadsToProcess = $input;
} else {
    respondError('JSON payload must be a lead object or an array of lead objects.');
}

if (count($leadsToProcess) === 0) {
    respondError('No lead records found in the payload.');
}

// 1. Resolve and validate List ID
// Find the list ID from the first element or check if it's passed as a query parameter/payload field
$list_id = trim($_GET['list_id'] ?? $input['list_id'] ?? $leadsToProcess[0]['list_id'] ?? '');

if (empty($list_id)) {
    respondError('list_id parameter is required.');
}

try {
    // Check if the list exists
    $listStmt = $pdo->prepare("SELECT COUNT(*) FROM lists WHERE list_id = ?");
    $listStmt->execute([$list_id]);
    if ($listStmt->fetchColumn() == 0) {
        respondError("List with ID '$list_id' does not exist.");
    }
    
    // We will batch insert leads to avoid memory and performance bottlenecks
    $pdo->beginTransaction();
    
    // Get existing phone numbers for this list to avoid duplicates
    $existingPhones = [];
    $phoneStmt = $pdo->prepare("SELECT phone_number FROM leads WHERE list_id = ?");
    $phoneStmt->execute([$list_id]);
    while ($p = $phoneStmt->fetchColumn()) {
        $existingPhones[$p] = true;
    }
    
    $successCount = 0;
    $failCount = 0;
    $seenInBatch = [];
    
    $insertLeadStmt = $pdo->prepare("INSERT INTO leads (list_id, name, phone_number, status) VALUES (?, ?, ?, 'pending')");
    
    $chunkSize = 5000;
    $currentChunkCount = 0;
    
    foreach ($leadsToProcess as $item) {
        $name = trim($item['name'] ?? $item['name_number'] ?? 'API Lead');
        $phone = preg_replace('/[^0-9]/', '', $item['phone_number'] ?? $item['phone'] ?? '');
        
        if (empty($name)) {
            $name = 'API Lead';
        }
        
        // Validation: phone number length 7 to 15 digits
        if (strlen($phone) < 7 || strlen($phone) > 15) {
            $failCount++;
            continue;
        }
        
        // Skip duplicate in the same payload batch
        if (isset($seenInBatch[$phone])) {
            $failCount++;
            continue;
        }
        $seenInBatch[$phone] = true;
        
        // Skip duplicate in the database for this list
        if (isset($existingPhones[$phone])) {
            $failCount++;
            continue;
        }
        
        $insertLeadStmt->execute([$list_id, $name, $phone]);
        $successCount++;
        
        $currentChunkCount++;
        if ($currentChunkCount >= $chunkSize) {
            $pdo->commit();
            $pdo->beginTransaction();
            $currentChunkCount = 0;
        }
    }
    
    // Log the API push event for real-time dashboard monitoring
    $logStmt = $pdo->prepare("INSERT INTO api_logs (list_id, success_count, fail_count) VALUES (?, ?, ?)");
    $logStmt->execute([$list_id, $successCount, $failCount]);
    
    $pdo->commit();
    
    respondSuccess([
        'list_id' => $list_id,
        'total_received' => count($leadsToProcess),
        'success_count' => $successCount,
        'fail_count' => $failCount,
        'message' => "Successfully processed $successCount leads, $failCount failed/duplicates skipped."
    ]);
    
} catch (\Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    respondError('Database error processing API request: ' . $e->getMessage(), 500);
}
