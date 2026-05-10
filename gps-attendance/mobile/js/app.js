const API_BASE = '../api/routes';
let authToken = localStorage.getItem('token');
let currentUser = null;
let currentPosition = null;
let activeAttendanceId = null;
let isOnline = navigator.onLine;

function updateOnlineStatus() {
    isOnline = navigator.onLine;
    document.getElementById('offlineBadge').style.display = isOnline ? 'none' : 'block';
    updateSyncIndicator();
}

window.addEventListener('online', updateOnlineStatus);
window.addEventListener('offline', updateOnlineStatus);

function updateSyncIndicator() {
    const indicator = document.getElementById('syncIndicator');
    if (!isOnline) {
        indicator.innerHTML = '<span class="badge bg-warning"><i class="bi bi-cloud-slash"></i> Offline</span>';
    } else {
        indicator.innerHTML = '<span class="badge bg-secondary"><i class="bi bi-cloud-check"></i> Synced</span>';
    }
}

async function login() {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const errorDiv = document.getElementById('loginError');

    if (!username || !password) {
        errorDiv.textContent = 'Please enter username and password';
        errorDiv.classList.remove('d-none');
        return;
    }

    try {
        const response = await fetch(API_BASE + '/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password })
        });

        const data = await response.json();

        if (data.token) {
            authToken = data.token;
            localStorage.setItem('token', authToken);
            localStorage.setItem('user', JSON.stringify(data.user));
            currentUser = data.user;
            showDashboard();
        } else {
            errorDiv.textContent = data.error || 'Login failed';
            errorDiv.classList.remove('d-none');
        }
    } catch (e) {
        errorDiv.textContent = 'Connection error. Please try again.';
        errorDiv.classList.remove('d-none');
    }
}

function logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    authToken = null;
    currentUser = null;
    document.getElementById('dashboardPage').classList.add('d-none');
    document.getElementById('loginPage').classList.remove('d-none');
}

function showDashboard() {
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    document.getElementById('loginPage').classList.add('d-none');
    document.getElementById('dashboardPage').classList.remove('d-none');
    document.getElementById('userName').textContent = user.employee_name || 'Employee';
    document.getElementById('userDept').textContent = user.role || '';

    loadSites();
    loadAttendanceHistory();
    checkActiveSession();
    getLocation();
}

async function loadSites() {
    try {
        const response = await fetch(API_BASE + '/admin-sites', {
            headers: { 'Authorization': 'Bearer ' + authToken }
        });
        const data = await response.json();
        const select = document.getElementById('siteSelect');
        select.innerHTML = '';
        if (data.sites && data.sites.length > 0) {
            data.sites.forEach(site => {
                select.innerHTML += `<option value="${site.id}">${site.name}</option>`;
            });
        } else {
            select.innerHTML = '<option value="">No sites configured</option>';
        }
    } catch (e) {
        console.error('Failed to load sites:', e);
    }
}

async function checkActiveSession() {
    try {
        const response = await fetch(API_BASE + '/history?emp_id=' + (currentUser?.employee_id || ''), {
            headers: { 'Authorization': 'Bearer ' + authToken }
        });
        const data = await response.json();
        const openRecord = (data.records || []).find(r => !r.check_out_time);
        if (openRecord) {
            activeAttendanceId = openRecord.id;
            document.getElementById('checkinBtn').innerHTML = '<i class="bi bi-x-circle"></i> Check Out';
            document.getElementById('checkinBtn').classList.add('checked-in');
            document.getElementById('activeSession').classList.remove('d-none');
            document.getElementById('checkinTime').textContent = openRecord.check_in_time;
            document.getElementById('checkinLocation').textContent = openRecord.site_name;
        }
    } catch (e) {
        console.error('Failed to check active session:', e);
    }
}

async function loadAttendanceHistory() {
    const list = document.getElementById('attendanceList');
    try {
        const response = await fetch(API_BASE + '/history?emp_id=' + (currentUser?.employee_id || ''), {
            headers: { 'Authorization': 'Bearer ' + authToken }
        });
        const data = await response.json();
        if (data.records && data.records.length > 0) {
            list.innerHTML = data.records.slice(0, 10).map(r => `
                <div class="border-bottom py-2">
                    <div class="d-flex justify-content-between">
                        <strong>${r.site_name || 'Site'}</strong>
                        <span class="badge ${r.check_out_time ? 'bg-success' : 'bg-warning'}">
                            ${r.check_out_time ? 'Complete' : 'Checked In'}
                        </span>
                    </div>
                    <small class="text-muted">
                        ${r.check_in_time}${r.check_out_time ? ' - ' + r.check_out_time : ''}
                    </small>
                </div>
            `).join('');
        } else {
            list.innerHTML = '<p class="text-muted text-center">No recent attendance</p>';
        }
    } catch (e) {
        list.innerHTML = '<p class="text-danger text-center">Failed to load history</p>';
    }
}

function getLocation() {
    const statusEl = document.getElementById('locationStatus');
    if (!navigator.geolocation) {
        statusEl.innerHTML = '<i class="bi bi-x-circle text-danger"></i><p>Geolocation not supported</p>';
        return;
    }

    statusEl.innerHTML = '<i class="bi bi-hourglass-split"></i><p>Getting location...</p>';

    navigator.geolocation.getCurrentPosition(
        (pos) => {
            currentPosition = {
                lat: pos.coords.latitude,
                lng: pos.coords.longitude
            };
            statusEl.innerHTML = `<i class="bi bi-check-circle text-success"></i>
                <p class="mb-0"><strong>${currentPosition.lat.toFixed(6)}, ${currentPosition.lng.toFixed(6)}</strong></p>
                <small class="text-muted">Accuracy: ${pos.coords.accuracy.toFixed(0)}m</small>`;
        },
        (err) => {
            statusEl.innerHTML = `<i class="bi bi-x-circle text-danger"></i><p>Location error: ${err.message}</p>`;
        },
        { enableHighAccuracy: true, timeout: 15000 }
    );
}

async function handleCheckIn() {
    if (!currentPosition) {
        alert('Please wait for GPS location');
        getLocation();
        return;
    }

    const siteId = document.getElementById('siteSelect').value;
    if (!siteId) {
        alert('Please select a site');
        return;
    }

    const btn = document.getElementById('checkinBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass"></i> Processing...';

    const payload = {
        site_id: parseInt(siteId),
        latitude: currentPosition.lat,
        longitude: currentPosition.lng
    };

    try {
        const endpoint = activeAttendanceId ? '/checkout' : '/checkin';
        const body = activeAttendanceId ? { ...payload, attendance_id: activeAttendanceId } : payload;

        const response = await fetch(API_BASE + endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + authToken
            },
            body: JSON.stringify(body)
        });

        const result = await response.json();

        if (result.success) {
            alert(activeAttendanceId ? 'Checked out successfully!' : 'Checked in successfully!');
            if (!activeAttendanceId && result.attendance_id) {
                localStorage.setItem('lastAttendanceId', result.attendance_id);
            }
            location.reload();
        } else {
            alert(result.error || 'Operation failed');
        }
    } catch (e) {
        if (!isOnline) {
            const queue = JSON.parse(localStorage.getItem('offlineQueue') || '[]');
            queue.push({
                type: activeAttendanceId ? 'checkout' : 'checkin',
                payload,
                timestamp: Date.now()
            });
            localStorage.setItem('offlineQueue', JSON.stringify(queue));
            alert('Saved offline. Will sync when online.');
        } else {
            alert('Connection error: ' + e.message);
        }
    }

    btn.disabled = false;
}

async function syncOfflineQueue() {
    if (!isOnline) return;
    const queue = JSON.parse(localStorage.getItem('offlineQueue') || '[]');
    if (queue.length === 0) return;

    for (const item of queue) {
        try {
            const endpoint = item.type === 'checkout' ? '/checkout' : '/checkin';
            await fetch(API_BASE + endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + authToken
                },
                body: JSON.stringify(item.payload)
            });
        } catch (e) {
            console.error('Sync failed for item:', item);
        }
    }

    localStorage.removeItem('offlineQueue');
}

setInterval(syncOfflineQueue, 30000);

if (authToken) {
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    if (user.token || authToken) {
        showDashboard();
    }
}

updateOnlineStatus();