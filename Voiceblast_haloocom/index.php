<?php
// index.php - Primary entry point for Haloocom Voiceblast
require_once __DIR__ . '/db.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$username = $_SESSION['username'] ?? '';
$displayName = $_SESSION['name'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Haloocom Voiceblast Platform</title>
    <!-- Google Fonts: Inter for general UI, Outfit for headers -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome for Premium Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js for beautiful graphs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="dark-theme">

    <?php if (!$isLoggedIn): ?>
    <!-- ================= AUTHENTICATION / LOGIN SCREEN ================= -->
    <div class="auth-container">
        <div class="auth-wrapper">
            <!-- Left Panel: Branding & Analytics -->
            <div class="auth-left-panel">
                <div class="auth-left-content">
                    <span class="auth-promo-badge"><i class="fa fa-circle-nodes"></i> Haloocom Enterprise Dialer</span>
                    <h1 class="auth-promo-title">Voiceblast Platform</h1>
                    <p class="auth-promo-desc">
                        Deliver high-volume pre-recorded voice campaigns and gather customer feedback in real-time with automated DTMF response analysis.
                    </p>
                    
                    <div class="auth-hero-container">
                        <img src="voiceblast_hero.png" alt="Voiceblast Analytics" class="auth-hero-img">
                    </div>
                    
                    <div class="auth-features-list">
                        <div class="auth-feature-item">
                            <span class="feature-icon"><i class="fa fa-phone-volume"></i></span>
                            <div class="feature-text">
                                <h3>Automated Outbound Broadcasts</h3>
                                <p>Deliver promotional messages, announcements, or notifications to thousands of contacts simultaneously.</p>
                            </div>
                        </div>
                        <div class="auth-feature-item">
                            <span class="feature-icon"><i class="fa fa-keyboard"></i></span>
                            <div class="feature-text">
                                <h3>Interactive Keypad Responses</h3>
                                <p>Configure IVR survey menus to capture instant customer DTMF feedback rating scores.</p>
                            </div>
                        </div>
                        <div class="auth-feature-item">
                            <span class="feature-icon"><i class="fa fa-chart-line"></i></span>
                            <div class="feature-text">
                                <h3>Real-time Activity Tracking</h3>
                                <p>Monitor active answered lines, call logs, recording playbacks, and campaign progress logs.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Panel: Login Form -->
            <div class="auth-right-panel">
                <!-- Theme Switcher option on Login page -->
                <div class="auth-theme-toggle-wrapper">
                    <button id="btn-theme-toggle" title="Switch Theme" class="btn-icon-theme">
                        <i class="fa fa-moon"></i>
                    </button>
                </div>
                
                <div class="auth-card-inner">
                    <div class="auth-header">
                        <img src="logo.png" alt="Haloocom Logo" class="auth-logo">
                        <h2>Welcome Back</h2>
                        <p>Enter credentials to access the dialer dashboard</p>
                    </div>
                    
                    <div id="login-error-msg" class="alert alert-danger hidden"></div>
                    
                    <form id="login-form">
                        <div class="form-group">
                            <label for="username"><i class="fa fa-user"></i> Username</label>
                            <input type="text" id="username" name="username" placeholder="e.g. admin" required autocomplete="username">
                        </div>
                        
                        <div class="form-group">
                            <label for="password"><i class="fa fa-lock"></i> Password</label>
                            <input type="password" id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">
                            <span>Login</span> <i class="fa fa-sign-in-alt"></i>
                        </button>
                    </form>
                    
                    <div class="auth-footer">
                        <p>© 2026. All rights reserved by Haloocom.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php else: ?>
    <!-- ================= MAIN APP DASHBOARD INTERFACE ================= -->
    <div class="app-layout">
        
        <!-- SIDEBAR NAVIGATION -->
        <aside class="app-sidebar glass-panel">
            <div class="sidebar-brand">
                <img src="logo.png" alt="Haloocom Logo" class="brand-logo-centered">
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li class="nav-item active" data-target="section-dashboard">
                        <a href="#dashboard"><i class="fa fa-tachometer-alt"></i> <span>Dashboard</span></a>
                    </li>
                    <li class="nav-item" data-target="section-lists">
                        <a href="#lists"><i class="fa fa-list"></i> <span>List Manager</span></a>
                    </li>
                    <li class="nav-item" data-target="section-reports">
                        <a href="#reports"><i class="fa fa-chart-bar"></i> <span>Call Reports</span></a>
                    </li>
                    <li class="nav-item" data-target="section-users">
                        <a href="#users"><i class="fa fa-users"></i> <span>User Management</span></a>
                    </li>
                </ul>
            </nav>
            
            <div class="sidebar-user">
                <div class="user-avatar">
                    <i class="fa fa-user-shield"></i>
                </div>
                <div class="user-info">
                    <span class="user-name"><?php echo htmlspecialchars($displayName); ?></span>
                    <span class="user-role">@<?php echo htmlspecialchars($username); ?></span>
                </div>
                <button id="btn-theme-toggle" title="Switch Theme" class="btn-icon-theme">
                    <i class="fa fa-moon"></i>
                </button>
                <button id="btn-logout" title="Log Out" class="btn-icon-logout">
                    <i class="fa fa-sign-out-alt"></i>
                </button>
            </div>
        </aside>
        
        <!-- MAIN CONTENT PANEL -->
        <main class="app-content">
            
            <!-- HEADER -->
            <header class="content-header">
                <div class="header-title">
                    <h2 id="current-page-title">Dashboard</h2>
                    <p id="current-page-desc">Real-time dialer performance and live statistics</p>
                </div>
                <div class="header-status">
                    <div id="dialer-status-badge" class="status-badge status-active">
                        <span class="pulse-ring"></span>
                        <span class="badge-text">Dialer Core Online</span>
                    </div>
                    <div class="server-time">
                        <i class="fa fa-clock"></i> <span id="clock-display">00:00:00</span>
                    </div>
                </div>
            </header>
            
            <!-- CONTENT SECTIONS -->
            <div class="sections-wrapper">
                
                <!-- SECTION 1: DASHBOARD -->
                <section id="section-dashboard" class="content-section">
                    <!-- Metrics Card Row -->
                    <div class="metrics-grid">
                        <div class="metric-card glass-panel border-blue clickable" data-drilldown="total">
                            <div class="metric-icon icon-blue"><i class="fa fa-users"></i></div>
                            <div class="metric-info">
                                <h3>Total Leads</h3>
                                <span id="stat-total" class="metric-value">0</span>
                            </div>
                        </div>
                        <div class="metric-card glass-panel border-grey clickable" data-drilldown="pending">
                            <div class="metric-icon icon-grey"><i class="fa fa-clock"></i></div>
                            <div class="metric-info">
                                <h3>Yet to Dial</h3>
                                <span id="stat-pending" class="metric-value">0</span>
                            </div>
                        </div>
                        <div class="metric-card glass-panel border-orange clickable" data-drilldown="ringing">
                            <div class="metric-icon icon-orange pulse-orange"><i class="fa fa-phone-volume"></i></div>
                            <div class="metric-info">
                                <h3>Ringing (Live)</h3>
                                <span id="stat-ringing" class="metric-value">0</span>
                            </div>
                        </div>
                        <div class="metric-card glass-panel border-green clickable" data-drilldown="answered">
                            <div class="metric-icon icon-green pulse-green"><i class="fa fa-headset"></i></div>
                            <div class="metric-info">
                                <h3>On Call (Live)</h3>
                                <span id="stat-answered" class="metric-value">0</span>
                            </div>
                        </div>
                        <div class="metric-card glass-panel border-cyan clickable" data-drilldown="completed">
                            <div class="metric-icon icon-cyan"><i class="fa fa-phone-slash"></i></div>
                            <div class="metric-info">
                                <h3>Connected</h3>
                                <span id="stat-completed" class="metric-value">0</span>
                            </div>
                        </div>
                        <div class="metric-card glass-panel border-red clickable" data-drilldown="not_connected">
                            <div class="metric-icon icon-red"><i class="fa fa-phone-xmark"></i></div>
                            <div class="metric-info">
                                <h3>Not Connected</h3>
                                <span id="stat-not_connected" class="metric-value">0</span>
                            </div>
                        </div>
                        <div class="metric-card glass-panel border-purple">
                            <div class="metric-icon icon-purple"><i class="fa fa-hourglass-half"></i></div>
                            <div class="metric-info">
                                <h3>Avg Duration</h3>
                                <span id="stat-avg-duration" class="metric-value">0s</span>
                            </div>
                        </div>
                        <div class="metric-card glass-panel border-cyan">
                            <div class="metric-icon icon-cyan"><i class="fa fa-signal"></i></div>
                            <div class="metric-info">
                                <h3>Connect Rate</h3>
                                <span id="stat-connect-rate" class="metric-value">0%</span>
                            </div>
                        </div>
                        <div class="metric-card glass-panel border-orange">
                            <div class="metric-icon icon-orange"><i class="fa fa-hand-pointer"></i></div>
                            <div class="metric-info">
                                <h3>DTMF Response</h3>
                                <span id="stat-dtmf-rate" class="metric-value">0%</span>
                            </div>
                        </div>
                        <div class="metric-card glass-panel border-purple">
                            <div class="metric-icon icon-purple"><i class="fa fa-phone-volume"></i></div>
                            <div class="metric-info">
                                <h3>Total Talktime</h3>
                                <span id="stat-total-talktime" class="metric-value">0 mins</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dashboard Visual Analytics Grid -->
                    <div class="dashboard-grid">
                        
                        <!-- List-wise Campaign Monitor -->
                        <div class="dashboard-panel glass-panel span-8">
                            <div class="panel-header">
                                <h3><i class="fa fa-chart-line"></i> List Progress Tracker</h3>
                                <span class="refresh-indicator"><i class="fa fa-sync fa-spin"></i> Polling</span>
                            </div>
                            <div class="panel-body">
                                <div class="table-container">
                                    <table id="tbl-dashboard-lists">
                                        <thead>
                                             <tr>
                                                 <th>List ID</th>
                                                 <th>List Name</th>
                                                 <th>Uploaded</th>
                                                 <th>Dialed</th>
                                                 <th>Answered</th>
                                                 <th>Not Connected</th>
                                                 <th>DTMF Hits</th>
                                                 <th>Progress</th>
                                                 <th>Status</th>
                                                 <th>Actions</th>
                                             </tr>
                                         </thead>
                                         <tbody>
                                             <!-- Loaded via AJAX -->
                                             <tr>
                                                 <td colspan="10" class="text-center text-muted">No campaigns running...</td>
                                             </tr>
                                         </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Real-time JSON API Logs -->
                        <div class="dashboard-panel glass-panel span-4">
                            <div class="panel-header">
                                <h3><i class="fa fa-bolt"></i> Realtime API Tracker</h3>
                            </div>
                            <div class="panel-body">
                                <div class="api-realtime-card">
                                    <div class="api-stat-row">
                                        <div class="api-stat-box success">
                                            <span class="label">API Uploaded</span>
                                            <span id="api-success-count" class="val">0</span>
                                        </div>
                                        <div class="api-stat-box failed">
                                            <span class="label">API Rejected</span>
                                            <span id="api-fail-count" class="val">0</span>
                                        </div>
                                    </div>
                                    
                                    <h4 class="sub-title">Recent API Pushes</h4>
                                    <ul id="api-log-list" class="api-log-list">
                                        <li class="text-muted text-center py-3">No API payloads pushed yet.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hourly Performance Graph -->
                        <div class="dashboard-panel glass-panel span-8">
                            <div class="panel-header">
                                <h3><i class="fa fa-chart-area"></i> Hourly Dialing Performance</h3>
                                <span class="panel-subtitle">Outbound activity for the last 12 hours</span>
                             </div>
                             <div class="panel-body">
                                 <div class="chart-container" style="position: relative; height: 260px; width: 100%;">
                                     <canvas id="chart-hourly"></canvas>
                                 </div>
                             </div>
                        </div>

                        <!-- Dialer Productivity & Health Summary -->
                        <div class="dashboard-panel glass-panel span-4">
                            <div class="panel-header">
                                <h3><i class="fa fa-chart-pie"></i> Productivity Summary</h3>
                            </div>
                            <div class="panel-body">
                                <div class="productivity-list">
                                    <div class="productivity-item">
                                        <div class="productivity-icon icon-blue"><i class="fa fa-bolt"></i></div>
                                        <div class="productivity-info">
                                            <div class="productivity-title">Peak Outbound Hour</div>
                                            <div id="stat-peak-hour" class="productivity-value">N/A</div>
                                        </div>
                                    </div>
                                    <div class="productivity-item">
                                        <div class="productivity-icon icon-green"><i class="fa fa-comments"></i></div>
                                        <div class="productivity-info">
                                            <div class="productivity-title">Talk Time (Minutes)</div>
                                            <div id="stat-total-minutes" class="productivity-value">0.0 mins</div>
                                        </div>
                                    </div>
                                    <div class="productivity-item">
                                        <div class="productivity-icon icon-purple"><i class="fa fa-users-viewfinder"></i></div>
                                        <div class="productivity-info">
                                            <div class="productivity-title">Contactability Index</div>
                                            <div id="stat-contact-index" class="productivity-value">Low</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- DTMF Breakdown Analytics -->
                        <div class="dashboard-panel glass-panel span-12">
                            <div class="panel-header">
                                <h3><i class="fa fa-th"></i> DTMF Voice Blast Analytics</h3>
                                <span class="panel-subtitle">Distribution of digit selections by customers</span>
                            </div>
                            <div class="panel-body">
                                <div class="dtmf-distribution-container">
                                    <div class="dtmf-keypad-grid">
                                        <!-- Keys 1-9, *, 0, # -->
                                        <?php 
                                        $keys = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '*', '0', '#'];
                                        foreach ($keys as $key):
                                        ?>
                                        <div class="dtmf-keycap" id="dtmf-key-<?php echo str_replace(['*', '#'], ['star', 'hash'], $key); ?>">
                                            <span class="key-label"><?php echo $key; ?></span>
                                            <span class="key-count">0 hits</span>
                                            <div class="key-progress-bar"><div class="key-progress-fill" style="width: 0%"></div></div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                
                <!-- SECTION 2: LIST MANAGER -->
                <section id="section-lists" class="content-section hidden">
                    <div class="dashboard-grid">
                        
                        <!-- Create Campaign List Form -->
                        <div class="dashboard-panel glass-panel span-6">
                            <div class="panel-header">
                                <h3><i class="fa fa-folder-plus"></i> Create Voiceblast List</h3>
                            </div>
                            <div class="panel-body">
                                <div id="list-msg" class="alert hidden"></div>
                                <form id="list-create-form" enctype="multipart/form-data">
                                    <div class="form-row">
                                        <div class="form-group span-6">
                                            <label for="list_name">Campaign Name</label>
                                            <input type="text" id="list_name" name="name" placeholder="e.g. Promo Blast July" required>
                                        </div>
                                        <div class="form-group span-6">
                                            <label for="list_desc">Description</label>
                                            <input type="text" id="list_desc" name="description" placeholder="Brief campaign context">
                                        </div>
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group span-6">
                                            <label for="start_time">Start Date & Time</label>
                                            <input type="datetime-local" id="start_time" name="start_time" required>
                                        </div>
                                        <div class="form-group span-6">
                                            <label for="end_time">End Date & Time</label>
                                            <input type="datetime-local" id="end_time" name="end_time" required>
                                        </div>
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group span-6">
                                            <label for="audio_file">Upload Audio Announcement</label>
                                            <div class="file-upload-wrapper">
                                                <input type="file" id="audio_file" name="audio" accept=".mp3,.wav" required>
                                                <div class="upload-trigger">
                                                    <i class="fa fa-volume-up"></i>
                                                    <span>Choose WAV or MP3</span>
                                                </div>
                                            </div>
                                            <div id="audio-preview-container" class="hidden" style="margin-top: 10px;">
                                                <audio id="audio-upload-preview" controls style="width: 100%; height: 32px;"></audio>
                                            </div>
                                        </div>
                                        <div class="form-group span-6">
                                            <label for="leads_file">Upload Lead Contacts File (Optional)</label>
                                            <div class="file-upload-wrapper">
                                                <input type="file" id="leads_file" name="leads_file" accept=".csv,.xlsx">
                                                <div class="upload-trigger">
                                                    <i class="fa fa-file-excel"></i>
                                                    <span>Choose XLSX or CSV</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-tip">
                                        <i class="fa fa-info-circle"></i> Leads file must have columns containing <strong>Name</strong> and <strong>Phone Number</strong> (e.g. 10 digits).
                                    </div>
                                    
                                    <button type="submit" class="btn btn-success mt-3" id="btn-submit-list">
                                        <span>Create List & Import</span> <i class="fa fa-cloud-upload-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- JSON API Documentation -->
                        <div class="dashboard-panel glass-panel span-6">
                            <div class="panel-header">
                                <h3><i class="fa fa-code"></i> JSON Lead Push API Integration</h3>
                            </div>
                            <div class="panel-body">
                                <div class="api-docs">
                                    <p>Pushed data automatically displays in real-time. Use this endpoint for automated CRM integrations:</p>
                                    <div class="api-endpoint-badge">
                                        <span class="method">POST</span>
                                        <span class="url" id="api-endpoint-url">http://<?php echo $_SERVER['HTTP_HOST']; ?>/api/push.php?list_id=LIST_XXXX</span>
                                    </div>
                                    
                                    <h4 class="mt-3">Request Payload (JSON Array)</h4>
                                    <pre class="code-block"><code>[
  {
    "name": "John Doe",
    "phone_number": "9876543210"
  },
  {
    "name": "Jane Smith",
    "phone_number": "9123456789"
  }
]</code></pre>
                                    
                                    <h4 class="mt-3">Response Payload</h4>
                                    <pre class="code-block"><code>{
  "status": "success",
  "list_id": "LIST_0001",
  "total_received": 2,
  "success_count": 2,
  "fail_count": 0,
  "message": "Successfully processed 2 leads, 0 failed..."
}</code></pre>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Active Campaign Lists Table -->
                        <div class="dashboard-panel glass-panel span-12">
                            <div class="panel-header">
                                <h3><i class="fa fa-folder-open"></i> Existing Campaign Lists</h3>
                            </div>
                            <div class="panel-body">
                                <div class="table-container">
                                    <table id="tbl-lists-manager">
                                        <thead>
                                            <tr>
                                                <th>List ID</th>
                                                <th>Campaign Name</th>
                                                <th>Description</th>
                                                <th>Audio File</th>
                                                <th>Total Leads</th>
                                                <th>Dialed Progress</th>
                                                <th>Start Date/Time</th>
                                                <th>End Date/Time</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Dynamically populated -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </section>
                
                <!-- SECTION 3: REPORTS -->
                <section id="section-reports" class="content-section hidden">
                    <!-- Search Filter Box -->
                    <div class="glass-panel filter-panel mb-4">
                        <form id="report-filter-form" class="filter-row">
                            <div class="form-group col-fit">
                                <label for="filter_start_date">Start Date</label>
                                <input type="date" id="filter_start_date" name="start_date">
                            </div>
                            <div class="form-group col-fit">
                                <label for="filter_end_date">End Date</label>
                                <input type="date" id="filter_end_date" name="end_date">
                            </div>
                            <div class="form-group col-fit">
                                <label for="filter_list_id">Select List ID</label>
                                <select id="filter_list_id" name="list_id">
                                    <option value="all">-- All Campaigns --</option>
                                    <!-- Dynamic -->
                                </select>
                            </div>
                            <div class="form-group col-fit">
                                <label for="filter_status">Status</label>
                                <select id="filter_status" name="status">
                                    <option value="all">-- All Calls --</option>
                                    <option value="connected">Connected (Answered)</option>
                                    <option value="not_connected">Not Connected</option>
                                    <option value="pending">Yet to Dial</option>
                                    <option value="ringing">Ringing (Active)</option>
                                    <option value="answered">On Call (Active)</option>
                                </select>
                            </div>
                            <div class="form-group col-fit">
                                <label for="filter_dtmf">DTMF Selection</label>
                                <select id="filter_dtmf" name="dtmf">
                                    <option value="all">-- All Keys --</option>
                                    <option value="any">Any Keypress Response</option>
                                    <option value="none">No Keypress Response</option>
                                    <option value="0">Key 0</option>
                                    <option value="1">Key 1</option>
                                    <option value="2">Key 2</option>
                                    <option value="3">Key 3</option>
                                    <option value="4">Key 4</option>
                                    <option value="5">Key 5</option>
                                    <option value="6">Key 6</option>
                                    <option value="7">Key 7</option>
                                    <option value="8">Key 8</option>
                                    <option value="9">Key 9</option>
                                    <option value="*">Key *</option>
                                    <option value="#">Key #</option>
                                </select>
                            </div>
                            
                            <div class="filter-actions col-fit">
                                <button type="submit" class="btn btn-primary" id="btn-search-reports">
                                    <i class="fa fa-filter"></i> Search
                                </button>
                                <button type="button" class="btn btn-success-outline" id="btn-export-reports">
                                    <i class="fa fa-file-excel"></i> Export Excel
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Call Logs Grid -->
                    <div class="dashboard-panel glass-panel">
                        <div class="panel-header">
                            <h3><i class="fa fa-history"></i> Detailed Call Logs Preview</h3>
                            <span id="reports-count-badge" class="badge-blue">0 records matched</span>
                        </div>
                        <div class="panel-body">
                            <div class="table-container">
                                <table id="tbl-reports-logs">
                                    <thead>
                                        <tr>
                                            <th>Date & Time</th>
                                            <th>List ID</th>
                                            <th>List Name</th>
                                            <th>Name</th>
                                            <th>Phone Number</th>
                                            <th>Status</th>
                                            <th>Duration (hh:mm:ss)</th>
                                            <th>DTMF Input</th>
                                            <th>Call Recording</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">Use search filters above to load reports...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
                
                <!-- SECTION 4: USER MANAGEMENT -->
                <section id="section-users" class="content-section hidden">
                    <div class="dashboard-grid">
                        
                        <!-- Create User Card -->
                        <div class="dashboard-panel glass-panel span-4">
                            <div class="panel-header">
                                <h3><i class="fa fa-user-plus"></i> Create Admin Account</h3>
                            </div>
                            <div class="panel-body">
                                <div id="user-msg" class="alert hidden"></div>
                                <form id="user-create-form">
                                    <div class="form-group">
                                        <label for="new_username">Login Username</label>
                                        <input type="text" id="new_username" placeholder="e.g. operator1" required autocomplete="username">
                                    </div>
                                    <div class="form-group">
                                        <label for="new_name">Display Name</label>
                                        <input type="text" id="new_name" placeholder="e.g. John Doe" required autocomplete="name">
                                    </div>
                                    <div class="form-group">
                                        <label for="new_password">Password</label>
                                        <input type="password" id="new_password" placeholder="Min 6 characters" required autocomplete="new-password">
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block mt-3">
                                        <span>Register User</span> <i class="fa fa-user-check"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Users List Table -->
                        <div class="dashboard-panel glass-panel span-8">
                            <div class="panel-header">
                                <h3><i class="fa fa-users-cog"></i> Active Platform Operators</h3>
                            </div>
                            <div class="panel-body">
                                <div class="table-container">
                                    <table id="tbl-users-list">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Username</th>
                                                <th>Display Name</th>
                                                <th>Created Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Dynamic via AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </section>
            </div>
            
            <!-- GLOBAL FOOTER -->
            <footer class="app-footer">
                <p>© 2026. All rights reserved by Haloocom.</p>
            </footer>
        </main>
        
        <!-- ================= STATS DRILLDOWN MODAL DIALOG ================= -->
        <div id="modal-drilldown" class="modal-backdrop hidden">
            <div class="modal-card glass-panel animate-zoom">
                <div class="modal-header">
                    <h3 id="modal-title">Lead Contacts List</h3>
                    <button class="modal-close-btn" id="btn-close-modal"><i class="fa fa-times"></i></button>
                </div>
                <div class="modal-filter-row">
                    <div class="search-box">
                        <i class="fa fa-search"></i>
                        <input type="text" id="modal-search-input" placeholder="Search by name, phone or list id...">
                    </div>
                    <button class="btn btn-success-outline" id="btn-modal-export">
                        <i class="fa fa-file-excel"></i> Export logs
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-container">
                        <table id="tbl-modal-details">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>List ID</th>
                                    <th>Phone Number</th>
                                    <th>Call Status</th>
                                    <th>Dialed Date/Time</th>
                                    <th>Duration</th>
                                    <th>DTMF Response</th>
                                    <th>Recording</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dynamic -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    <?php endif; ?>

    <!-- AJAX Dashboard Core Logic -->
    <script src="dashboard.js"></script>
</body>
</html>
