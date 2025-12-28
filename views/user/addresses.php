<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url() ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= url('account') ?>">My Account</a></li>
                <li class="breadcrumb-item active">Addresses</li>
            </ol>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-4">
                <?php include VIEW_PATH . '/user/partials/sidebar.php'; ?>
            </div>

            <!-- Content -->
            <div class="col-lg-9">
                <div class="account-content">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="mb-0">My Addresses</h3>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                            <i class="fas fa-plus me-2"></i>Add New Address
                        </button>
                    </div>

                    <?php if (empty($addresses)): ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                            <h5>No addresses saved</h5>
                            <p class="text-muted mb-3">Add a shipping address to make checkout faster.</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                <i class="fas fa-plus me-2"></i>Add Address
                            </button>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($addresses as $address): ?>
                        <div class="col-md-6">
                            <div class="card h-100 <?= $address['is_default'] ? 'border-primary' : '' ?>">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <span class="badge bg-secondary me-2"><?= sanitize($address['label'] ?? 'Address') ?></span>
                                            <?php if ($address['is_default']): ?>
                                            <span class="badge bg-primary">Default</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="editAddress(<?= $address['id'] ?>)">
                                                        <i class="fas fa-edit me-2"></i>Edit
                                                    </a>
                                                </li>
                                                <?php if (!$address['is_default']): ?>
                                                <li>
                                                    <a class="dropdown-item" href="<?= url('account/addresses/default/' . $address['id']) ?>">
                                                        <i class="fas fa-check me-2"></i>Set as Default
                                                    </a>
                                                </li>
                                                <?php endif; ?>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#" onclick="deleteAddress(<?= $address['id'] ?>)">
                                                        <i class="fas fa-trash me-2"></i>Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <h6 class="mb-2"><?= sanitize($address['name']) ?></h6>
                                    <p class="mb-1 text-muted">
                                        <?= sanitize($address['address_line1']) ?>
                                        <?php if (!empty($address['address_line2'])): ?>
                                        <br><?= sanitize($address['address_line2']) ?>
                                        <?php endif; ?>
                                    </p>
                                    <p class="mb-1 text-muted">
                                        <?= sanitize($address['city']) ?><?= !empty($address['state']) ? ', ' . sanitize($address['state']) : '' ?> <?= sanitize($address['postal_code'] ?? '') ?>
                                    </p>
                                    <p class="mb-0 text-muted"><?= sanitize($address['country'] ?? 'Bangladesh') ?></p>
                                    <?php if (!empty($address['phone'])): ?>
                                    <p class="mb-0 mt-2">
                                        <i class="fas fa-phone me-1"></i> <?= sanitize($address['phone']) ?>
                                    </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= url('account/addresses') ?>" method="POST">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Label</label>
                            <select name="label" class="form-select">
                                <option value="Home">Home</option>
                                <option value="Office">Office</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number *</label>
                            <input type="tel" name="phone" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Country</label>
                            <input type="text" name="country" class="form-control" value="Bangladesh">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address Line 1 *</label>
                            <input type="text" name="address_line1" class="form-control" placeholder="Street address, P.O. box" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address Line 2</label>
                            <input type="text" name="address_line2" class="form-control" placeholder="Apartment, suite, unit, building, floor, etc.">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">City *</label>
                            <input type="text" name="city" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State / Division</label>
                            <input type="text" name="state" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Postal Code</label>
                            <input type="text" name="postal_code" class="form-control">
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" name="is_default" value="1" class="form-check-input" id="isDefault">
                                <label class="form-check-label" for="isDefault">Set as default address</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Address</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function deleteAddress(id) {
    if (confirm('Are you sure you want to delete this address?')) {
        window.location.href = '<?= url('account/addresses/delete') ?>/' + id;
    }
}

function editAddress(id) {
    // For now, redirect to a simple edit - could be enhanced with modal
    alert('Edit functionality coming soon. Please delete and add a new address for now.');
}
</script>
