const API_BASE = '/api';
let refreshInterval;

async function fetchAPI(endpoint, options = {}) {
    try {
        const response = await fetch(`${API_BASE}/${endpoint}`, {
            headers: {
                'Content-Type': 'application/json',
            },
            ...options
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error('API Error:', error);
        showToast('Connection error. Retrying...', 'error');
        return null;
    }
}

async function loadStats() {
    const data = await fetchAPI('stats');
    if (data) {
        document.getElementById('activeCount').textContent = data.active_count || 0;
        document.getElementById('respondersCount').textContent = data.responders_on_duty || 0;
        document.getElementById('resolvedCount').textContent = data.resolved_today || 0;
    }
}

async function loadFacilities() {
    const data = await fetchAPI('facilities');
    if (data && Array.isArray(data)) {
        const select = document.getElementById('facility');
        data.forEach(f => {
            const opt = document.createElement('option');
            opt.value = f.id;
            opt.textContent = f.name;
            select.appendChild(opt);
        });
    }
}

async function loadAlerts() {
    const facility = document.getElementById('facility').value;
    const endpoint = facility === 'all'
        ? 'alerts?status=active'
        : `alerts?status=active&facility=${facility}`;

    const data = await fetchAPI(endpoint);
    const grid = document.getElementById('alertsGrid');

    if (!data) {
        grid.innerHTML = '<div style="text-align:center;padding:2rem;color:var(--text-secondary);">Failed to load alerts. Check console.</div>';
        return;
    }

    if (data.alerts.length === 0) {
        grid.innerHTML = '<div style="text-align:center;padding:2rem;color:var(--text-secondary);">No active alerts</div>';
        return;
    }

    grid.innerHTML = data.alerts.map(alert => `
        <div class="alert-card ${alert.status}" data-id="${alert.id}">
            <div class="alert-header">
                <div class="alert-type">${alert.type}</div>
                <span class="badge ${alert.status}">${alert.status}</span>
            </div>
            <div class="alert-details">
                <div class="detail-row">
                    <span class="detail-label">Location:</span>
                    <span class="detail-value">${alert.room}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Reported:</span>
                    <span class="detail-value">${formatTime(alert.created_at)}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Recipients:</span>
                    <span class="detail-value">${alert.recipients}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Responders:</span>
                    <span class="detail-value">${alert.responders?.length || 0}</span>
                </div>
            </div>
            <div class="alert-actions">
                <button class="btn primary" onclick="acknowledgeAlert('${alert.id}')" ${alert.status !== 'active' ? 'disabled' : ''}>
                    Acknowledge
                </button>
                <button class="btn danger" onclick="resolveAlert('${alert.id}')" ${alert.status === 'resolved' ? 'disabled' : ''}>
                    Resolve
                </button>
            </div>
        </div>
    `).join('');
}

async function acknowledgeAlert(id) {
    const data = await fetchAPI(`alerts/${id}`, {
        method: 'PUT',
        body: JSON.stringify({
            status: 'acknowledged',
            acknowledged_by: 'Admin'
        })
    });

    if (data) {
        showToast('Alert acknowledged', 'success');
        loadAlerts();
    }
}

async function resolveAlert(id) {
    const data = await fetchAPI(`alerts/${id}`, {
        method: 'PUT',
        body: JSON.stringify({ status: 'resolved' })
    });

    if (data) {
        showToast('Alert resolved', 'success');
        loadAlerts();
        loadStats();
    }
}

function formatTime(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000);

    if (diff < 60) return 'Just now';
    if (diff < 3600) return `${Math.floor(diff / 60)} min ago`;
    if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;

    return date.toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit'
    });
}

function showToast(message, type = 'success') {
    const existing = document.querySelector('.toast');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => toast.remove(), 3000);
}

function switchTab(tabName) {
    // Update nav tabs
    document.querySelectorAll('.nav-tab').forEach(t => {
        t.classList.toggle('active', t.dataset.tab === tabName);
    });

    // Update content
    document.querySelectorAll('.tab-content').forEach(content => {
        content.style.display = 'none';
    });
    document.getElementById(`${tabName}Tab`).style.display = 'block';

    // Load tab data
    if (tabName === 'contacts') {
        loadContacts();
        loadFacilitiesList();
    } else if (tabName === 'history') {
        loadHistory();
        loadTimeline();
    }
}

async function loadContacts() {
    const grid = document.getElementById('contactsGrid');
    const contacts = [
        { name: 'John Smith', role: 'Building A - Floor 1', online: true, initials: 'JS' },
        { name: 'Maria Garcia', role: 'Building A - Floor 2', online: true, initials: 'MG' },
        { name: 'David Lee', role: 'Building A - Floor 3', online: false, initials: 'DL' },
        { name: 'Sarah Johnson', role: 'Building B - Security', online: true, initials: 'SJ' },
        { name: 'Mike Chen', role: 'Campus Maintenance', online: false, initials: 'MC' },
        { name: 'Lisa Wong', role: 'Building B - Floor 1', online: true, initials: 'LW' }
    ];

    grid.innerHTML = contacts.map(c => `
        <div class="contact-card">
            <div class="contact-avatar">${c.initials}</div>
            <div class="contact-info">
                <div class="contact-name">${c.name}</div>
                <div class="contact-role">${c.role}</div>
            </div>
            <div class="contact-status ${c.online ? 'online' : 'offline'}" title="${c.online ? 'Online' : 'Offline'}"></div>
        </div>
    `).join('');
}

async function loadFacilitiesList() {
    const grid = document.getElementById('facilitiesGrid');
    const data = await fetchAPI('stats');

    grid.innerHTML = `
        <div class="facility-card">
            <div class="facility-name">Building A</div>
            <div class="facility-stats">3 Floors • 24 Rooms</div>
        </div>
        <div class="facility-card">
            <div class="facility-name">Building B</div>
            <div class="facility-stats">4 Floors • 32 Rooms</div>
        </div>
        <div class="facility-card">
            <div class="facility-name">Campus</div>
            <div class="facility-stats">All Zones • Common Areas</div>
        </div>
    `;
}

async function loadHistory() {
    const grid = document.getElementById('historyGrid');
    const allAlerts = await fetchAPI('alerts');

    if (!allAlerts || allAlerts.alerts.length === 0) {
        grid.innerHTML = '<div class="loading">No alert history</div>';
        return;
    }

    grid.innerHTML = allAlerts.alerts.map(alert => `
        <div class="history-item">
            <div class="history-left">
                <span class="history-type ${alert.type.toLowerCase()}">${alert.type}</span>
                <span class="history-location">${alert.room}</span>
            </div>
            <span class="history-time">${formatTime(alert.created_at)}</span>
        </div>
    `).join('');
}

async function loadTimeline() {
    const timeline = document.getElementById('timeline');
    const allAlerts = await fetchAPI('alerts');

    const events = [
        { time: '2:45 PM', event: 'Fire alert resolved in Room 214', type: 'resolved' },
        { time: '2:30 PM', event: 'Evacuation order issued for Campus Wide', type: 'alert' },
        { time: '2:28 PM', event: 'Lockdown lifted - Building B East Wing', type: 'resolved' },
        { time: '2:25 PM', event: 'Custom alert sent to Room 101', type: 'alert' },
        { time: '2:20 PM', event: 'All-clear signal broadcast - Building A', type: 'resolved' }
    ];

    timeline.innerHTML = events.map(e => `
        <div class="timeline-item">
            <div class="timeline-time">${e.time}</div>
            <div class="timeline-event">${e.event}</div>
        </div>
    `).join('');
}

function init() {
    loadFacilities();
    loadStats();
    loadAlerts();

    // Tab navigation
    document.querySelectorAll('.nav-tab').forEach(tab => {
        tab.addEventListener('click', () => switchTab(tab.dataset.tab));
    });

    // Facility filter
    document.getElementById('facility').addEventListener('change', loadAlerts);

    // Poll for new alerts every 500ms for instant updates
    refreshInterval = setInterval(() => {
        loadStats();
        loadAlerts();
    }, 500);
}

init();
