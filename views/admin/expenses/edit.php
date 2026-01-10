<div class="page-header">
    <h1>Edit Expense</h1>
    <a href="<?= url('admin/expenses') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back
    </a>
</div>

<form action="<?= url('admin/expenses/update/' . $expense['id']) ?>" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">

    <div class="row">
        <div class="col-lg-8">
            <!-- Basic Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Expense Details</h5>
                    <small class="text-muted">Expense #<?= $expense['expense_number'] ?></small>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required value="<?= sanitize($expense['title']) ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><?= CURRENCY_SYMBOL ?></span>
                                <input type="number" name="amount" class="form-control" required step="0.01" min="0" value="<?= $expense['amount'] ?>">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tax Amount</label>
                            <div class="input-group">
                                <span class="input-group-text"><?= CURRENCY_SYMBOL ?></span>
                                <input type="number" name="tax_amount" class="form-control" step="0.01" min="0" value="<?= $expense['tax_amount'] ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Expense Date <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" class="form-control" required value="<?= $expense['expense_date'] ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select">
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $expense['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                    <?= sanitize($cat['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?= sanitize($expense['description']) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Payment Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-select">
                                <option value="cash" <?= $expense['payment_method'] === 'cash' ? 'selected' : '' ?>>Cash</option>
                                <option value="bank_transfer" <?= $expense['payment_method'] === 'bank_transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                                <option value="mobile_banking" <?= $expense['payment_method'] === 'mobile_banking' ? 'selected' : '' ?>>Mobile Banking (bKash, Nagad)</option>
                                <option value="card" <?= $expense['payment_method'] === 'card' ? 'selected' : '' ?>>Card</option>
                                <option value="other" <?= $expense['payment_method'] === 'other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Payment Status</label>
                            <select name="payment_status" class="form-select">
                                <option value="paid" <?= $expense['payment_status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
                                <option value="pending" <?= $expense['payment_status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="partial" <?= $expense['payment_status'] === 'partial' ? 'selected' : '' ?>>Partial</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Vendor/Payee Name</label>
                            <input type="text" name="vendor_name" class="form-control" value="<?= sanitize($expense['vendor_name']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Reference Number</label>
                            <input type="text" name="reference_number" class="form-control" value="<?= sanitize($expense['reference_number']) ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Receipt Upload -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Receipt/Document</h5>
                </div>
                <div class="card-body">
                    <?php if ($expense['receipt_path']): ?>
                    <div class="mb-3">
                        <label class="form-label">Current Receipt</label>
                        <div>
                            <a href="<?= upload($expense['receipt_path']) ?>" target="_blank" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-file-image me-1"></i> View Receipt
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label"><?= $expense['receipt_path'] ? 'Replace Receipt' : 'Upload Receipt' ?></label>
                        <input type="file" name="receipt" class="form-control" accept="image/*,.pdf" id="receiptInput">
                        <small class="text-muted">Max 5MB. Accepted: Images, PDF</small>
                    </div>
                    <div id="receiptPreview" style="display: none;">
                        <img src="" alt="Preview" class="img-fluid rounded">
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Notes</h5>
                </div>
                <div class="card-body">
                    <textarea name="notes" class="form-control" rows="4"><?= sanitize($expense['notes']) ?></textarea>
                </div>
            </div>

            <!-- Actions -->
            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                <i class="fas fa-save me-2"></i> Update Expense
            </button>

            <button type="button" class="btn btn-outline-danger btn-lg w-100"
                    onclick="if(confirm('Delete this expense?')) window.location.href='<?= url('admin/expenses/delete/' . $expense['id']) ?>'">
                <i class="fas fa-trash me-2"></i> Delete Expense
            </button>
        </div>
    </div>
</form>

<script>
document.getElementById('receiptInput').addEventListener('change', function(e) {
    const preview = document.getElementById('receiptPreview');
    const img = preview.querySelector('img');

    if (this.files && this.files[0]) {
        const file = this.files[0];
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    }
});
</script>
