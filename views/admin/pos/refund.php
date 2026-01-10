<?php
/**
 * POS Refund View
 * Process refunds for POS transactions
 */
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">POS Refund</h4>
                    <p class="text-muted mb-0">Process refunds for POS transactions</p>
                </div>
                <a href="<?= url('admin/pos') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to POS
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Search Transaction -->
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-search me-2 text-primary"></i>Find Transaction
                    </h5>
                </div>
                <div class="card-body">
                    <form id="searchTransactionForm">
                        <div class="mb-3">
                            <label class="form-label">Transaction Number</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-receipt"></i></span>
                                <input type="text" class="form-control" id="transactionNumber"
                                       placeholder="Enter transaction number..." autofocus>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <small class="text-muted">e.g., TXN-20240115-001</small>
                        </div>
                    </form>

                    <hr>

                    <h6 class="text-muted mb-3">Or Search By:</h6>

                    <div class="mb-3">
                        <label class="form-label">Customer Phone</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="text" class="form-control" id="customerPhone"
                                   placeholder="Enter phone number...">
                            <button type="button" class="btn btn-outline-primary" onclick="searchByPhone()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date Range</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="date" class="form-control" id="dateFrom"
                                       value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-6">
                                <input type="date" class="form-control" id="dateTo"
                                       value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline-secondary w-100" onclick="searchByDate()">
                        <i class="fas fa-calendar-alt me-2"></i>Search by Date
                    </button>
                </div>
            </div>
        </div>

        <!-- Transaction Details -->
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-invoice me-2 text-info"></i>Transaction Details
                    </h5>
                </div>
                <div class="card-body">
                    <!-- No Transaction Selected -->
                    <div id="noTransaction" class="text-center py-5">
                        <i class="fas fa-receipt fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">No Transaction Selected</h5>
                        <p class="text-muted mb-0">Search for a transaction to process a refund</p>
                    </div>

                    <!-- Transaction Found -->
                    <div id="transactionDetails" style="display: none;">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <small class="text-muted d-block">Transaction #</small>
                                <strong id="txnNumber">-</strong>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <small class="text-muted d-block">Date</small>
                                <strong id="txnDate">-</strong>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <small class="text-muted d-block">Customer</small>
                                <strong id="txnCustomer">-</strong>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <small class="text-muted d-block">Payment Method</small>
                                <strong id="txnPayment">-</strong>
                            </div>
                        </div>

                        <hr>

                        <h6 class="mb-3">Items</h6>
                        <div class="table-responsive">
                            <table class="table table-sm" id="txnItemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th><input type="checkbox" id="selectAll" onclick="toggleAllItems()"></th>
                                        <th>Item</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-center">Refund Qty</th>
                                    </tr>
                                </thead>
                                <tbody id="txnItems">
                                    <!-- Items will be populated here -->
                                </tbody>
                            </table>
                        </div>

                        <hr>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Refund Reason</label>
                                    <select class="form-select" id="refundReason">
                                        <option value="customer_request">Customer Request</option>
                                        <option value="defective">Defective Product</option>
                                        <option value="wrong_item">Wrong Item</option>
                                        <option value="price_adjustment">Price Adjustment</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Notes</label>
                                    <textarea class="form-control" id="refundNotes" rows="2"
                                              placeholder="Additional notes..."></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Original Total:</span>
                                            <strong id="originalTotal">৳0.00</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Refund Amount:</span>
                                            <strong id="refundAmount" class="text-danger">৳0.00</strong>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <span>Refund Method:</span>
                                            <select class="form-select form-select-sm w-auto" id="refundMethod">
                                                <option value="cash">Cash</option>
                                                <option value="card">Card</option>
                                                <option value="store_credit">Store Credit</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-danger btn-lg" onclick="processRefund()">
                                <i class="fas fa-undo me-2"></i>Process Refund
                            </button>
                        </div>
                    </div>

                    <!-- Transaction List (for date/phone search) -->
                    <div id="transactionList" style="display: none;">
                        <h6 class="mb-3">Select a Transaction</h6>
                        <div class="list-group" id="txnListItems">
                            <!-- Transaction list will be populated here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Refunds -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2 text-warning"></i>Recent Refunds
                    </h5>
                    <span class="badge bg-secondary">Today</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Refund #</th>
                                    <th>Original Transaction</th>
                                    <th>Customer</th>
                                    <th>Reason</th>
                                    <th class="text-end">Amount</th>
                                    <th>Method</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody id="recentRefunds">
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fas fa-check-circle fa-2x mb-2 d-block"></i>
                                        No refunds processed today
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Refund Success Modal -->
<div class="modal fade" id="refundSuccessModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                </div>
                <h4 class="text-success mb-3">Refund Processed!</h4>
                <p class="text-muted mb-0">Refund #: <strong id="successRefundNumber">-</strong></p>
                <p class="text-muted mb-4">Amount: <strong id="successRefundAmount">৳0.00</strong></p>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary" onclick="printRefundReceipt()">
                        <i class="fas fa-print me-2"></i>Print Receipt
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 0.5rem;
}

.card-header {
    border-bottom: 1px solid #eee;
    padding: 1rem 1.25rem;
}

.table th {
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.list-group-item {
    cursor: pointer;
    transition: all 0.2s;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.list-group-item.active {
    background-color: #e7f1ff;
    border-color: #0d6efd;
    color: #0d6efd;
}

#txnItemsTable input[type="number"] {
    width: 70px;
}

#txnItemsTable input[type="checkbox"] {
    width: 18px;
    height: 18px;
}
</style>

<script>
let currentTransaction = null;
let selectedItems = [];

// Search transaction by number
document.getElementById('searchTransactionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const txnNumber = document.getElementById('transactionNumber').value.trim();
    if (txnNumber) {
        searchTransaction(txnNumber);
    }
});

function searchTransaction(txnNumber) {
    fetch(`<?= url('admin/pos/search-transaction') ?>?number=${encodeURIComponent(txnNumber)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.transaction) {
                showTransactionDetails(data.transaction);
            } else {
                alert('Transaction not found');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error searching transaction');
        });
}

function searchByPhone() {
    const phone = document.getElementById('customerPhone').value.trim();
    if (!phone) {
        alert('Please enter a phone number');
        return;
    }

    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;

    fetch(`<?= url('admin/pos/search-transaction') ?>?phone=${encodeURIComponent(phone)}&from=${dateFrom}&to=${dateTo}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.transactions && data.transactions.length > 0) {
                showTransactionList(data.transactions);
            } else {
                alert('No transactions found');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error searching transactions');
        });
}

function searchByDate() {
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;

    fetch(`<?= url('admin/pos/search-transaction') ?>?from=${dateFrom}&to=${dateTo}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.transactions && data.transactions.length > 0) {
                showTransactionList(data.transactions);
            } else {
                alert('No transactions found for this date range');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error searching transactions');
        });
}

function showTransactionList(transactions) {
    document.getElementById('noTransaction').style.display = 'none';
    document.getElementById('transactionDetails').style.display = 'none';
    document.getElementById('transactionList').style.display = 'block';

    const listContainer = document.getElementById('txnListItems');
    listContainer.innerHTML = transactions.map(txn => `
        <a href="#" class="list-group-item list-group-item-action" onclick="selectTransaction('${txn.transaction_number}')">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>${txn.transaction_number}</strong>
                    <small class="text-muted d-block">${txn.customer_name || 'Walk-in Customer'}</small>
                </div>
                <div class="text-end">
                    <strong>৳${parseFloat(txn.total_amount).toFixed(2)}</strong>
                    <small class="text-muted d-block">${txn.created_at}</small>
                </div>
            </div>
        </a>
    `).join('');
}

function selectTransaction(txnNumber) {
    searchTransaction(txnNumber);
}

function showTransactionDetails(transaction) {
    currentTransaction = transaction;

    document.getElementById('noTransaction').style.display = 'none';
    document.getElementById('transactionList').style.display = 'none';
    document.getElementById('transactionDetails').style.display = 'block';

    document.getElementById('txnNumber').textContent = transaction.transaction_number;
    document.getElementById('txnDate').textContent = transaction.created_at;
    document.getElementById('txnCustomer').textContent = transaction.customer_name || 'Walk-in Customer';
    document.getElementById('txnPayment').textContent = transaction.payment_method.toUpperCase();
    document.getElementById('originalTotal').textContent = '৳' + parseFloat(transaction.total_amount).toFixed(2);

    // Populate items
    const itemsContainer = document.getElementById('txnItems');
    itemsContainer.innerHTML = (transaction.items || []).map((item, index) => `
        <tr>
            <td>
                <input type="checkbox" class="item-checkbox" data-index="${index}"
                       onchange="updateRefundAmount()">
            </td>
            <td>
                <strong>${item.product_name}</strong>
                ${item.variant_info ? `<small class="text-muted d-block">${item.variant_info}</small>` : ''}
            </td>
            <td class="text-center">${item.quantity}</td>
            <td class="text-end">৳${parseFloat(item.price).toFixed(2)}</td>
            <td class="text-center">
                <input type="number" class="form-control form-control-sm refund-qty"
                       data-index="${index}" data-max="${item.quantity}" data-price="${item.price}"
                       min="0" max="${item.quantity}" value="0"
                       onchange="updateRefundAmount()" disabled>
            </td>
        </tr>
    `).join('');

    updateRefundAmount();
}

function toggleAllItems() {
    const selectAll = document.getElementById('selectAll').checked;
    const checkboxes = document.querySelectorAll('.item-checkbox');
    const qtyInputs = document.querySelectorAll('.refund-qty');

    checkboxes.forEach((cb, index) => {
        cb.checked = selectAll;
        qtyInputs[index].disabled = !selectAll;
        if (selectAll) {
            qtyInputs[index].value = qtyInputs[index].dataset.max;
        } else {
            qtyInputs[index].value = 0;
        }
    });

    updateRefundAmount();
}

function updateRefundAmount() {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    const qtyInputs = document.querySelectorAll('.refund-qty');
    let total = 0;

    checkboxes.forEach((cb, index) => {
        const qtyInput = qtyInputs[index];
        qtyInput.disabled = !cb.checked;

        if (cb.checked) {
            const qty = parseInt(qtyInput.value) || 0;
            const price = parseFloat(qtyInput.dataset.price) || 0;
            total += qty * price;
        }
    });

    document.getElementById('refundAmount').textContent = '৳' + total.toFixed(2);
}

function processRefund() {
    if (!currentTransaction) {
        alert('No transaction selected');
        return;
    }

    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Please select at least one item to refund');
        return;
    }

    const items = [];
    const qtyInputs = document.querySelectorAll('.refund-qty');

    checkboxes.forEach(cb => {
        const index = parseInt(cb.dataset.index);
        const qty = parseInt(qtyInputs[index].value) || 0;
        if (qty > 0) {
            items.push({
                item_id: currentTransaction.items[index].id,
                product_id: currentTransaction.items[index].product_id,
                quantity: qty,
                price: parseFloat(currentTransaction.items[index].price)
            });
        }
    });

    if (items.length === 0) {
        alert('Please enter refund quantity for selected items');
        return;
    }

    const refundData = {
        transaction_id: currentTransaction.id,
        items: items,
        reason: document.getElementById('refundReason').value,
        notes: document.getElementById('refundNotes').value,
        refund_method: document.getElementById('refundMethod').value
    };

    if (!confirm('Are you sure you want to process this refund?')) {
        return;
    }

    fetch('<?= url('admin/pos/process-refund') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= Session::getCsrfToken() ?>'
        },
        body: JSON.stringify(refundData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('successRefundNumber').textContent = data.refund_number;
            document.getElementById('successRefundAmount').textContent = '৳' + parseFloat(data.refund_amount).toFixed(2);
            new bootstrap.Modal(document.getElementById('refundSuccessModal')).show();

            // Reset form
            currentTransaction = null;
            document.getElementById('transactionDetails').style.display = 'none';
            document.getElementById('noTransaction').style.display = 'block';
            document.getElementById('transactionNumber').value = '';

            // Reload recent refunds
            loadRecentRefunds();
        } else {
            alert(data.message || 'Error processing refund');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error processing refund');
    });
}

function loadRecentRefunds() {
    // This would load today's refunds from the server
    // For now, we'll just reload the page after a short delay
    setTimeout(() => {
        location.reload();
    }, 2000);
}

function printRefundReceipt() {
    // Implement print functionality
    window.print();
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('transactionNumber').focus();
});
</script>
