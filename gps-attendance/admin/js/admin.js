document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form[data-api]');
    forms.forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const action = form.dataset.api;
            const method = form.dataset.method || 'POST';
            const data = new FormData(form);
            const body = Object.fromEntries(data.entries());
            
            try {
                const response = await fetch(action, {
                    method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(body)
                });
                const result = await response.json();
                if (result.success) {
                    alert('Success!');
                    location.reload();
                } else {
                    alert('Error: ' + (result.error || 'Unknown error'));
                }
            } catch (err) {
                alert('Request failed: ' + err.message);
            }
        });
    });
});
