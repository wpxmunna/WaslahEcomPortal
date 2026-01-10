<div class="page-header">
    <h1>Create Journal Entry</h1>
    <a href="<?= url('admin/accounting/journal') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back
    </a>
</div>

<form action="<?= url('admin/accounting/journal/store') ?>" method="POST" id="journalForm">
    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">

    <div class="row">
        <div class="col-lg-8">
            <!-- Entry Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Entry Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Entry Date <span class="text-danger">*</span></label>
                            <input type="date" name="entry_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="2" required placeholder="Describe this journal entry..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Entry Lines -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i> Entry Lines</h5>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addLine()">
                        <i class="fas fa-plus me-1"></i> Add Line
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0" id="linesTable">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 35%;">Account</th>
                                    <th style="width: 25%;">Description</th>
                                    <th style="width: 15%;">Debit</th>
                                    <th style="width: 15%;">Credit</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="linesBody">
                                <tr class="line-row">
                                    <td>
                                        <select name="account_id[]" class="form-select account-select" required>
                                            <option value="">Select Account</option>
                                            <?php
                                            $currentType = '';
                                            foreach ($accounts as $account):
                                                if ($account['account_type'] !== $currentType):
                                                    if ($currentType !== '') echo '</optgroup>';
                                                    $currentType = $account['account_type'];
                                                    echo '<optgroup label="' . ucfirst($currentType) . '">';
                                                endif;
                                            ?>
                                            <option value="<?= $account['id'] ?>"><?= $account['account_code'] ?> - <?= sanitize($account['account_name']) ?></option>
                                            <?php endforeach; ?>
                                            <?php if ($currentType !== '') echo '</optgroup>'; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="line_description[]" class="form-control" placeholder="Optional">
                                    </td>
                                    <td>
                                        <input type="number" name="debit_amount[]" class="form-control debit-input" step="0.01" min="0" value="0" onchange="updateTotals()">
                                    </td>
                                    <td>
                                        <input type="number" name="credit_amount[]" class="form-control credit-input" step="0.01" min="0" value="0" onchange="updateTotals()">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeLine(this)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="line-row">
                                    <td>
                                        <select name="account_id[]" class="form-select account-select" required>
                                            <option value="">Select Account</option>
                                            <?php
                                            $currentType = '';
                                            foreach ($accounts as $account):
                                                if ($account['account_type'] !== $currentType):
                                                    if ($currentType !== '') echo '</optgroup>';
                                                    $currentType = $account['account_type'];
                                                    echo '<optgroup label="' . ucfirst($currentType) . '">';
                                                endif;
                                            ?>
                                            <option value="<?= $account['id'] ?>"><?= $account['account_code'] ?> - <?= sanitize($account['account_name']) ?></option>
                                            <?php endforeach; ?>
                                            <?php if ($currentType !== '') echo '</optgroup>'; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="line_description[]" class="form-control" placeholder="Optional">
                                    </td>
                                    <td>
                                        <input type="number" name="debit_amount[]" class="form-control debit-input" step="0.01" min="0" value="0" onchange="updateTotals()">
                                    </td>
                                    <td>
                                        <input type="number" name="credit_amount[]" class="form-control credit-input" step="0.01" min="0" value="0" onchange="updateTotals()">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeLine(this)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="2" class="text-end"><strong>Total:</strong></td>
                                    <td><strong id="totalDebit">0.00</strong></td>
                                    <td><strong id="totalCredit">0.00</strong></td>
                                    <td></td>
                                </tr>
                                <tr id="differenceRow" class="d-none">
                                    <td colspan="2" class="text-end text-danger"><strong>Difference:</strong></td>
                                    <td colspan="2"><strong id="difference" class="text-danger">0.00</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calculator me-2"></i> Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Debit</span>
                        <strong id="summaryDebit">৳0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Credit</span>
                        <strong id="summaryCredit">৳0.00</strong>
                    </div>
                    <hr>
                    <div id="balanceStatus" class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Entry not balanced
                    </div>
                </div>
            </div>

            <!-- Options -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-cog me-2"></i> Options</h5>
                </div>
                <div class="card-body">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="post_immediately" id="postImmediately" value="1">
                        <label class="form-check-label" for="postImmediately">
                            Post immediately
                        </label>
                        <small class="d-block text-muted">Update account balances right away</small>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100" id="submitBtn" disabled>
                <i class="fas fa-save me-2"></i> Create Entry
            </button>
        </div>
    </div>
</form>

<script>
const accountsHtml = document.querySelector('.account-select').innerHTML;

function addLine() {
    const tbody = document.getElementById('linesBody');
    const row = document.createElement('tr');
    row.className = 'line-row';
    row.innerHTML = `
        <td>
            <select name="account_id[]" class="form-select account-select" required>
                ${accountsHtml}
            </select>
        </td>
        <td>
            <input type="text" name="line_description[]" class="form-control" placeholder="Optional">
        </td>
        <td>
            <input type="number" name="debit_amount[]" class="form-control debit-input" step="0.01" min="0" value="0" onchange="updateTotals()">
        </td>
        <td>
            <input type="number" name="credit_amount[]" class="form-control credit-input" step="0.01" min="0" value="0" onchange="updateTotals()">
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeLine(this)">
                <i class="fas fa-times"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
}

function removeLine(btn) {
    const tbody = document.getElementById('linesBody');
    if (tbody.children.length > 2) {
        btn.closest('tr').remove();
        updateTotals();
    }
}

function updateTotals() {
    let totalDebit = 0;
    let totalCredit = 0;

    document.querySelectorAll('.debit-input').forEach(input => {
        totalDebit += parseFloat(input.value) || 0;
    });

    document.querySelectorAll('.credit-input').forEach(input => {
        totalCredit += parseFloat(input.value) || 0;
    });

    document.getElementById('totalDebit').textContent = totalDebit.toFixed(2);
    document.getElementById('totalCredit').textContent = totalCredit.toFixed(2);
    document.getElementById('summaryDebit').textContent = '৳' + totalDebit.toFixed(2);
    document.getElementById('summaryCredit').textContent = '৳' + totalCredit.toFixed(2);

    const diff = Math.abs(totalDebit - totalCredit);
    const isBalanced = diff < 0.01 && totalDebit > 0;

    const diffRow = document.getElementById('differenceRow');
    const balanceStatus = document.getElementById('balanceStatus');
    const submitBtn = document.getElementById('submitBtn');

    if (!isBalanced) {
        diffRow.classList.remove('d-none');
        document.getElementById('difference').textContent = diff.toFixed(2);
        balanceStatus.className = 'alert alert-warning mb-0';
        balanceStatus.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i> Entry not balanced';
        submitBtn.disabled = true;
    } else {
        diffRow.classList.add('d-none');
        balanceStatus.className = 'alert alert-success mb-0';
        balanceStatus.innerHTML = '<i class="fas fa-check-circle me-2"></i> Entry is balanced';
        submitBtn.disabled = false;
    }
}

// Initial update
updateTotals();
</script>
