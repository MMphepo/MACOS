<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investigation Request Management System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --secondary: #0891b2;
            --success: #059669;
            --warning: #d97706;
            --danger: #dc2626;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-900: #111827;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            min-height: 100vh;
            padding: 0;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(120, 119, 198, 0.3), transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 135, 135, 0.3), transparent 50%),
                radial-gradient(circle at 40% 90%, rgba(135, 206, 235, 0.3), transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        .app-container {
            position: relative;
            z-index: 1;
            max-width: 1600px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 40px 50px;
            margin-bottom: 30px;
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .header-content {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5em;
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.3);
            flex-shrink: 0;
        }

        .header-text h1 {
            font-size: 2.2em;
            color: var(--gray-900);
            margin-bottom: 8px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .header-text p {
            color: var(--gray-600);
            font-size: 1.1em;
            font-weight: 500;
        }

        .main-grid {
            display: grid;
            grid-template-columns: 480px 1fr;
            gap: 30px;
            align-items: start;
        }

        .card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }

        .card-header {
            padding: 30px 35px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
        }

        .card-header h2 {
            font-size: 1.6em;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .card-header p {
            opacity: 0.9;
            font-size: 0.95em;
        }

        .card-body {
            padding: 35px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: var(--gray-700);
            font-weight: 600;
            font-size: 0.95em;
            letter-spacing: 0.3px;
        }

        .required {
            color: var(--danger);
            margin-left: 3px;
        }

        input, select, textarea {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            font-size: 1em;
            font-family: inherit;
            transition: all 0.3s ease;
            background: white;
            color: var(--gray-900);
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%234b5563' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            padding-right: 45px;
        }

        textarea {
            resize: vertical;
            min-height: 120px;
            line-height: 1.6;
        }

        .readonly-field {
            background: var(--gray-50);
            color: var(--gray-600);
            cursor: not-allowed;
        }

        .btn-primary {
            width: 100%;
            padding: 16px 30px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1em;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.3);
            margin-top: 10px;
            letter-spacing: 0.5px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(37, 99, 235, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid var(--gray-100);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5em;
            margin-bottom: 15px;
        }

        .stat-total { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-pending { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stat-responded { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .stat-overdue { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }

        .stat-value {
            font-size: 2.5em;
            font-weight: 800;
            color: var(--gray-900);
            line-height: 1;
            margin-bottom: 5px;
        }

        .stat-label {
            color: var(--gray-600);
            font-size: 0.9em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .search-container {
            position: relative;
            margin-bottom: 25px;
        }

        .search-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-600);
            pointer-events: none;
        }

        #searchInput {
            padding-left: 50px;
            font-size: 1em;
        }

        .requests-container {
            max-height: 700px;
            overflow-y: auto;
            padding-right: 10px;
        }

        .requests-container::-webkit-scrollbar {
            width: 8px;
        }

        .requests-container::-webkit-scrollbar-track {
            background: var(--gray-100);
            border-radius: 10px;
        }

        .requests-container::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: 10px;
        }

        .requests-container::-webkit-scrollbar-thumb:hover {
            background: var(--gray-600);
        }

        .request-item {
            background: white;
            border: 2px solid var(--gray-100);
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .request-item:hover {
            border-color: var(--primary);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.15);
            transform: translateX(5px);
        }

        .request-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--gray-100);
        }

        .request-id {
            font-size: 1.2em;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: 0.5px;
        }

        .status-badge {
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .status-pending {
            background: linear-gradient(135deg, #fef3c7 0%, #fcd34d 100%);
            color: #92400e;
        }

        .status-sent {
            background: linear-gradient(135deg, #dbeafe 0%, #60a5fa 100%);
            color: #1e3a8a;
        }

        .status-responded {
            background: linear-gradient(135deg, #d1fae5 0%, #34d399 100%);
            color: #065f46;
        }

        .status-overdue {
            background: linear-gradient(135deg, #fee2e2 0%, #f87171 100%);
            color: #7f1d1d;
        }

        .request-details {
            display: grid;
            gap: 12px;
        }

        .detail-item {
            display: flex;
            align-items: start;
            gap: 12px;
        }

        .detail-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--gray-100);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.1em;
        }

        .detail-content {
            flex: 1;
        }

        .detail-label {
            font-size: 0.8em;
            color: var(--gray-600);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .detail-value {
            color: var(--gray-900);
            font-weight: 600;
            font-size: 1em;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid var(--gray-100);
        }

        .btn-action {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.9em;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-sent {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
        }

        .btn-responded {
            background: linear-gradient(135deg, var(--success) 0%, #10b981 100%);
            color: white;
        }

        .btn-delete {
            background: linear-gradient(135deg, var(--danger) 0%, #ef4444 100%);
            color: white;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .empty-state {
            text-align: center;
            padding: 80px 40px;
            color: var(--gray-600);
        }

        .empty-icon {
            font-size: 5em;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .empty-state h3 {
            font-size: 1.5em;
            color: var(--gray-700);
            margin-bottom: 10px;
        }

        .empty-state p {
            font-size: 1em;
        }

        .notification {
            position: fixed;
            top: 30px;
            right: 30px;
            background: white;
            padding: 20px 30px;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 15px;
            animation: slideInRight 0.4s ease;
            border-left: 5px solid var(--success);
            max-width: 400px;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(500px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .notification-icon {
            font-size: 1.8em;
        }

        .notification-text {
            font-weight: 600;
            color: var(--gray-900);
        }

        @media (max-width: 1400px) {
            .main-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
            }

            .header-text h1 {
                font-size: 1.8em;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .card-body {
                padding: 25px;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <header>
            <div class="header-content">
                <div class="logo">📋</div>
                <div class="header-text">
                    <h1>Investigation Request Management System</h1>
                    <p>Consumer Affairs Department - Service Provider Investigations</p>
                </div>
            </div>
        </header>

        <div class="main-grid">
            <!-- Form Section -->
            <div class="card">
                <div class="card-header">
                    <h2>New Investigation Request</h2>
                    <p>Submit a formal investigation to service providers</p>
                </div>
                <div class="card-body">
                    <form id="requestForm">
                        <div class="form-group">
                            <label>Case Reference Number</label>
                            <input type="text" id="caseNumber" class="readonly-field" readonly placeholder="AUTO-GENERATED">
                        </div>
                        <div class="form-group">
                            <label>Service Provider <span class="required">*</span></label>
                            <select id="providerName" required>
                                <option value="">Select Service Provider</option>
                                <option value="Airtel Malawi">Airtel Malawi</option>
                                <option value="TNM (Telekom Networks Malawi)">TNM (Telekom Networks Malawi)</option>
                                <option value="Access Communications">Access Communications</option>
                                <option value="Malawi Posts Corporation">Malawi Posts Corporation</option>
                                <option value="Malawi Telecommunications Limited (MTL)">Malawi Telecommunications Limited (MTL)</option>
                                <option value="ESCOM (Electricity Supply Corporation)">ESCOM (Electricity Supply Corporation)</option>
                                <option value="Lilongwe Water Board">Lilongwe Water Board</option>
                                <option value="Blantyre Water Board">Blantyre Water Board</option>
                                <option value="Northern Region Water Board">Northern Region Water Board</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Investigation Type <span class="required">*</span></label>
                            <select id="investigationType" required>
                                <option value="">Select Investigation Type</option>
                                <option value="Consumer Complaint">Consumer Complaint</option>
                                <option value="Service Quality">Service Quality</option>
                                <option value="Billing Dispute">Billing Dispute</option>
                                <option value="Contract Violation">Contract Violation</option>
                                <option value="Network Issues">Network Issues</option>
                                <option value="Fraud Investigation">Fraud Investigation</option>
                                <option value="Regulatory Compliance">Regulatory Compliance</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        

                        <div class="form-group">
                            <label>Investigation Details <span class="required">*</span></label>
                            <textarea id="description" required placeholder="Provide detailed information about the investigation request..."></textarea>
                        </div>

                        <div class="form-group">
                            <label>Response Deadline <span class="required">*</span></label>
                            <input type="date" id="deadline" required>
                        </div>

                        <div class="form-group">
                            <label>Investigation Officer <span class="required">*</span></label>
                            <select id="officer" required>
                                <option value="">Select Investigation Officer</option>
                                <option value="John Banda">John Banda</option>
                                <option value="Grace Mwale">Grace Mwale</option>
                                <option value="Michael Phiri">Michael Phiri</option>
                                <option value="Thandiwe Nyirenda">Thandiwe Nyirenda</option>
                                <option value="Patrick Chirwa">Patrick Chirwa</option>
                                <option value="Mercy Kamanga">Mercy Kamanga</option>
                                <option value="Daniel Tembo">Daniel Tembo</option>
                                <option value="Faith Kachale">Faith Kachale</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-primary">📨 Submit Investigation Request</button>
                    </form>
                </div>
            </div>

            <!-- Tracker Section -->
            <div class="card">
                <div class="card-header">
                    <h2>Active Investigations Tracker</h2>
                    <p>Monitor and manage all investigation requests</p>
                </div>
                <div class="card-body">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon stat-total">📊</div>
                            <div class="stat-value" id="totalRequests">0</div>
                            <div class="stat-label">Total</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon stat-pending">⏳</div>
                            <div class="stat-value" id="pendingRequests">0</div>
                            <div class="stat-label">Pending</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon stat-responded">✅</div>
                            <div class="stat-value" id="respondedRequests">0</div>
                            <div class="stat-label">Responded</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon stat-overdue">⚠️</div>
                            <div class="stat-value" id="overdueRequests">0</div>
                            <div class="stat-label">Overdue</div>
                        </div>
                    </div>

                    <div class="search-container">
                        <span class="search-icon">🔍</span>
                        <input type="text" id="searchInput" placeholder="Search by provider, case number, or officer...">
                    </div>

                    <div class="requests-container" id="requestsList">
                        <div class="empty-state">
                            <div class="empty-icon">📄</div>
                            <h3>No Investigation Requests</h3>
                            <p>Create your first investigation request to begin tracking</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let requests = [];
        let requestCounter = 1;

        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('deadline').min = today;

        // Form submission
        document.getElementById('requestForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const request = {
                id: `INV-${new Date().getFullYear()}-${String(requestCounter++).padStart(4, '0')}`,
                providerName: document.getElementById('providerName').value,
                investigationType: document.getElementById('investigationType').value,
                description: document.getElementById('description').value,
                deadline: document.getElementById('deadline').value,
                officer: document.getElementById('officer').value,
                status: 'pending',
                dateCreated: today,
                dateSent: null,
                dateResponded: null
            };

            requests.unshift(request);
            renderRequests();
            updateStats();
            this.reset();
            showNotification('✅ Investigation request created successfully!');
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const filtered = requests.filter(req => 
                req.providerName.toLowerCase().includes(searchTerm) ||
                req.id.toLowerCase().includes(searchTerm) ||
                req.officer.toLowerCase().includes(searchTerm)
            );
            renderRequests(filtered);
        });

        function renderRequests(requestsToRender = requests) {
            const container = document.getElementById('requestsList');
            
            if (requestsToRender.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-icon">📄</div>
                        <h3>No Requests Found</h3>
                        <p>Try adjusting your search criteria</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = requestsToRender.map(req => `
                <div class="request-item">
                    <div class="request-header">
                        <div class="request-id">${req.id}</div>
                        <div class="status-badge status-${getStatus(req)}">${getStatusLabel(req)}</div>
                    </div>
                    <div class="request-details">
                        <div class="detail-item">
                            <div class="detail-icon">🏢</div>
                            <div class="detail-content">
                                <div class="detail-label">Service Provider</div>
                                <div class="detail-value">${req.providerName}</div>
                            </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-icon">📑</div>
                            <div class="detail-content">
                                <div class="detail-label">Investigation Type</div>
                                <div class="detail-value">${req.investigationType}</div>
                            </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-icon">👤</div>
                            <div class="detail-content">
                                <div class="detail-label">Investigation Officer</div>
                                <div class="detail-value">${req.officer}</div>
                            </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-icon">⏰</div>
                            <div class="detail-content">
                                <div class="detail-label">Response Deadline</div>
                                <div class="detail-value">${formatDate(req.deadline)} (${getDaysRemaining(req.deadline)})</div>
                            </div>
                        </div>
                    </div>
                    <div class="action-buttons">
                        ${req.status === 'pending' ? `<button class="btn-action btn-sent" onclick="markAsSent('${req.id}')">📤 Mark as Sent</button>` : ''}
                        ${req.status === 'sent' ? `<button class="btn-action btn-responded" onclick="markAsResponded('${req.id}')">✅ Mark as Responded</button>` : ''}
                        <button class="btn-action btn-delete" onclick="deleteRequest('${req.id}')">🗑️ Delete</button>
                    </div>
                </div>
            `).join('');
        }

        function getStatus(req) {
            if (req.status === 'responded') return 'responded';
            if (req.status === 'sent') {
                const daysRemaining = Math.floor((new Date(req.deadline) - new Date()) / (1000 * 60 * 60 * 24));
                return daysRemaining < 0 ? 'overdue' : 'sent';
            }
            return 'pending';
        }

        function getStatusLabel(req) {
            const status = getStatus(req);
            const labels = {
                'pending': 'Pending',
                'sent': 'Sent',
                'responded': 'Responded',
                'overdue': 'Overdue'
            };
            return labels[status];
        }

        function getDaysRemaining(deadline) {
            const days = Math.floor((new Date(deadline) - new Date()) / (1000 * 60 * 60 * 24));
            if (days < 0) return `${Math.abs(days)} days overdue`;
            if (days === 0) return 'Due today';
            if (days === 1) return '1 day remaining';
            return `${days} days remaining`;
        }

        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-GB', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            });
        }

        function markAsSent(id) {
            const req = requests.find(r => r.id === id);
            if (req) {
                req.status = 'sent';
                req.dateSent = new Date().toISOString().split('T')[0];
                renderRequests();
                updateStats();
                showNotification('📤 Request marked as sent!');
            }
        }

        function markAsResponded(id) {
            const req = requests.find(r => r.id === id);
            if (req) {
                req.status = 'responded';
                req.dateResponded = new Date().toISOString().split('T')[0];
                renderRequests();
                updateStats();
                showNotification('✅ Request marked as responded!');
            }
        }

        function deleteRequest(id) {
            if (confirm('Are you sure you want to delete this investigation request? This action cannot be undone.')) {
                requests = requests.filter(r