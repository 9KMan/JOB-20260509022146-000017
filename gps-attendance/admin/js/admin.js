const API_BASE = '../api';

async function deleteEmployee(id) {
    if (!confirm('Delete this employee?')) return;
    try {
        const response = await fetch(API_BASE + '/admin/employees', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });
        const result = await response.json();
        if (result.success) {
            alert('Employee deleted');
            location.reload();
        } else {
            alert('Error: ' + (result.error || 'Unknown error'));
        }
    } catch (err) {
        alert('Request failed: ' + err.message);
    }
}

async function editEmployee(id) {
    window.location.href = 'employee-edit.php?id=' + id;
}

async function deleteSite(id) {
    if (!confirm('Delete this site?')) return;
    try {
        const response = await fetch(API_BASE + '/admin/sites', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });
        const result = await response.json();
        if (result.success) {
            alert('Site deleted');
            location.reload();
        } else {
            alert('Error: ' + (result.error || 'Unknown error'));
        }
    } catch (err) {
        alert('Request failed: ' + err.message);
    }
}

function openAddSiteModal() {
    const modal = new bootstrap.Modal(document.getElementById('addSiteModal'));
    modal.show();
}