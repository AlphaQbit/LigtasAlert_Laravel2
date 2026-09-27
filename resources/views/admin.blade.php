@extends('layouts.admin')

@section('content')
<div class="header">
        <div class="nav-container">
            <div class="nav-header">
                <div class="header-title">
                    🚨 LigtasAlert Admin
                </div>
                <div class="facility-selector">
                    <label for="facility">Facility:</label>
                    <select id="facility">
                        <option value="all">All Facilities</option>
                    </select>
                </div>
            </div>
            <div class="nav-tabs">
                <button class="nav-tab active" data-tab="dashboard">Dashboard</button>
                <button class="nav-tab" data-tab="contacts">Contacts</button>
                <button class="nav-tab" data-tab="history">History</button>
            </div>
        </div>
    </div>

    <div class="container">
        <div id="dashboardTab" class="tab-content active">
            <div class="stats-grid">
                <div class="stat-card active">
                    <div class="stat-number" id="activeCount">-</div>
                    <div class="stat-label">Active Alerts</div>
                </div>
                <div class="stat-card standby">
                    <div class="stat-number" id="respondersCount">-</div>
                    <div class="stat-label">Responders On Duty</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="resolvedCount">-</div>
                    <div class="stat-label">Resolved Today</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="avgResponseTime">-</div>
                    <div class="stat-label">Avg Response Time</div>
                </div>
            </div>

            <div class="alerts-section">
                <div class="section-title">Active Incidents</div>
                <div class="alerts-grid" id="alertsGrid">
                    <div class="loading">Loading alerts...</div>
                </div>
            </div>

            <div class="alerts-section">
                <div class="section-title">Recent Activity</div>
                <div class="activity-log" id="activityLog">
                    <div class="loading">Loading activity...</div>
                </div>
            </div>
        </div>

        <div id="contactsTab" class="tab-content" style="display:none;">
            <div class="alerts-section">
                <div class="section-title">Emergency Contacts</div>
                <div class="contacts-grid" id="contactsGrid">
                    <div class="loading">Loading contacts...</div>
                </div>
            </div>

            <div class="alerts-section">
                <div class="section-title">Facilities & Zones</div>
                <div class="facilities-grid" id="facilitiesGrid">
                    <div class="loading">Loading facilities...</div>
                </div>
            </div>
        </div>

        <div id="historyTab" class="tab-content" style="display:none;">
            <div class="alerts-section">
                <div class="section-title">Alert History</div>
                <div class="history-grid" id="historyGrid">
                    <div class="loading">Loading history...</div>
                </div>
            </div>

            <div class="alerts-section">
                <div class="section-title">Response Timeline</div>
                <div class="timeline" id="timeline">
                    <div class="loading">Loading timeline...</div>
                </div>
            </div>
        </div>
    </div>
@endsection