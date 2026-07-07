// dashboard.js - Frontend Controller for Haloocom Voiceblast

document.addEventListener('DOMContentLoaded', function() {
    
    // ================= GLOBAL STATE & ROUTING =================
    let hourlyChart = null;
    const AppState = {
        activeSection: 'section-dashboard',
        pollingInterval: null,
        currentDrilldownType: 'total',
        drilldownLeads: []
    };

    // Initialize clock in header
    function updateClock() {
        const clockEl = document.getElementById('clock-display');
        if (clockEl) {
            const now = new Date();
            clockEl.textContent = now.toLocaleTimeString();
        }
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Theme Switcher Initialization
    const currentTheme = localStorage.getItem('theme') || 'dark';
    const themeToggleBtn = document.getElementById('btn-theme-toggle');
    if (themeToggleBtn) {
        if (currentTheme === 'light') {
            document.body.classList.add('light-theme');
            themeToggleBtn.innerHTML = '<i class="fa fa-moon"></i>';
        } else {
            document.body.classList.remove('light-theme');
            themeToggleBtn.innerHTML = '<i class="fa fa-sun"></i>';
        }
        
        themeToggleBtn.addEventListener('click', function() {
            document.body.classList.toggle('light-theme');
            const isLight = document.body.classList.contains('light-theme');
            localStorage.setItem('theme', isLight ? 'light' : 'dark');
            this.innerHTML = isLight ? '<i class="fa fa-moon"></i>' : '<i class="fa fa-sun"></i>';
            
            // Adjust Chart styles on theme toggle
            if (hourlyChart) {
                const textColor = isLight ? '#475569' : '#cbd5e1';
                const gridColor = isLight ? 'rgba(0,0,0,0.04)' : 'rgba(255, 255, 255, 0.04)';
                hourlyChart.options.plugins.legend.labels.color = textColor;
                hourlyChart.options.scales.x.ticks.color = textColor;
                hourlyChart.options.scales.x.grid.color = gridColor;
                hourlyChart.options.scales.y.ticks.color = textColor;
                hourlyChart.options.scales.y.grid.color = gridColor;
                hourlyChart.update();
            }
        });
    }

    // Check login state and bootstrap UI
    checkSessionStatus();

    // Handle Login Submit
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const usernameInput = document.getElementById('username').value.trim();
            const passwordInput = document.getElementById('password').value.trim();
            const errorEl = document.getElementById('login-error-msg');
            
            errorEl.classList.add('hidden');
            
            fetch('api/auth.php?action=login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username: usernameInput, password: passwordInput })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.reload(); // Reload to start sessions and bootstrap dashboard
                } else {
                    errorEl.textContent = data.message || 'Login failed. Try again.';
                    errorEl.classList.remove('hidden');
                }
            })
            .catch(err => {
                errorEl.textContent = 'Server connection failed.';
                errorEl.classList.remove('hidden');
            });
        });
    }

    // Handle Logout Action
    const btnLogout = document.getElementById('btn-logout');
    if (btnLogout) {
        btnLogout.addEventListener('click', function() {
            if (confirm('Are you sure you want to log out?')) {
                fetch('api/auth.php?action=logout', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' }
                })
                .then(res => res.json())
                .then(() => {
                    window.location.reload();
                });
            }
        });
    }

    // ================= VIEW STATE INITIALIZER =================
    function checkSessionStatus() {
        // If login form is present, we are in auth container, skip session check
        if (document.getElementById('login-form')) return;
        
        // Otherwise, fetch session details and start polling
        fetch('api/auth.php?action=status')
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success' && data.logged_in) {
                    // Set API doc URL dynamically
                    const apiDocUrlEl = document.getElementById('api-endpoint-url');
                    if (apiDocUrlEl) {
                        apiDocUrlEl.textContent = `http://${window.location.host}/api/push.php?list_id=LIST_XXXX`;
                    }
                    
                    // Setup listeners
                    initNavigation();
                    initListCreation();
                    initReports();
                    initUsersManager();
                    initDrilldowns();
                    initHourlyChart();
                    
                    // Initial load and run polling
                    loadDashboardData();
                    AppState.pollingInterval = setInterval(loadDashboardData, 3000);
                } else {
                    window.location.reload();
                }
            })
            .catch(() => {
                console.error('Session check failed.');
            });
    }

    // Initialize Hourly Dialing Chart
    function initHourlyChart() {
        const ctx = document.getElementById('chart-hourly');
        if (!ctx) return;
        
        const isLight = document.body.classList.contains('light-theme');
        const textColor = isLight ? '#475569' : '#cbd5e1';
        const gridColor = isLight ? 'rgba(0,0,0,0.04)' : 'rgba(255, 255, 255, 0.04)';
        
        hourlyChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Dialed',
                        borderColor: '#00f2fe',
                        backgroundColor: 'rgba(0, 242, 254, 0.02)',
                        data: [],
                        borderWidth: 2,
                        tension: 0.35,
                        fill: true
                    },
                    {
                        label: 'Connected',
                        borderColor: '#00f5a0',
                        backgroundColor: 'rgba(0, 245, 160, 0.02)',
                        data: [],
                        borderWidth: 2,
                        tension: 0.35,
                        fill: true
                    },
                    {
                        label: 'Not Connected',
                        borderColor: '#ff4757',
                        backgroundColor: 'rgba(255, 71, 87, 0.02)',
                        data: [],
                        borderWidth: 2,
                        tension: 0.35,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: textColor, font: { family: 'Inter', size: 11 } }
                    }
                },
                scales: {
                    x: {
                        grid: { color: gridColor },
                        ticks: { color: textColor, font: { family: 'Inter', size: 10 } }
                    },
                    y: {
                        grid: { color: gridColor },
                        ticks: { color: textColor, font: { family: 'Inter', size: 10 }, precision: 0 }
                    }
                }
            }
        });
    }

    // ================= SIDEBAR NAVIGATION =================
    function initNavigation() {
        const navItems = document.querySelectorAll('.nav-item');
        const sections = document.querySelectorAll('.content-section');
        const pageTitle = document.getElementById('current-page-title');
        const pageDesc = document.getElementById('current-page-desc');
        
        const pageMetadata = {
            'section-dashboard': { title: 'Dashboard', desc: 'Real-time dialer performance and live statistics' },
            'section-lists': { title: 'List Manager', desc: 'Configure campaigns, audio voice messages, and upload contact leads' },
            'section-reports': { title: 'Call Reports', desc: 'Filter, analyze, and download full communication histories' },
            'section-users': { title: 'User Management', desc: 'Manage administrative login accounts' }
        };

        navItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const target = this.getAttribute('data-target');
                
                navItems.forEach(n => n.classList.remove('active'));
                this.classList.add('active');
                
                sections.forEach(s => s.classList.add('hidden'));
                const targetSection = document.getElementById(target);
                if (targetSection) {
                    targetSection.classList.remove('hidden');
                }
                
                AppState.activeSection = target;
                
                // Update header titles
                if (pageMetadata[target]) {
                    pageTitle.textContent = pageMetadata[target].title;
                    pageDesc.textContent = pageMetadata[target].desc;
                }
                
                // Perform specific page loads
                if (target === 'section-lists') {
                    loadListsManagerData();
                } else if (target === 'section-users') {
                    loadUsersManagerData();
                } else if (target === 'section-reports') {
                    loadReportsDropdowns();
                }
            });
        });
    }

    // ================= SECTION 1: REALTIME DASHBOARD =================
    function loadDashboardData() {
        if (AppState.activeSection !== 'section-dashboard') return;
        
        fetch('api/dashboard.php?action=summary')
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    updateDashboardMetrics(data.overall);
                    updateDashboardLists(data.listwise);
                    updateApiStats(data.api_stats);
                    updateDtmfBreakdown(data.dtmf_breakdown);
                    updateHourlyChartData(data.hourly_stats);
                }
            })
            .catch(err => console.error('Error fetching dashboard stats:', err));
    }

    function updateHourlyChartData(stats) {
        if (!hourlyChart || !stats) return;
        
        const labels = [];
        const dialedData = [];
        const connectedData = [];
        const notConnectedData = [];
        
        stats.forEach(item => {
            labels.push(item.hour_label);
            dialedData.push(parseInt(item.dialed));
            connectedData.push(parseInt(item.connected));
            notConnectedData.push(parseInt(item.not_connected));
        });
        
        hourlyChart.data.labels = labels;
        hourlyChart.data.datasets[0].data = dialedData;
        hourlyChart.data.datasets[1].data = connectedData;
        hourlyChart.data.datasets[2].data = notConnectedData;
        hourlyChart.update();
    }

    function updateDashboardMetrics(overall) {
        document.getElementById('stat-total').textContent = formatNum(overall.total);
        document.getElementById('stat-pending').textContent = formatNum(overall.pending);
        document.getElementById('stat-ringing').textContent = formatNum(overall.ringing);
        document.getElementById('stat-answered').textContent = formatNum(overall.answered);
        document.getElementById('stat-completed').textContent = formatNum(overall.completed);
        document.getElementById('stat-not_connected').textContent = formatNum(overall.not_connected);
        
        // Update new KPI metrics cards
        const avgDurationEl = document.getElementById('stat-avg-duration');
        if (avgDurationEl) avgDurationEl.textContent = formatDurationHMS(overall.avg_duration);
        
        const connectRateEl = document.getElementById('stat-connect-rate');
        if (connectRateEl) connectRateEl.textContent = overall.connection_rate + '%';
        
        const dtmfRateEl = document.getElementById('stat-dtmf-rate');
        if (dtmfRateEl) dtmfRateEl.textContent = overall.dtmf_rate + '%';
        
        const totalTalktimeEl = document.getElementById('stat-total-talktime');
        if (totalTalktimeEl) totalTalktimeEl.textContent = formatDurationHMS(overall.total_seconds);
        
        // Update Dialer Productivity panel elements
        const peakHourEl = document.getElementById('stat-peak-hour');
        if (peakHourEl) peakHourEl.textContent = overall.peak_hour;
        
        const totalMinEl = document.getElementById('stat-total-minutes');
        if (totalMinEl) totalMinEl.textContent = formatDurationHMS(overall.total_seconds);
        
        const contactIndexEl = document.getElementById('stat-contact-index');
        if (contactIndexEl) {
            let indexRating = 'Low';
            let indexColor = 'var(--accent-red)';
            if (overall.connection_rate >= 80) {
                indexRating = 'High';
                indexColor = 'var(--accent-green)';
            } else if (overall.connection_rate >= 50) {
                indexRating = 'Medium';
                indexColor = 'var(--accent-orange)';
            }
            contactIndexEl.textContent = indexRating;
            contactIndexEl.style.color = indexColor;
        }
    }

    function updateDashboardLists(lists) {
        PaginatedTable.init('tbl-dashboard-lists', lists, renderDashboardListsOnly, 5);
    }

    function renderDashboardListsOnly(pageLists) {
        const tbody = document.querySelector('#tbl-dashboard-lists tbody');
        if (!tbody) return;
        
        if (pageLists.length === 0) {
            tbody.innerHTML = `<tr><td colspan="10" class="text-center text-muted">No active campaigns...</td></tr>`;
            return;
        }
        
        let html = '';
        pageLists.forEach(list => {
            const total = parseInt(list.total_uploaded);
            const dialed = parseInt(list.dialed);
            const answered = parseInt(list.answered);
            const not_connected = parseInt(list.not_connected || 0);
            const dtmf = parseInt(list.dtmf_responses);
            
            const progress = total > 0 ? Math.round((dialed / total) * 100) : 0;
            
            // Determine active/inactive state based on dates
            const now = new Date();
            const start = new Date(list.start_time);
            const end = new Date(list.end_time);
            
            const isPaused = parseInt(list.is_paused) === 1;
            
            let statusBadge = '';
            let pauseResumeBtn = '';
            
            if (now < start) {
                statusBadge = '<span class="badge-orange">Scheduled</span>';
            } else if (now > end) {
                statusBadge = '<span class="badge-grey">Expired</span>';
            } else if (dialed >= total && total > 0) {
                statusBadge = '<span class="badge-grey">Completed</span>';
            } else {
                if (isPaused) {
                    statusBadge = '<span class="badge-orange"><i class="fa fa-pause"></i> Paused</span>';
                    pauseResumeBtn = `
                        <button class="btn-sm-success" onclick="resumeCampaign('${list.list_id}')" title="Resume Campaign">
                            <i class="fa fa-play"></i>
                        </button>
                    `;
                } else {
                    statusBadge = '<span class="badge-green">Active</span>';
                    pauseResumeBtn = `
                        <button class="btn-sm-primary" style="background:rgba(255, 159, 67, 0.1); border-color:rgba(255, 159, 67, 0.2); color:var(--accent-orange);" onclick="pauseCampaign('${list.list_id}')" title="Pause Campaign">
                            <i class="fa fa-pause"></i>
                        </button>
                    `;
                }
            }
            
            let redialBtn = '';
            if (not_connected > 0) {
                redialBtn = `
                    <button class="btn-sm-primary btn-redial" onclick="redialCampaign('${list.list_id}')" title="Redial Failed Contacts">
                        <i class="fa fa-redo"></i>
                    </button>
                `;
            }
            
            html += `
                <tr>
                    <td><strong>${list.list_id}</strong></td>
                    <td>${escapeHtml(list.name)}</td>
                    <td>${formatNum(total)}</td>
                    <td>${formatNum(dialed)}</td>
                    <td>${formatNum(answered)}</td>
                    <td>${formatNum(not_connected)}</td>
                    <td><span class="badge-blue"><i class="fa fa-phone"></i> ${formatNum(dtmf)} Hits</span></td>
                    <td>
                        <div class="progress-container">
                           <div class="progress-bar-bg">
                                <div class="progress-bar-fill" style="width: ${progress}%"></div>
                            </div>
                            <span class="progress-text">${progress}%</span>
                        </div>
                    </td>
                    <td>${statusBadge}</td>
                    <td>
                        <div style="display:flex; gap:6px; align-items:center;">
                            <button class="btn-sm-primary btn-drilldown-list" onclick="viewListCalls('${list.list_id}')" title="View Calls Details">
                                <i class="fa fa-eye"></i>
                            </button>
                            ${pauseResumeBtn}
                            ${redialBtn}
                        </div>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    function updateApiStats(apiStats) {
        document.getElementById('api-success-count').textContent = formatNum(apiStats.success);
        document.getElementById('api-fail-count').textContent = formatNum(apiStats.failed);
        
        const listEl = document.getElementById('api-log-list');
        if (!listEl) return;
        
        if (apiStats.recent.length === 0) {
            listEl.innerHTML = `<li class="text-muted text-center py-3">No API payloads pushed yet.</li>`;
            return;
        }
        
        let html = '';
        apiStats.recent.forEach(log => {
            const timeStr = new Date(log.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            html += `
                <li class="api-log-item">
                    <div class="api-log-info">
                        <span class="api-log-listname">${log.list_id} (${escapeHtml(log.list_name || 'API Campaign')})</span>
                        <span class="api-log-time"><i class="fa fa-clock"></i> ${timeStr}</span>
                    </div>
                    <div class="api-log-counts">
                        <span class="api-log-success">+${formatNum(log.success_count)} ok</span>
                        <span class="api-log-fail">-${formatNum(log.fail_count)} err</span>
                    </div>
                </li>
            `;
        });
        listEl.innerHTML = html;
    }

    function updateDtmfBreakdown(breakdown) {
        const grid = document.querySelector('.dtmf-keypad-grid');
        let placeholder = document.getElementById('dtmf-empty-placeholder');
        
        if (!placeholder && grid) {
            placeholder = document.createElement('div');
            placeholder.id = 'dtmf-empty-placeholder';
            placeholder.className = 'text-muted text-center py-4 span-12';
            placeholder.innerHTML = '<i class="fa fa-comment-slash" style="font-size:24px; margin-bottom:10px; display:block;"></i> No customer DTMF responses captured yet.';
            grid.parentNode.insertBefore(placeholder, grid);
        }
        
        // Hide all keycaps by default
        const keys = ['1', '2', '3', '4', '5', '6', '7', '8', '9', 'star', '0', 'hash'];
        keys.forEach(k => {
            const cap = document.getElementById(`dtmf-key-${k}`);
            if (cap) {
                cap.style.display = 'none';
                cap.querySelector('.key-count').textContent = '0 hits';
                cap.querySelector('.key-progress-fill').style.width = '0%';
            }
        });
        
        if (breakdown.length === 0) {
            if (placeholder) placeholder.style.display = 'block';
            if (grid) grid.style.display = 'none';
            return;
        }
        
        let hasHits = false;
        let maxHits = 0;
        breakdown.forEach(item => {
            const count = parseInt(item.count);
            if (count > maxHits) maxHits = count;
            if (count > 0) hasHits = true;
        });
        
        if (!hasHits) {
            if (placeholder) placeholder.style.display = 'block';
            if (grid) grid.style.display = 'none';
            return;
        }
        
        // Show grid and hide empty placeholder
        if (placeholder) placeholder.style.display = 'none';
        if (grid) grid.style.display = 'grid';
        
        breakdown.forEach(item => {
            let keyId = item.dtmf_response;
            if (keyId === '*') keyId = 'star';
            if (keyId === '#') keyId = 'hash';
            
            const cap = document.getElementById(`dtmf-key-${keyId}`);
            const count = parseInt(item.count);
            
            if (cap && count > 0) {
                cap.style.display = 'flex'; // Show active keycap
                const pct = maxHits > 0 ? Math.round((count / maxHits) * 100) : 0;
                cap.querySelector('.key-count').textContent = `${formatNum(count)} hits`;
                cap.querySelector('.key-progress-fill').style.width = `${pct}%`;
            }
        });
    }

    // ================= SECTION 2: LIST MANAGER =================
    function initListCreation() {
        const form = document.getElementById('list-create-form');
        const submitBtn = document.getElementById('btn-submit-list');
        const msgEl = document.getElementById('list-msg');
        
        // Handle custom file selectors UI updates
        const audioInput = document.getElementById('audio_file');
        const leadsInput = document.getElementById('leads_file');
        
        if (audioInput) {
            audioInput.addEventListener('change', function() {
                const trigger = this.nextElementSibling;
                const previewContainer = document.getElementById('audio-preview-container');
                const previewPlayer = document.getElementById('audio-upload-preview');
                
                if (this.files && this.files[0]) {
                    trigger.classList.add('selected');
                    trigger.querySelector('span').textContent = `${this.files[0].name} (${formatBytes(this.files[0].size)})`;
                    trigger.querySelector('i').className = 'fa fa-check-circle';
                    
                    // Create Object URL for audio playback preview
                    const blobUrl = URL.createObjectURL(this.files[0]);
                    if (previewPlayer && previewContainer) {
                        previewPlayer.src = blobUrl;
                        previewContainer.classList.remove('hidden');
                    }
                } else {
                    if (previewContainer) previewContainer.classList.add('hidden');
                }
            });
        }
        
        if (leadsInput) {
            leadsInput.addEventListener('change', function() {
                const trigger = this.nextElementSibling;
                if (this.files && this.files[0]) {
                    trigger.classList.add('selected');
                    trigger.querySelector('span').textContent = `${this.files[0].name} (${formatBytes(this.files[0].size)})`;
                    trigger.querySelector('i').className = 'fa fa-check-circle';
                }
            });
        }
        
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                submitBtn.disabled = true;
                submitBtn.querySelector('span').textContent = 'Uploading & Parsing...';
                msgEl.className = 'alert hidden';
                
                const formData = new FormData(this);
                
                fetch('api/lists.php?action=create', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    submitBtn.disabled = false;
                    submitBtn.querySelector('span').textContent = 'Create List & Import';
                    
                    if (data.status === 'success') {
                        msgEl.className = 'alert alert-success';
                        msgEl.innerHTML = `<strong>Success!</strong> ${data.message}`;
                        
                        // Clear form and hide preview
                        form.reset();
                        const previewContainer = document.getElementById('audio-preview-container');
                        if (previewContainer) previewContainer.classList.add('hidden');
                        
                        document.querySelectorAll('.upload-trigger').forEach(trigger => {
                            trigger.classList.remove('selected');
                            const isAudio = trigger.querySelector('i').classList.contains('fa-volume-up') || trigger.previousElementSibling.id === 'audio_file';
                            trigger.querySelector('span').textContent = isAudio ? 'Choose WAV or MP3' : 'Choose XLSX or CSV';
                            trigger.querySelector('i').className = isAudio ? 'fa fa-volume-up' : 'fa fa-file-excel';
                        });
                        
                        // Reload data
                        loadListsManagerData();
                    } else {
                        msgEl.className = 'alert alert-danger';
                        msgEl.textContent = data.message || 'Error occurred uploading file.';
                    }
                })
                .catch(err => {
                    submitBtn.disabled = false;
                    submitBtn.querySelector('span').textContent = 'Create List & Import';
                    msgEl.className = 'alert alert-danger';
                    msgEl.textContent = 'Server communication error.';
                });
            });
        }
    }

    function loadListsManagerData() {
        fetch('api/lists.php?action=list')
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    renderListsManagerTable(data.lists);
                }
            })
            .catch(err => console.error('Error loading list data:', err));
    }

    function renderListsManagerTable(lists) {
        PaginatedTable.init('tbl-lists-manager', lists, renderListsManagerTableOnly, 10);
    }

    function renderListsManagerTableOnly(pageLists) {
        const tbody = document.querySelector('#tbl-lists-manager tbody');
        if (!tbody) return;
        
        if (pageLists.length === 0) {
            tbody.innerHTML = `<tr><td colspan="9" class="text-center text-muted">No campaigns created yet. Create one above!</td></tr>`;
            return;
        }
        
        let html = '';
        pageLists.forEach(list => {
            const total = parseInt(list.total_leads);
            const dialed = parseInt(list.dialed_leads);
            const answered = parseInt(list.answered_leads);
            const failed = parseInt(list.failed_leads || 0);
            const progress = total > 0 ? Math.round((dialed / total) * 100) : 0;
            
            const isPaused = parseInt(list.is_paused) === 1;
            
            // Pause/Resume button inside List Manager
            const now = new Date();
            const start = new Date(list.start_time);
            const end = new Date(list.end_time);
            
            let pauseResumeBtn = '';
            if (now >= start && now <= end && dialed < total) {
                if (isPaused) {
                    pauseResumeBtn = `
                        <button onclick="resumeCampaign('${list.list_id}')" class="btn-sm-success" title="Resume Campaign">
                            <i class="fa fa-play"></i> Resume
                        </button>
                    `;
                } else {
                    pauseResumeBtn = `
                        <button onclick="pauseCampaign('${list.list_id}')" class="btn-sm-primary" style="background:rgba(255, 159, 67, 0.1); border-color:rgba(255, 159, 67, 0.2); color:var(--accent-orange);" title="Pause Campaign">
                            <i class="fa fa-pause"></i> Pause
                        </button>
                    `;
                }
            }
            
            let redialBtn = '';
            if (failed > 0) {
                redialBtn = `
                    <button onclick="redialCampaign('${list.list_id}')" class="btn-sm-primary btn-redial" title="Redial Failed Contacts">
                        <i class="fa fa-redo"></i> Redial
                    </button>
                `;
            }
            
            html += `
                <tr id="list-row-${list.list_id}">
                    <td><strong>${list.list_id}</strong></td>
                    <td>${escapeHtml(list.name)}</td>
                    <td>${escapeHtml(list.description || '-')}</td>
                    <td>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <audio src="${list.audio_file}" controls class="table-audio-player" preload="none"></audio>
                            <a href="${list.audio_file}" download class="btn-sm-primary btn-download-rec" title="Download Announcement">
                                <i class="fa fa-download"></i>
                            </a>
                        </div>
                    </td>
                    <td>${formatNum(total)}</td>
                    <td>
                        <div class="progress-container">
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill" style="width: ${progress}%"></div>
                            </div>
                            <span class="progress-text">${dialed}/${total}</span>
                        </div>
                    </td>
                    <td><small>${list.start_time}</small></td>
                    <td><small>${list.end_time}</small></td>
                    <td>
                        <div style="display:flex; gap:6px; align-items:center;">
                            <a href="api/lists.php?action=download&list_id=${list.list_id}" class="btn-sm-success" title="Download Leads CSV">
                                <i class="fa fa-download"></i> Leads
                            </a>
                            ${pauseResumeBtn}
                            ${redialBtn}
                            <button onclick="deleteList('${list.list_id}')" class="btn-sm-danger" title="Delete Campaign">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    // Global hook for list deletion since onclick is used inline
    window.deleteList = function(listId) {
        if (confirm(`WARNING: Deleting campaign ${listId} will delete all associated leads, call logs, recordings, and history. Proceed?`)) {
            fetch('api/lists.php?action=delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ list_id: listId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const row = document.getElementById(`list-row-${listId}`);
                    if (row) row.remove();
                    loadListsManagerData();
                } else {
                    alert(data.message || 'Failed to delete list.');
                }
            })
            .catch(() => alert('Failed to communicate with server.'));
        }
    };

    // ================= SECTION 3: CALL REPORTS =================
    function initReports() {
        const filterForm = document.getElementById('report-filter-form');
        const exportBtn = document.getElementById('btn-export-reports');
        
        // Default date range: today to today
        const todayStr = new Date().toISOString().split('T')[0];
        document.getElementById('filter_start_date').value = todayStr;
        document.getElementById('filter_end_date').value = todayStr;
        
        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                loadReportsLogs();
            });
        }
        
        if (exportBtn) {
            exportBtn.addEventListener('click', function() {
                // Compile form query string
                const start = document.getElementById('filter_start_date').value;
                const end = document.getElementById('filter_end_date').value;
                const listId = document.getElementById('filter_list_id').value;
                const status = document.getElementById('filter_status').value;
                const dtmf = document.getElementById('filter_dtmf').value;
                
                const qs = `action=export&start_date=${start}&end_date=${end}&list_id=${listId}&status=${status}&dtmf=${dtmf}`;
                window.location.href = `api/reports.php?${qs}`;
            });
        }
    }

    function loadReportsDropdowns() {
        const dropdown = document.getElementById('filter_list_id');
        if (!dropdown) return;
        
        fetch('api/lists.php?action=list')
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    let html = '<option value="all">-- All Campaigns --</option>';
                    data.lists.forEach(list => {
                        html += `<option value="${list.list_id}">${list.list_id} - ${escapeHtml(list.name)}</option>`;
                    });
                    dropdown.innerHTML = html;
                }
            })
            .catch(err => console.error('Error loading reports dropdown:', err));
    }

    function loadReportsLogs() {
        const start = document.getElementById('filter_start_date').value;
        const end = document.getElementById('filter_end_date').value;
        const listId = document.getElementById('filter_list_id').value;
        const status = document.getElementById('filter_status').value;
        const dtmf = document.getElementById('filter_dtmf').value;
        
        const badge = document.getElementById('reports-count-badge');
        const tbody = document.querySelector('#tbl-reports-logs tbody');
        
        tbody.innerHTML = `<tr><td colspan="9" class="text-center text-muted"><i class="fa fa-spinner fa-spin"></i> Fetching records...</td></tr>`;
        
        const qs = `action=search&start_date=${start}&end_date=${end}&list_id=${listId}&status=${status}&dtmf=${dtmf}`;
        
        fetch(`api/reports.php?${qs}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    badge.textContent = `${data.total} records matched`;
                    renderReportsTable(data.records);
                } else {
                    tbody.innerHTML = `<tr><td colspan="9" class="text-center text-danger">${data.message || 'Error pulling logs'}</td></tr>`;
                }
            })
            .catch(() => {
                tbody.innerHTML = `<tr><td colspan="9" class="text-center text-danger">Server communication error.</td></tr>`;
            });
    }

    function renderReportsTable(records) {
        PaginatedTable.init('tbl-reports-logs', records, renderReportsTableOnly, 10);
    }

    function renderReportsTableOnly(pageRecords) {
        const tbody = document.querySelector('#tbl-reports-logs tbody');
        if (!tbody) return;
        
        if (pageRecords.length === 0) {
            tbody.innerHTML = `<tr><td colspan="9" class="text-center text-muted">No records matched your criteria.</td></tr>`;
            return;
        }
        
        let html = '';
        pageRecords.forEach(log => {
            let badge = '';
            if (log.status === 'completed') badge = '<span class="badge-green">Connected</span>';
            else if (log.status === 'ringing') badge = '<span class="badge-orange">Ringing</span>';
            else if (log.status === 'answered') badge = '<span class="badge-blue">On Call</span>';
            else if (log.status === 'pending') badge = '<span class="badge-grey">Pending</span>';
            else badge = '<span class="badge-red">Not Connected</span>';
            
            // Audio recording cell
            let recordingCell = '';
            if (log.status === 'completed' && log.recording_file) {
                const relativePath = getRelativeRecordingPath(log.recording_file);
                recordingCell = `
                    <div class="audio-player-wrapper" style="display:flex; align-items:center; gap:8px;">
                        <audio src="${relativePath}" controls preload="none" style="height:28px; width:120px;"></audio>
                        <a href="${relativePath}" download class="btn-sm-primary btn-download-rec" title="Download Recording">
                            <i class="fa fa-download"></i>
                        </a>
                    </div>
                `;
            } else {
                recordingCell = `<span class="recording-missing">-</span>`;
            }
            
            html += `
                <tr>
                    <td><small>${log.dial_time || log.created_at}</small></td>
                    <td><strong>${log.list_id}</strong></td>
                    <td>${escapeHtml(log.list_name || '-')}</td>
                    <td>${escapeHtml(log.name)}</td>
                    <td>${log.phone_number}</td>
                    <td>${badge}</td>
                    <td>${formatDurationHMS(log.duration)}</td>
                    <td><strong class="text-muted">${log.dtmf_response !== null ? log.dtmf_response : '-'}</strong></td>
                    <td>${recordingCell}</td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    // ================= SECTION 4: USER MANAGEMENT =================
    function initUsersManager() {
        const form = document.getElementById('user-create-form');
        const msgEl = document.getElementById('user-msg');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const newUsername = document.getElementById('new_username').value.trim();
                const newName = document.getElementById('new_name').value.trim();
                const newPassword = document.getElementById('new_password').value.trim();
                
                msgEl.className = 'alert hidden';
                
                fetch('api/users.php?action=create', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username: newUsername, name: newName, password: newPassword })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        msgEl.className = 'alert alert-success';
                        msgEl.textContent = 'Account registered successfully!';
                        form.reset();
                        loadUsersManagerData();
                    } else {
                        msgEl.className = 'alert alert-danger';
                        msgEl.textContent = data.message || 'Failed to create user.';
                    }
                })
                .catch(() => {
                    msgEl.className = 'alert alert-danger';
                    msgEl.textContent = 'Server connection failed.';
                });
            });
        }
    }

    function loadUsersManagerData() {
        fetch('api/users.php')
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    renderUsersTable(data.users);
                }
            })
            .catch(err => console.error('Error loading users:', err));
    }

    function renderUsersTable(users) {
        PaginatedTable.init('tbl-users-list', users, renderUsersTableOnly, 10);
    }

    function renderUsersTableOnly(pageUsers) {
        const tbody = document.querySelector('#tbl-users-list tbody');
        if (!tbody) return;
        
        let html = '';
        pageUsers.forEach(user => {
            // Check if primary admin
            const isPrimaryAdmin = user.username === 'admin';
            const actionBtn = isPrimaryAdmin 
                ? '<span class="text-muted"><i class="fa fa-lock"></i> Protected</span>'
                : `<button onclick="deleteUser(${user.id})" class="btn-sm-danger"><i class="fa fa-user-minus"></i> Delete</button>`;
            
            html += `
                <tr id="user-row-${user.id}" class="${isPrimaryAdmin ? 'user-tr-admin' : ''}">
                    <td>${user.id}</td>
                    <td><strong>${escapeHtml(user.username)}</strong></td>
                    <td>${escapeHtml(user.name)}</td>
                    <td><small>${user.created_at}</small></td>
                    <td>${actionBtn}</td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    // Global hook for user deletion since onclick is used inline
    window.deleteUser = function(userId) {
        if (confirm('Are you sure you want to delete this administrative user? This cannot be undone.')) {
            fetch('api/users.php?action=delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: userId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const row = document.getElementById(`user-row-${userId}`);
                    if (row) row.remove();
                } else {
                    alert(data.message || 'Failed to delete user.');
                }
            })
            .catch(() => alert('Failed to communicate with server.'));
        }
    };

    // ================= STATS DRILLDOWN DETAILS MODALS =================
    function initDrilldowns() {
        const modal = document.getElementById('modal-drilldown');
        const closeModalBtn = document.getElementById('btn-close-modal');
        const searchInput = document.getElementById('modal-search-input');
        const exportBtn = document.getElementById('btn-modal-export');
        
        // Close modal events
        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', () => {
                modal.classList.add('hidden');
            });
        }
        
        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
        
        // Grid cards click listeners
        const cards = document.querySelectorAll('.metric-card.clickable');
        cards.forEach(card => {
            card.addEventListener('click', function() {
                const type = this.getAttribute('data-drilldown');
                openDrilldownModal(type);
            });
        });

        // DTMF keycaps click listeners
        const dtmfCaps = document.querySelectorAll('.dtmf-keycap');
        dtmfCaps.forEach(cap => {
            cap.style.cursor = 'pointer';
            cap.addEventListener('click', function() {
                let keyVal = this.id.replace('dtmf-key-', '');
                if (keyVal === 'star') keyVal = '*';
                if (keyVal === 'hash') keyVal = '#';
                openDrilldownModal('dtmf_key', keyVal);
            });
        });
        
        // Export drilldown to CSV
        if (exportBtn) {
            exportBtn.addEventListener('click', function() {
                let exportUrl = `api/dashboard.php?action=export_details&type=${AppState.currentDrilldownType}`;
                if (AppState.currentDrilldownType === 'campaign_dialed') {
                    exportUrl += `&list_id=${AppState.currentDrilldownExtra}`;
                } else if (AppState.currentDrilldownType === 'dtmf_key') {
                    exportUrl += `&dtmf_key=${AppState.currentDrilldownExtra}`;
                }
                window.location.href = exportUrl;
            });
        }
        
        // Client side filtering inside modal
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                filterModalTable(query);
            });
        }
    }

    function openDrilldownModal(type, extraParam = '') {
        AppState.currentDrilldownType = type;
        AppState.currentDrilldownExtra = extraParam;
        const modal = document.getElementById('modal-drilldown');
        const titleEl = document.getElementById('modal-title');
        const searchInput = document.getElementById('modal-search-input');
        const thead = document.querySelector('#tbl-modal-details thead');
        const tbody = document.querySelector('#tbl-modal-details tbody');
        
        searchInput.value = '';
        modal.classList.remove('hidden');
        
        // Dynamically adjust table headers based on type
        const isBasicColumns = (type === 'pending' || type === 'ringing' || type === 'answered');
        if (isBasicColumns) {
            thead.innerHTML = `
                <tr>
                    <th>Name</th>
                    <th>List ID</th>
                    <th>Phone Number</th>
                    <th>Call Status</th>
                </tr>
            `;
            tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted"><i class="fa fa-spinner fa-spin"></i> Fetching drilldown logs...</td></tr>`;
        } else {
            thead.innerHTML = `
                <tr>
                    <th>Name</th>
                    <th>List ID</th>
                    <th>Phone Number</th>
                    <th>Call Status</th>
                    <th>Dialed Date/Time</th>
                    <th>Duration (hh:mm:ss)</th>
                    <th>DTMF Response</th>
                    <th>Recording</th>
                </tr>
            `;
            tbody.innerHTML = `<tr><td colspan="8" class="text-center text-muted"><i class="fa fa-spinner fa-spin"></i> Fetching drilldown logs...</td></tr>`;
        }
        
        // Format modal title based on type
        const typeLabels = {
            'total': 'Total Uploaded Leads',
            'pending': 'Yet to Dial Leads',
            'ringing': 'Live Ringing Contacts',
            'answered': 'Live On Call (Answered) Contacts',
            'completed': 'Connected Lead Contacts',
            'not_connected': 'Not Connected Contacts',
            'campaign_dialed': `Dialed Campaign Details: ${extraParam}`,
            'dtmf_key': `DTMF Hits Details for Key [${extraParam}]`
        };
        titleEl.textContent = typeLabels[type] || 'Lead Details';
        
        // Fetch detailed logs
        let fetchUrl = `api/dashboard.php?action=details&type=${type}`;
        if (type === 'campaign_dialed') {
            fetchUrl += `&list_id=${extraParam}`;
        } else if (type === 'dtmf_key') {
            fetchUrl += `&dtmf_key=${extraParam}`;
        }
        
        fetch(fetchUrl)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    AppState.drilldownLeads = data.leads;
                    renderModalTable(data.leads);
                } else {
                    const cols = isBasicColumns ? 4 : 8;
                    tbody.innerHTML = `<tr><td colspan="${cols}" class="text-center text-danger">Error: ${data.message}</td></tr>`;
                }
            })
            .catch(() => {
                const cols = isBasicColumns ? 4 : 8;
                tbody.innerHTML = `<tr><td colspan="${cols}" class="text-center text-danger">Failed to connect to backend.</td></tr>`;
            });
    }

    function renderModalTable(leads) {
        PaginatedTable.init('tbl-modal-details', leads, renderModalTableOnly, 10);
    }

    function renderModalTableOnly(pageLeads) {
        const tbody = document.querySelector('#tbl-modal-details tbody');
        if (!tbody) return;
        
        const isBasicColumns = (AppState.currentDrilldownType === 'pending' || AppState.currentDrilldownType === 'ringing' || AppState.currentDrilldownType === 'answered');
        const cols = isBasicColumns ? 4 : 8;
        
        if (pageLeads.length === 0) {
            tbody.innerHTML = `<tr><td colspan="${cols}" class="text-center text-muted">No records found.</td></tr>`;
            return;
        }
        
        let html = '';
        pageLeads.forEach(lead => {
            let badge = '';
            if (lead.status === 'completed') badge = '<span class="badge-green">Connected</span>';
            else if (lead.status === 'ringing') badge = '<span class="badge-orange">Ringing</span>';
            else if (lead.status === 'answered') badge = '<span class="badge-blue">On Call</span>';
            else if (lead.status === 'pending') badge = '<span class="badge-grey">Pending</span>';
            else badge = '<span class="badge-red">Not Connected</span>';
            
            if (isBasicColumns) {
                html += `
                    <tr class="modal-lead-row">
                        <td>${escapeHtml(lead.name)}</td>
                        <td><strong>${lead.list_id}</strong></td>
                        <td class="phone-search-val">${lead.phone_number}</td>
                        <td>${badge}</td>
                    </tr>
                `;
            } else {
                let recording = '-';
                if (lead.status === 'completed' && lead.recording_file) {
                    const relativePath = getRelativeRecordingPath(lead.recording_file);
                    recording = `
                        <div class="audio-player-wrapper" style="display:flex; align-items:center; gap:8px;">
                            <audio src="${relativePath}" controls preload="none" style="height:28px; width:120px;"></audio>
                            <a href="${relativePath}" download class="btn-sm-primary btn-download-rec" title="Download Recording">
                                <i class="fa fa-download"></i>
                            </a>
                        </div>
                    `;
                }
                
                html += `
                    <tr class="modal-lead-row">
                        <td>${escapeHtml(lead.name)}</td>
                        <td><strong>${lead.list_id}</strong></td>
                        <td class="phone-search-val">${lead.phone_number}</td>
                        <td>${badge}</td>
                        <td><small>${lead.dial_time || 'Not Dialed'}</small></td>
                        <td>${formatDurationHMS(lead.duration)}</td>
                        <td><strong class="text-muted">${lead.dtmf_response !== null ? lead.dtmf_response : '-'}</strong></td>
                        <td>${recording}</td>
                    </tr>
                `;
            }
        });
        tbody.innerHTML = html;
    }

    function filterModalTable(query) {
        const filteredLeads = AppState.drilldownLeads.filter(lead => {
            const name = (lead.name || '').toLowerCase();
            const phone = (lead.phone_number || '').toLowerCase();
            const listId = (lead.list_id || '').toLowerCase();
            return name.includes(query) || phone.includes(query) || listId.includes(query);
        });
        renderModalTable(filteredLeads);
    }

    // ================= HELPER FUNCTIONS =================
    function formatNum(num) {
        return parseInt(num).toLocaleString();
    }

    function formatBytes(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    }

    function escapeHtml(string) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(string).replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    function getRelativeRecordingPath(path) {
        if (!path) return '';
        const index = path.indexOf('recordings/');
        if (index !== -1) {
            return path.substring(index);
        }
        return path;
    }

    function formatDurationHMS(sec) {
        sec = Math.round(parseFloat(sec || 0));
        if (sec < 0) sec = 0;
        const h = Math.floor(sec / 3600);
        const m = Math.floor((sec % 3600) / 60);
        const s = sec % 60;
        const pad = (n) => String(n).padStart(2, '0');
        return `${pad(h)}:${pad(m)}:${pad(s)}`;
    }

    // Global hook for list details drilldown modal
    window.viewListCalls = function(listId) {
        openDrilldownModal('campaign_dialed', listId);
    };

    window.pauseCampaign = function(listId) {
        if (confirm(`Are you sure you want to PAUSE dialing operations for campaign ${listId}?`)) {
            fetch('api/lists.php?action=pause', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ list_id: listId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    loadDashboardData();
                    loadListsManagerData();
                } else {
                    alert(data.message || 'Failed to pause campaign.');
                }
            })
            .catch(() => alert('Failed to communicate with server.'));
        }
    };

    window.resumeCampaign = function(listId) {
        fetch('api/lists.php?action=resume', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ list_id: listId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                loadDashboardData();
                loadListsManagerData();
            } else {
                alert(data.message || 'Failed to resume campaign.');
            }
        })
        .catch(() => alert('Failed to communicate with server.'));
    };

    window.redialCampaign = function(listId) {
        if (confirm(`This will revert all failed or not-connected calls in campaign ${listId} to 'pending' state so they can be dialed again. Proceed?`)) {
            fetch('api/lists.php?action=redial', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ list_id: listId })
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                loadDashboardData();
                loadListsManagerData();
            })
            .catch(() => alert('Failed to communicate with server.'));
        }
    };

    // ================= CLIENT-SIDE PAGINATION CONTROLLER =================
    const PaginatedTable = {
        init: function(tableId, data, renderFn, pageSize = 10) {
            const table = document.getElementById(tableId);
            if (!table) return;
            
            let controlsContainer = document.getElementById(tableId + '-pagination');
            if (!controlsContainer) {
                controlsContainer = document.createElement('div');
                controlsContainer.id = tableId + '-pagination';
                controlsContainer.className = 'table-pagination-controls';
                table.parentNode.appendChild(controlsContainer);
            }
            
            let currentPage = 1;
            
            function renderPage(page) {
                currentPage = page;
                const start = (page - 1) * pageSize;
                const end = start + pageSize;
                const pageData = data.slice(start, end);
                
                renderFn(pageData);
                updateControls(data.length, page);
            }
            
            function updateControls(totalItems, activePage) {
                const totalPages = Math.ceil(totalItems / pageSize) || 1;
                if (activePage > totalPages) activePage = totalPages;
                
                const startItem = totalItems === 0 ? 0 : (activePage - 1) * pageSize + 1;
                const endItem = Math.min(activePage * pageSize, totalItems);
                
                let html = `
                    <div class="pagination-info">
                        Showing <strong>${startItem}</strong> to <strong>${endItem}</strong> of <strong>${totalItems}</strong> entries
                    </div>
                `;
                
                if (totalPages > 1) {
                    html += `<div class="pagination-buttons">`;
                    html += `
                        <button type="button" class="btn-pag" ${activePage === 1 ? 'disabled' : ''} data-page="1"><i class="fa fa-angle-double-left"></i></button>
                        <button type="button" class="btn-pag" ${activePage === 1 ? 'disabled' : ''} data-page="${activePage - 1}"><i class="fa fa-angle-left"></i></button>
                    `;
                    
                    let startPage = Math.max(1, activePage - 2);
                    let endPage = Math.min(totalPages, startPage + 4);
                    if (endPage - startPage < 4) {
                        startPage = Math.max(1, endPage - 4);
                    }
                    
                    for (let p = startPage; p <= endPage; p++) {
                        html += `
                            <button type="button" class="btn-pag ${p === activePage ? 'active' : ''}" data-page="${p}">${p}</button>
                        `;
                    }
                    
                    html += `
                        <button type="button" class="btn-pag" ${activePage === totalPages ? 'disabled' : ''} data-page="${activePage + 1}"><i class="fa fa-angle-right"></i></button>
                        <button type="button" class="btn-pag" ${activePage === totalPages ? 'disabled' : ''} data-page="${totalPages}"><i class="fa fa-angle-double-right"></i></button>
                    `;
                    html += `</div>`;
                }
                
                html += `
                    <div class="pagination-size-selector">
                        Show 
                        <select class="select-pag-size">
                            <option value="5" ${pageSize === 5 ? 'selected' : ''}>5</option>
                            <option value="10" ${pageSize === 10 ? 'selected' : ''}>10</option>
                            <option value="25" ${pageSize === 25 ? 'selected' : ''}>25</option>
                            <option value="50" ${pageSize === 50 ? 'selected' : ''}>50</option>
                        </select>
                        entries
                    </div>
                `;
                
                controlsContainer.innerHTML = html;
                
                controlsContainer.querySelectorAll('.pagination-buttons button').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const targetPage = parseInt(this.getAttribute('data-page'));
                        if (targetPage && targetPage !== activePage) {
                            renderPage(targetPage);
                        }
                    });
                });
                
                const sizeSelector = controlsContainer.querySelector('.select-pag-size');
                if (sizeSelector) {
                    sizeSelector.addEventListener('change', function() {
                        pageSize = parseInt(this.value);
                        currentPage = 1;
                        renderPage(1);
                    });
                }
            }
            
            renderPage(1);
        }
    };

});
