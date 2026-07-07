<?php
// api/reports.php - Reports and Logs Filtering & Export Endpoint
require_once __DIR__ . '/../db.php';

// Ensure user is logged in
checkAuth();

$action = $_GET['action'] ?? 'search';

if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Parse filters
    $start_date = $_GET['start_date'] ?? $_POST['start_date'] ?? '';
    $end_date = $_GET['end_date'] ?? $_POST['end_date'] ?? '';
    $list_id = $_GET['list_id'] ?? $_POST['list_id'] ?? '';
    $status = $_GET['status'] ?? $_POST['status'] ?? '';
    $dtmf = $_GET['dtmf'] ?? $_POST['dtmf'] ?? '';
    
    // Build query conditions
    $conditions = [];
    $params = [];
    
    // Date filter (defaults to today if not provided)
    if (!empty($start_date)) {
        $conditions[] = "le.created_at >= ?";
        $params[] = $start_date . " 00:00:00";
    }
    if (!empty($end_date)) {
        $conditions[] = "le.created_at <= ?";
        $params[] = $end_date . " 23:59:59";
    }
    
    // List filter
    if (!empty($list_id) && $list_id !== 'all') {
        $conditions[] = "le.list_id = ?";
        $params[] = $list_id;
    }
    
    // Status filter
    if (!empty($status) && $status !== 'all') {
        if ($status === 'connected') {
            $conditions[] = "le.status = 'completed'";
        } else if ($status === 'not_connected') {
            $conditions[] = "le.status IN ('failed', 'not_connected')";
        } else {
            $conditions[] = "le.status = ?";
            $params[] = $status;
        }
    }
    
    // DTMF filter
    if (!empty($dtmf) && $dtmf !== 'all') {
        if ($dtmf === 'any') {
            $conditions[] = "le.dtmf_response IS NOT NULL AND le.dtmf_response != ''";
        } else if ($dtmf === 'none') {
            $conditions[] = "(le.dtmf_response IS NULL OR le.dtmf_response = '')";
        } else {
            $conditions[] = "le.dtmf_response = ?";
            $params[] = $dtmf;
        }
    }
    
    $whereSql = "";
    if (count($conditions) > 0) {
        $whereSql = "WHERE " . implode(" AND ", $conditions);
    }
    
    // Search action (returns JSON)
    if ($action === 'search') {
        try {
            // Count total matching
            $countQuery = "SELECT COUNT(*) FROM leads le $whereSql";
            $countStmt = $pdo->prepare($countQuery);
            $countStmt->execute($params);
            $totalRecords = intval($countStmt->fetchColumn());
            
            // Get records (limit to 500 for display in preview grid)
            $query = "
                SELECT le.*, l.name as list_name 
                FROM leads le
                LEFT JOIN lists l ON le.list_id = l.list_id
                $whereSql
                ORDER BY le.id DESC
                LIMIT 500
            ";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $records = $stmt->fetchAll();
            
            respondSuccess([
                'total' => $totalRecords,
                'records' => $records
            ]);
            
        } catch (\PDOException $e) {
            respondError('Database error: ' . $e->getMessage());
        }
    }
    
    // Export action (returns CSV file download)
    if ($action === 'export') {
        try {
            $query = "
                SELECT le.created_at, le.list_id, l.name as list_name, le.name, le.phone_number, le.status, le.dtmf_response, le.duration, le.recording_file, le.dial_time
                FROM leads le
                LEFT JOIN lists l ON le.list_id = l.list_id
                $whereSql
                ORDER BY le.id DESC
            ";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            
            // Send CSV headers
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="voiceblast_reports_' . date('Ymd_His') . '.csv"');
            
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Date Created', 'List ID', 'List Name', 'Name', 'Phone Number', 'Status', 'Dial Time', 'Duration (sec)', 'DTMF Capture', 'Recording File']);
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, [
                    $row['created_at'],
                    $row['list_id'],
                    $row['list_name'],
                    $row['name'],
                    $row['phone_number'],
                    $row['status'],
                    $row['dial_time'] ?? 'Not Dialed',
                    $row['duration'],
                    $row['dtmf_response'] ?? '',
                    $row['recording_file'] ?? ''
                ]);
            }
            fclose($output);
            exit;
            
        } catch (\PDOException $e) {
            // Since header might already be sent or is expecting CSV, it's safer to output error directly or fall back
            echo "Error generating export: " . $e->getMessage();
            exit;
        }
    }

    // 3. IVR Ratings Search Action
    if ($action === 'ivr_ratings') {
        try {
            $rating_filter = $_GET['rating'] ?? $_POST['rating'] ?? '';
            $search_val = $_GET['search'] ?? $_POST['search'] ?? '';
            
            $conditions = [];
            $params = [];
            
            if (!empty($start_date)) {
                $conditions[] = "created_at >= ?";
                $params[] = $start_date . " 00:00:00";
            }
            if (!empty($end_date)) {
                $conditions[] = "created_at <= ?";
                $params[] = $end_date . " 23:59:59";
            }
            if (!empty($rating_filter) && $rating_filter !== 'all') {
                $conditions[] = "rating = ?";
                $params[] = $rating_filter;
            }
            if (!empty($search_val)) {
                $conditions[] = "(calledid LIKE ? OR callerid LIKE ? OR ivr_name LIKE ?)";
                $like = "%" . $search_val . "%";
                $params[] = $like;
                $params[] = $like;
                $params[] = $like;
            }
            
            $whereSql = "";
            if (count($conditions) > 0) {
                $whereSql = "WHERE " . implode(" AND ", $conditions);
            }
            
            // Count total matching
            $countQuery = "SELECT COUNT(*) FROM ivr_ratings $whereSql";
            $countStmt = $pdo->prepare($countQuery);
            $countStmt->execute($params);
            $totalRecords = intval($countStmt->fetchColumn());
            
            // Get records
            $query = "SELECT * FROM ivr_ratings $whereSql ORDER BY id DESC LIMIT 500";
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $records = $stmt->fetchAll();
            
            respondSuccess([
                'total' => $totalRecords,
                'records' => $records
            ]);
        } catch (\PDOException $e) {
            respondError('Database error: ' . $e->getMessage());
        }
    }

    // 4. IVR Ratings Export Action
    if ($action === 'export_ivr_ratings') {
        try {
            $rating_filter = $_GET['rating'] ?? $_POST['rating'] ?? '';
            $search_val = $_GET['search'] ?? $_POST['search'] ?? '';
            
            $conditions = [];
            $params = [];
            
            if (!empty($start_date)) {
                $conditions[] = "created_at >= ?";
                $params[] = $start_date . " 00:00:00";
            }
            if (!empty($end_date)) {
                $conditions[] = "created_at <= ?";
                $params[] = $end_date . " 23:59:59";
            }
            if (!empty($rating_filter) && $rating_filter !== 'all') {
                $conditions[] = "rating = ?";
                $params[] = $rating_filter;
            }
            if (!empty($search_val)) {
                $conditions[] = "(calledid LIKE ? OR callerid LIKE ? OR ivr_name LIKE ?)";
                $like = "%" . $search_val . "%";
                $params[] = $like;
                $params[] = $like;
                $params[] = $like;
            }
            
            $whereSql = "";
            if (count($conditions) > 0) {
                $whereSql = "WHERE " . implode(" AND ", $conditions);
            }
            
            $query = "SELECT created_at, calledid, callerid, ivr_name, uniq, channel, rating FROM ivr_ratings $whereSql ORDER BY id DESC";
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="ivr_ratings_' . date('Ymd_His') . '.csv"');
            
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Date Created', 'Called ID (Customer)', 'Caller ID (DID)', 'IVR Campaign', 'Call Unique ID', 'Channel', 'Customer Rating']);
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, [
                    $row['created_at'],
                    $row['calledid'],
                    $row['callerid'],
                    $row['ivr_name'],
                    $row['uniq'],
                    $row['channel'],
                    $row['rating']
                ]);
            }
            fclose($output);
            exit;
        } catch (\PDOException $e) {
            echo "Error generating IVR Ratings export: " . $e->getMessage();
            exit;
        }
    }
}

respondError('Invalid request method or action.');
