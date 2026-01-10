<div class="page-header">
    <h1>Add Supplier</h1>
    <a href="<?= url('admin/suppliers') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Suppliers
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <form action="<?= url('admin/suppliers/store') ?>" method="POST">
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
                            <input type="text" name="name" class="form-control" required placeholder="Enter supplier name">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Supplier Code</label>
                            <input type="text" name="code" class="form-control" placeholder="Auto-generated if empty">
                            <small class="text-muted">Leave empty for auto-generated code</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Additional notes about this supplier..."></textarea>
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
                            <input type="text" name="contact_person" class="form-control" placeholder="Primary contact name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" placeholder="+880 1XXX-XXXXXX">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="supplier@example.com">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Street address..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" placeholder="City">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Country</label>
                            <select name="country" class="form-select">
                                <option value="Bangladesh" selected>Bangladesh</option>
                                <option value="India">India</option>
                                <option value="China">China</option>
                                <option value="Thailand">Thailand</option>
                                <option value="Vietnam">Vietnam</option>
                                <option value="Indonesia">Indonesia</option>
                                <option value="Turkey">Turkey</option>
                                <option value="Other">Other</option>
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
                            <option value="0">Due on Receipt</option>
                            <option value="7">Net 7</option>
                            <option value="15">Net 15</option>
                            <option value="30" selected>Net 30</option>
                            <option value="45">Net 45</option>
                            <option value="60">Net 60</option>
                            <option value="90">Net 90</option>
                        </select>
                        <small class="text-muted">Number of days until payment is due</small>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Save Supplier
                </button>
                <a href="<?= url('admin/suppliers') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <!-- Help Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i> Help</h5>
            </div>
            <div class="card-body">
                <h6>Supplier Code</h6>
                <p class="text-muted small">A unique identifier for the supplier. If left empty, a code like "SUP1234" will be auto-generated.</p>

                <h6>Payment Terms</h6>
                <p class="text-muted small">Standard payment period for this supplier. This helps track when payments are due for purchase orders.</p>

                <h6>Contact Information</h6>
                <p class="text-muted small">Add contact details to easily communicate with the supplier about orders and payments.</p>
            </div>
        </div>
    </div>
</div>
