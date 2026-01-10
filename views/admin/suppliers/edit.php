<div class="page-header">
    <h1>Edit Supplier</h1>
    <a href="<?= url('admin/suppliers/view/' . $supplier['id']) ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Supplier
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <form action="<?= url('admin/suppliers/update/' . $supplier['id']) ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">

            <!-- Basic Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Basic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Supplier Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required value="<?= sanitize($supplier['name']) ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Supplier Code</label>
                            <input type="text" name="code" class="form-control" value="<?= sanitize($supplier['code']) ?>" readonly>
                            <small class="text-muted">Code cannot be changed</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" <?= $supplier['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $supplier['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"><?= sanitize($supplier['notes']) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-address-book me-2"></i> Contact Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact Person</label>
                            <input type="text" name="contact_person" class="form-control" value="<?= sanitize($supplier['contact_person']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?= sanitize($supplier['phone']) ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= sanitize($supplier['email']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2"><?= sanitize($supplier['address']) ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" value="<?= sanitize($supplier['city']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Country</label>
                            <select name="country" class="form-select">
                                <option value="Bangladesh" <?= $supplier['country'] === 'Bangladesh' ? 'selected' : '' ?>>Bangladesh</option>
                                <option value="India" <?= $supplier['country'] === 'India' ? 'selected' : '' ?>>India</option>
                                <option value="China" <?= $supplier['country'] === 'China' ? 'selected' : '' ?>>China</option>
                                <option value="Thailand" <?= $supplier['country'] === 'Thailand' ? 'selected' : '' ?>>Thailand</option>
                                <option value="Vietnam" <?= $supplier['country'] === 'Vietnam' ? 'selected' : '' ?>>Vietnam</option>
                                <option value="Indonesia" <?= $supplier['country'] === 'Indonesia' ? 'selected' : '' ?>>Indonesia</option>
                                <option value="Turkey" <?= $supplier['country'] === 'Turkey' ? 'selected' : '' ?>>Turkey</option>
                                <option value="Other" <?= $supplier['country'] === 'Other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Terms -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-money-check-alt me-2"></i> Payment Terms</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Payment Terms (Days)</label>
                        <select name="payment_terms" class="form-select">
                            <option value="0" <?= $supplier['payment_terms'] == 0 ? 'selected' : '' ?>>Due on Receipt</option>
                            <option value="7" <?= $supplier['payment_terms'] == 7 ? 'selected' : '' ?>>Net 7</option>
                            <option value="15" <?= $supplier['payment_terms'] == 15 ? 'selected' : '' ?>>Net 15</option>
                            <option value="30" <?= $supplier['payment_terms'] == 30 ? 'selected' : '' ?>>Net 30</option>
                            <option value="45" <?= $supplier['payment_terms'] == 45 ? 'selected' : '' ?>>Net 45</option>
                            <option value="60" <?= $supplier['payment_terms'] == 60 ? 'selected' : '' ?>>Net 60</option>
                            <option value="90" <?= $supplier['payment_terms'] == 90 ? 'selected' : '' ?>>Net 90</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Update Supplier
                </button>
                <a href="<?= url('admin/suppliers/view/' . $supplier['id']) ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <!-- Supplier Stats -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i> Supplier Stats</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Total Purchases</span>
                    <strong><?= formatPrice($supplier['total_purchases'] ?? 0) ?></strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Total Paid</span>
                    <strong class="text-success"><?= formatPrice($supplier['total_paid'] ?? 0) ?></strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Balance Due</span>
                    <?php $balance = ($supplier['total_purchases'] ?? 0) - ($supplier['total_paid'] ?? 0); ?>
                    <strong class="<?= $balance > 0 ? 'text-danger' : 'text-success' ?>"><?= formatPrice($balance) ?></strong>
                </div>
            </div>
        </div>
    </div>
</div>
