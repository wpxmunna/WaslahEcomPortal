<?php
/**
 * POS Receipt View (Printable)
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - <?= htmlspecialchars($transaction['receipt_number']) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            padding: 10px;
            max-width: 80mm;
            margin: 0 auto;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .divider {
            border-bottom: 1px dashed #000;
            margin: 8px 0;
        }
        .double-divider {
            border-bottom: 2px solid #000;
            margin: 8px 0;
        }
        .store-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .items-table {
            width: 100%;
            margin: 10px 0;
        }
        .items-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .qty-col { width: 30px; }
        .price-col { width: 70px; text-align: right; }
        .total-row td {
            padding-top: 8px;
            font-weight: bold;
        }
        .footer {
            margin-top: 15px;
            font-size: 11px;
        }
        @media print {
            body { margin: 0; padding: 5px; }
            .no-print { display: none; }
        }
        .print-btn {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .print-btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <!-- Store Header -->
    <div class="text-center">
        <div class="store-name"><?= htmlspecialchars($storeName ?? 'WASLAH STORE') ?></div>
        <div><?= htmlspecialchars($storeAddress ?? '') ?></div>
        <div><?= htmlspecialchars($storePhone ?? '') ?></div>
    </div>

    <div class="double-divider"></div>

    <!-- Receipt Info -->
    <div>
        <table style="width: 100%;">
            <tr>
                <td>Receipt #:</td>
                <td class="text-right bold"><?= htmlspecialchars($transaction['receipt_number']) ?></td>
            </tr>
            <tr>
                <td>Date:</td>
                <td class="text-right"><?= date('M d, Y', strtotime($transaction['created_at'])) ?></td>
            </tr>
            <tr>
                <td>Time:</td>
                <td class="text-right"><?= date('h:i A', strtotime($transaction['created_at'])) ?></td>
            </tr>
            <tr>
                <td>Cashier:</td>
                <td class="text-right"><?= htmlspecialchars($transaction['cashier_name'] ?? 'N/A') ?></td>
            </tr>
            <?php if (!empty($transaction['customer_phone'])): ?>
            <tr>
                <td>Customer:</td>
                <td class="text-right"><?= htmlspecialchars($transaction['customer_phone']) ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>

    <div class="divider"></div>

    <!-- Items -->
    <table class="items-table">
        <thead>
            <tr>
                <td class="qty-col bold">Qty</td>
                <td class="bold">Item</td>
                <td class="price-col bold">Amount</td>
            </tr>
        </thead>
        <tbody>
            <tr><td colspan="3"><div class="divider" style="margin: 3px 0;"></div></td></tr>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td class="qty-col"><?= $item['quantity'] ?></td>
                    <td>
                        <?= htmlspecialchars($item['product_name']) ?>
                        <br>
                        <small>@ <?= CURRENCY_SYMBOL ?><?= number_format($item['unit_price'], 2) ?></small>
                    </td>
                    <td class="price-col"><?= CURRENCY_SYMBOL ?><?= number_format($item['total_price'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="divider"></div>

    <!-- Totals -->
    <table style="width: 100%;">
        <tr>
            <td>Subtotal:</td>
            <td class="text-right"><?= CURRENCY_SYMBOL ?><?= number_format($transaction['subtotal'], 2) ?></td>
        </tr>
        <?php if ($transaction['discount_amount'] > 0): ?>
        <tr>
            <td>Discount:</td>
            <td class="text-right">-<?= CURRENCY_SYMBOL ?><?= number_format($transaction['discount_amount'], 2) ?></td>
        </tr>
        <?php endif; ?>
        <?php if ($transaction['tax_amount'] > 0): ?>
        <tr>
            <td>Tax:</td>
            <td class="text-right"><?= CURRENCY_SYMBOL ?><?= number_format($transaction['tax_amount'], 2) ?></td>
        </tr>
        <?php endif; ?>
        <tr class="total-row">
            <td colspan="2"><div class="divider" style="margin: 3px 0;"></div></td>
        </tr>
        <tr class="total-row">
            <td class="bold" style="font-size: 14px;">TOTAL:</td>
            <td class="text-right bold" style="font-size: 14px;"><?= CURRENCY_SYMBOL ?><?= number_format($transaction['total_amount'], 2) ?></td>
        </tr>
    </table>

    <div class="divider"></div>

    <!-- Payment Info -->
    <table style="width: 100%;">
        <tr>
            <td>Payment Method:</td>
            <td class="text-right bold"><?= ucfirst(str_replace('_', ' ', $transaction['payment_method'])) ?></td>
        </tr>
        <?php if ($transaction['payment_method'] === 'cash' && isset($transaction['amount_received'])): ?>
        <tr>
            <td>Amount Received:</td>
            <td class="text-right"><?= CURRENCY_SYMBOL ?><?= number_format($transaction['amount_received'], 2) ?></td>
        </tr>
        <tr>
            <td>Change:</td>
            <td class="text-right bold"><?= CURRENCY_SYMBOL ?><?= number_format($transaction['change_amount'], 2) ?></td>
        </tr>
        <?php endif; ?>
    </table>

    <div class="double-divider"></div>

    <!-- Footer -->
    <div class="footer text-center">
        <p class="bold">Thank you for your purchase!</p>
        <p>Please keep this receipt for returns/exchanges</p>
        <p>Returns accepted within 7 days with receipt</p>
        <br>
        <p style="font-size: 10px;">
            Printed: <?= date('M d, Y h:i A') ?>
        </p>
    </div>

    <!-- Print Button (hidden when printing) -->
    <button class="print-btn no-print" onclick="window.print()">
        Print Receipt
    </button>

    <script>
        // Auto print when opened
        window.onload = function() {
            // Uncomment to auto-print
            // window.print();
        };
    </script>
</body>
</html>
