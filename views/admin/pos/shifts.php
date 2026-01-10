<?php
/**
 * POS Shifts View
 */
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-clock"></i> POS Shifts</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('admin/pos') ?>">POS</a></li>
                <li class="breadcrumb-item active">Shifts</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">From Date</label>
                <input type="date" name="date_from" class="form-control" value="<?= $filters['date_from'] ?? '' ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">To Date</label>
                <input type="date" name="date_to" class="form-control" value="<?= $filters['date_to'] ?? '' ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="open" <?= ($filters['status'] ?? '') === 'open' ? 'selected' : '' ?>>Open</option>
                    <option value="closed" <?= ($filters['status'] ?? '') === 'closed' ? 'selected' : '' ?>>Closed</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Filter</button>
                <a href="<?= url('admin/pos/shifts') ?>" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Shifts Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-history"></i> Shift History
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Shift #</th>
                    <th>Terminal</th>
                    <th>Cashier</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Duration</th>
                    <th>Opening</th>
                    <th>Sales</th>
                    <th>Closing</th>
                    <th>Variance</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($shifts)): ?>
                    <tr>
                        <td colspan="12" class="text-center py-5">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fas fa-clock"></i></div>
                                <h4>No Shifts</h4>
                                <p>No shift records found</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($shifts as $shift): ?>
                        <?php
                        $openingTime = $shift['opening_time'] ?? null;
                        $closingTime = $shift['closing_time'] ?? null;
                        $duration = null;
                        if ($openingTime) {
                            $startTime = new DateTime($openingTime);
                            $endTime = $closingTime ? new DateTime($closingTime) : new DateTime();
                            $duration = $startTime->diff($endTime);
                        }
                        $expectedCash = ($shift['opening_cash'] ?? 0) + ($shift['total_cash_sales'] ?? 0);
                        $actualCash = $shift['actual_cash'] ?? null;
                        $variance = $actualCash !== null ? $actualCash - $expectedCash : 0;
                        ?>
                        <tr>
                            <td><strong>#<?= $shift['id'] ?></strong></td>
                            <td><?= htmlspecialchars($shift['terminal_name'] ?? 'Terminal ' . ($shift['terminal_id'] ?? '')) ?></td>
                            <td><?= htmlspecialchars($shift['cashier_name'] ?? 'N/A') ?></td>
                            <td>
                                <?php if ($openingTime): ?>
                                    <?= date('M d', strtotime($openingTime)) ?><br>
                                    <small class="text-muted"><?= date('h:i A', strtotime($openingTime)) ?></small>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($closingTime): ?>
                                    <?= date('M d', strtotime($closingTime)) ?><br>
                                    <small class="text-muted"><?= date('h:i A', strtotime($closingTime)) ?></small>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $duration ? $duration->h . 'h ' . $duration->i . 'm' : '-' ?></td>
                            <td><?= CURRENCY_SYMBOL ?><?= number_format($shift['opening_cash'] ?? 0, 2) ?></td>
                            <td>
                                <strong><?= CURRENCY_SYMBOL ?><?= number_format($shift['total_sales'] ?? 0, 2) ?></strong><br>
                                <small class="text-muted"><?= $shift['total_transactions'] ?? 0 ?> txns</small>
                            </td>
                            <td>
                                <?php if ($actualCash !== null): ?>
                                    <?= CURRENCY_SYMBOL ?><?= number_format($actualCash, 2) ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($shift['status'] === 'closed'): ?>
                                    <?php if (abs($variance) < 0.01): ?>
                                        <span class="badge bg-success">Balanced</span>
                                    <?php elseif ($variance > 0): ?>
                                        <span class="badge bg-info">+<?= CURRENCY_SYMBOL ?><?= number_format($variance, 2) ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-danger"><?= CURRENCY_SYMBOL ?><?= number_format($variance, 2) ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($shift['status'] === 'open'): ?>
                                    <span class="badge bg-success p-2">
                                        <i class="fas fa-play me-1"></i>Active
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Closed</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#shiftModal<?= $shift['id'] ?>">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (!empty($pagination) && $pagination['total_pages'] > 1): ?>
        <div class="card-footer">
            <nav>
                <ul class="pagination justify-content-center mb-0">
                    <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                        <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&<?= http_build_query(array_filter($filters ?? [])) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<!-- Shift Detail Modals -->
<?php foreach ($shifts as $shift): ?>
    <?php
    $expectedCash = ($shift['opening_cash'] ?? 0) + ($shift['total_cash_sales'] ?? 0);
    $actualCash = $shift['actual_cash'] ?? null;
    $variance = $actualCash !== null ? $actualCash - $expectedCash : 0;
    $openingTime = $shift['opening_time'] ?? null;
    $closingTime = $shift['closing_time'] ?? null;
    ?>
    <div class="modal fade" id="shiftModal<?= $shift['id'] ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Shift #<?= $shift['id'] ?> Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3">Shift Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td class="fw-bold">Terminal:</td>
                                    <td><?= htmlspecialchars($shift['terminal_name'] ?? 'Terminal ' . ($shift['terminal_id'] ?? '')) ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Cashier:</td>
                                    <td><?= htmlspecialchars($shift['cashier_name'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Start Time:</td>
                                    <td><?= $openingTime ? date('M d, Y h:i A', strtotime($openingTime)) : '-' ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">End Time:</td>
                                    <td><?= $closingTime ? date('M d, Y h:i A', strtotime($closingTime)) : '-' ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Status:</td>
                                    <td>
                                        <?php if ($shift['status'] === 'open'): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Closed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3">Cash Summary</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td class="fw-bold">Opening Cash:</td>
                                    <td><?= CURRENCY_SYMBOL ?><?= number_format($shift['opening_cash'] ?? 0, 2) ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Cash Sales:</td>
                                    <td class="text-success">+<?= CURRENCY_SYMBOL ?><?= number_format($shift['total_cash_sales'] ?? 0, 2) ?></td>
                                </tr>
                                <tr class="border-top">
                                    <td class="fw-bold">Expected Cash:</td>
                                    <td><?= CURRENCY_SYMBOL ?><?= number_format($expectedCash, 2) ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Actual Closing:</td>
                                    <td><?= $actualCash !== null ? CURRENCY_SYMBOL . number_format($actualCash, 2) : '-' ?></td>
                                </tr>
                                <?php if ($shift['status'] === 'closed'): ?>
                                    <tr class="border-top">
                                        <td class="fw-bold">Variance:</td>
                                        <td class="<?= $variance >= 0 ? 'text-success' : 'text-danger' ?> fw-bold">
                                            <?= $variance >= 0 ? '+' : '' ?><?= CURRENCY_SYMBOL ?><?= number_format($variance, 2) ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">Sales Summary</h6>
                            <div class="row text-center">
                                <div class="col-3">
                                    <div class="h4 mb-0"><?= $shift['total_transactions'] ?? 0 ?></div>
                                    <small class="text-muted">Transactions</small>
                                </div>
                                <div class="col-3">
                                    <div class="h4 mb-0"><?= CURRENCY_SYMBOL ?><?= number_format($shift['total_sales'] ?? 0, 2) ?></div>
                                    <small class="text-muted">Total Sales</small>
                                </div>
                                <div class="col-3">
                                    <div class="h4 mb-0"><?= CURRENCY_SYMBOL ?><?= number_format($shift['total_cash_sales'] ?? 0, 2) ?></div>
                                    <small class="text-muted">Cash Sales</small>
                                </div>
                                <div class="col-3">
                                    <div class="h4 mb-0"><?= CURRENCY_SYMBOL ?><?= number_format($shift['total_card_sales'] ?? 0, 2) ?></div>
                                    <small class="text-muted">Card/Digital</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="<?= url('admin/pos/transactions?shift_id=' . $shift['id']) ?>" class="btn btn-info">
                        <i class="fas fa-receipt me-1"></i>View Transactions
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
