<?php
// api/dashboard.php - Real-time Analytics Dashboard Endpoint
require_once __DIR__ . '/../db.php';

// Ensure user is logged in
checkAuth();

$action = $_GET['action'] ?? 'summary';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    // 1. Get summary counts
    if ($action === 'summary') {
        try {
            // Get overall lead stats
            $overallStmt = $pdo->query("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'ringing' THEN 1 ELSE 0 END) as ringing,
                    SUM(CASE WHEN status = 'answered' THEN 1 ELSE 0 END) as answered,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status IN ('failed', 'not_connected') THEN 1 ELSE 0 END) as not_connected,
                    AVG(CASE WHEN status = 'completed' THEN duration ELSE NULL END) as avg_duration,
                    COUNT(CASE WHEN status = 'completed' AND dtmf_response IS NOT NULL AND dtmf_response != '' THEN 1 ELSE NULL END) as dtmf_count,
                    SUM(CASE WHEN status = 'completed' THEN duration ELSE 0 END) as total_seconds
                FROM leads
            ");
            $overall = $overallStmt->fetch();
            
            // Get peak hour of dialing activity
            $peakStmt = $pdo->query("
                SELECT DATE_FORMAT(dial_time, '%H:00') as peak_hour, COUNT(*) as volume 
                FROM leads 
                WHERE dial_time IS NOT NULL
                GROUP BY DATE_FORMAT(dial_time, '%H:00') 
                ORDER BY volume DESC 
                LIMIT 1
            ");
            $peakData = $peakStmt->fetch();
            $peakHourStr = $peakData ? $peakData['peak_hour'] : 'N/A';
            
            // Clean up nulls to 0
            $overall['total'] = intval($overall['total'] ?? 0);
            $overall['pending'] = intval($overall['pending'] ?? 0);
            $overall['ringing'] = intval($overall['ringing'] ?? 0);
            $overall['answered'] = intval($overall['answered'] ?? 0);
            $overall['completed'] = intval($overall['completed'] ?? 0);
            $overall['not_connected'] = intval($overall['not_connected'] ?? 0);
            
            // New KPI Calculations
            $overall['avg_duration'] = round(floatval($overall['avg_duration'] ?? 0), 1);
            $totalDialed = $overall['total'] - $overall['pending'];
            $overall['connection_rate'] = $totalDialed > 0 ? round(($overall['completed'] / $totalDialed) * 100, 1) : 0;
            $overall['dtmf_rate'] = $overall['completed'] > 0 ? round((intval($overall['dtmf_count'] ?? 0) / $overall['completed']) * 100, 1) : 0;
            $overall['total_minutes'] = round(intval($overall['total_seconds'] ?? 0) / 60, 1);
            $overall['peak_hour'] = $peakHourStr;
            
            // Get list-wise summary
            $listwiseStmt = $pdo->query("
                SELECT 
                    l.list_id,
                    l.name,
                    l.is_paused,
                    COUNT(le.id) as total_uploaded,
                    SUM(CASE WHEN le.status != 'pending' THEN 1 ELSE 0 END) as dialed,
                    SUM(CASE WHEN le.status = 'completed' THEN 1 ELSE 0 END) as answered,
                    SUM(CASE WHEN le.status IN ('failed', 'not_connected') THEN 1 ELSE 0 END) as not_connected,
                    SUM(CASE WHEN le.status = 'completed' AND le.dtmf_response IS NOT NULL AND le.dtmf_response != '' THEN 1 ELSE 0 END) as dtmf_responses,
                    l.start_time,
                    l.end_time
                FROM lists l
                LEFT JOIN leads le ON l.list_id = le.list_id
                GROUP BY l.list_id
                ORDER BY l.created_at DESC
            ");
            $listwise = $listwiseStmt->fetchAll();
            
            // Get DTMF Breakdown Analytics
            $dtmfStmt = $pdo->query("
                SELECT dtmf_response, COUNT(*) as count 
                FROM leads 
                WHERE status = 'completed' AND dtmf_response IS NOT NULL AND dtmf_response != ''
                GROUP BY dtmf_response
                ORDER BY dtmf_response ASC
            ");
            $dtmfBreakdown = $dtmfStmt->fetchAll();
            
            // Realtime JSON API push stats
            $apiStatsStmt = $pdo->query("
                SELECT 
                    SUM(success_count) as total_success,
                    SUM(fail_count) as total_fail
                FROM api_logs
            ");
            $apiStats = $apiStatsStmt->fetch();
            
            $apiRecentStmt = $pdo->query("
                SELECT a.*, l.name as list_name 
                FROM api_logs a
                LEFT JOIN lists l ON a.list_id = l.list_id
                ORDER BY a.created_at DESC
                LIMIT 5
            ");
            $apiRecent = $apiRecentStmt->fetchAll();
            
            // Get hourly dialing stats for the last 12 hours
            $hourlyStmt = $pdo->query("
                SELECT 
                    DATE_FORMAT(dial_time, '%H:00') as hour_label,
                    COUNT(*) as dialed,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as connected,
                    SUM(CASE WHEN status IN ('failed', 'not_connected') THEN 1 ELSE 0 END) as not_connected
                FROM leads
                WHERE dial_time >= DATE_SUB(NOW(), INTERVAL 12 HOUR)
                GROUP BY DATE_FORMAT(dial_time, '%H:00')
                ORDER BY MIN(dial_time) ASC
            ");
            $hourlyStats = $hourlyStmt->fetchAll();
            
            respondSuccess([
                'overall' => $overall,
                'listwise' => $listwise,
                'dtmf_breakdown' => $dtmfBreakdown,
                'hourly_stats' => $hourlyStats,
                'api_stats' => [
                    'success' => intval($apiStats['total_success'] ?? 0),
                    'failed' => intval($apiStats['total_fail'] ?? 0),
                    'recent' => $apiRecent
                ]
            ]);
            
        } catch (\PDOException $e) {
            respondError('Database error: ' . $e->getMessage());
        }
    }
    
    // 2. Get detailed drilldown table when clicking on card box
    if ($action === 'details') {
        $type = $_GET['type'] ?? '';
        
        if (empty($type)) {
            respondError('Drilldown type is required.');
        }
        
        try {
            $query = "SELECT name, list_id, phone_number, status, dtmf_response, duration, recording_file, dial_time FROM leads ";
            $params = [];
            
            switch ($type) {
                case 'total':
                    // all leads
                    break;
                case 'pending':
                    $query .= "WHERE status = 'pending'";
                    break;
                case 'ringing':
                    $query .= "WHERE status = 'ringing'";
                    break;
                case 'answered':
                    $query .= "WHERE status = 'answered'";
                    break;
                case 'completed':
                    $query .= "WHERE status = 'completed'";
                    break;
                case 'not_connected':
                    $query .= "WHERE status IN ('failed', 'not_connected')";
                    break;
                case 'campaign_dialed':
                    $query .= "WHERE list_id = ? AND status != 'pending'";
                    $params[] = $_GET['list_id'] ?? '';
                    break;
                case 'dtmf_key':
                    $query .= "WHERE status = 'completed' AND dtmf_response = ?";
                    $params[] = $_GET['dtmf_key'] ?? '';
                    break;
                default:
                    respondError('Invalid drilldown type.');
            }
            
            $query .= " ORDER BY id DESC LIMIT 500"; // cap at 500 for UI responsiveness
            
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $leads = $stmt->fetchAll();
            
            respondSuccess(['leads' => $leads]);
        } catch (\PDOException $e) {
            respondError('Database error: ' . $e->getMessage());
        }
    }
    
    // 3. Export detailed drilldown call logs to CSV/Excel
    if ($action === 'export_details') {
        $type = $_GET['type'] ?? '';
        if (empty($type)) {
            respondError('Type is required for export.');
        }
        
        try {
            $query = "SELECT name, list_id, phone_number, status, dtmf_response, duration, recording_file, dial_time FROM leads ";
            $params = [];
            
            switch ($type) {
                case 'total':
                    break;
                case 'pending':
                    $query .= "WHERE status = 'pending'";
                    break;
                case 'ringing':
                    $query .= "WHERE status = 'ringing'";
                    break;
                case 'answered':
                    $query .= "WHERE status = 'answered'";
                    break;
                case 'completed':
                    $query .= "WHERE status = 'completed'";
                    break;
                case 'not_connected':
                    $query .= "WHERE status IN ('failed', 'not_connected')";
                    break;
                case 'campaign_dialed':
                    $query .= "WHERE list_id = ? AND status != 'pending'";
                    $params[] = $_GET['list_id'] ?? '';
                    break;
                case 'dtmf_key':
                    $query .= "WHERE status = 'completed' AND dtmf_response = ?";
                    $params[] = $_GET['dtmf_key'] ?? '';
                    break;
                default:
                    respondError('Invalid type.');
            }
            
            $query .= " ORDER BY id DESC";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $leads = $stmt->fetchAll();
            
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="voiceblast_' . $type . '_logs.csv"');
            
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Name', 'List ID', 'Phone Number', 'Status', 'DTMF Digit', 'Duration (sec)', 'Recording File', 'Call Time']);
            
            foreach ($leads as $lead) {
                fputcsv($output, [
                    $lead['name'],
                    $lead['list_id'],
                    $lead['phone_number'],
                    $lead['status'],
                    $lead['dtmf_response'] ?? '',
                    $lead['duration'],
                    $lead['recording_file'] ?? '',
                    $lead['dial_time'] ?? ''
                ]);
            }
            fclose($output);
            exit;
            
        } catch (\PDOException $e) {
            respondError('Database error: ' . $e->getMessage());
        }
    }
}

respondError('Invalid request method or action.');
