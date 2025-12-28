/**
 * Waslah Admin Panel JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {

    // Confirm delete actions
    document.querySelectorAll('[data-confirm]').forEach(function(el) {
        el.addEventListener('click', function(e) {
            if (!confirm(this.dataset.confirm || 'Are you sure?')) {
                e.preventDefault();
            }
        });
    });

    // Image preview
    document.querySelectorAll('input[type="file"][data-preview]').forEach(function(input) {
        input.addEventListener('change', function() {
            const preview = document.querySelector(this.dataset.preview);
            if (preview && this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    });

    // Auto-generate slug from name
    const nameInput = document.querySelector('input[name="name"]');
    const slugInput = document.querySelector('input[name="slug"]');

    if (nameInput && slugInput && !slugInput.value) {
        nameInput.addEventListener('input', function() {
            slugInput.value = this.value
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-|-$/g, '');
        });
    }

    // Select all checkbox
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('input[name="selected[]"]').forEach(function(cb) {
                cb.checked = selectAll.checked;
            });
        });
    }

    // Bulk actions
    const bulkActionForm = document.getElementById('bulkActionForm');
    if (bulkActionForm) {
        bulkActionForm.addEventListener('submit', function(e) {
            const selected = document.querySelectorAll('input[name="selected[]"]:checked');
            if (selected.length === 0) {
                e.preventDefault();
                alert('Please select at least one item');
            }
        });
    }

    // Order status update
    window.updateOrderStatus = function(orderId, status) {
        if (!confirm('Update order status to ' + status + '?')) return;

        fetch(SITE_URL + '/admin/orders/status/' + orderId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'status=' + status
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error updating status');
            }
        });
    };

    // Shipment status simulation
    window.simulateShipment = function(shipmentId) {
        fetch(SITE_URL + '/admin/shipments/simulate/' + shipmentId, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    };

    // Charts (if using Chart.js later)
    // ...

});
