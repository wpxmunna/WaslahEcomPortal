<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?= $order['order_number'] ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; color: #333; background: #fff; }
        .invoice { max-width: 800px; margin: 0 auto; padding: 40px; }
        .invoice-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 2px solid #1a1a2e; }
        .brand { font-size: 32px; font-weight: 700; letter-spacing: 3px; color: #1a1a2e; }
        .brand small { display: block; font-size: 12px; font-weight: 400; letter-spacing: 1px; color: #666; }
        .invoice-title { text-align: right; }
        .invoice-title h1 { font-size: 28px; color: #1a1a2e; margin-bottom: 5px; }
        .invoice-title p { color: #666; }
        .invoice-info { display: flex; justify-content: space-between; margin-bottom: 40px; }
        .invoice-info .section { width: 48%; }
        .invoice-info h3 { font-size: 14px; text-transform: uppercase; color: #666; margin-bottom: 10px; letter-spacing: 1px; }
        .invoice-info p { margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table th { background: #1a1a2e; color: #fff; padding: 12px 15px; text-align: left; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
        table td { padding: 15px; border-bottom: 1px solid #eee; }
        table th:last-child, table td:last-child { text-align: right; }
        table th:nth-child(3), table td:nth-child(3) { text-align: center; }
        .totals { width: 300px; margin-left: auto; }
        .totals table { margin-bottom: 0; }
        .totals td { padding: 8px 0; border: none; }
        .totals .total-row td { font-size: 18px; font-weight: 700; color: #e94560; padding-top: 15px; border-top: 2px solid #1a1a2e; }
        .footer { text-align: center; margin-top: 50px; padding-top: 20px; border-top: 1px solid #eee; color: #666; font-size: 12px; }
        .status { display: inline-block; padding: 5px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; }
        .status-paid { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="invoice">
        <div class="no-print" style="margin-bottom: 20px; text-align: right;">
            <button onclick="window.print()" style="padding: 10px 20px; background: #1a1a2e; color: #fff; border: none; border-radius: 4px; cursor: pointer;">
                Print Invoice
            </button>
        </div>

        <div class="invoice-header">
            <div>
                <div class="brand">
                    <img src="<?= asset('images/logo.png') ?>" alt="Waslah" style="height: 50px;">
                </div>
            </div>
            <div class="invoice-title">
                <h1>INVOICE</h1>
                <p><?= $order['order_number'] ?></p>
                <p><?= formatDate($order['created_at']) ?></p>
            </div>
        </div>

        <div class="invoice-info">
            <div class="section">
                <h3>Bill To</h3>
                <p><strong><?= sanitize($order['billing_name']) ?></strong></p>
                <p><?= sanitize($order['billing_address_line1']) ?></p>
                <?php if ($order['billing_address_line2']): ?>
                <p><?= sanitize($order['billing_address_line2']) ?></p>
                <?php endif; ?>
                <p><?= sanitize($order['billing_city']) ?>, <?= sanitize($order['billing_state']) ?> <?= sanitize($order['billing_postal_code']) ?></p>
                <p><?= sanitize($order['billing_country']) ?></p>
                <p>Phone: <?= sanitize($order['billing_phone']) ?></p>
            </div>
            <div class="section">
                <h3>Ship To</h3>
                <p><strong><?= sanitize($order['shipping_name']) ?></strong></p>
                <p><?= sanitize($order['shipping_address_line1']) ?></p>
                <?php if ($order['shipping_address_line2']): ?>
                <p><?= sanitize($order['shipping_address_line2']) ?></p>
                <?php endif; ?>
                <p><?= sanitize($order['shipping_city']) ?>, <?= sanitize($order['shipping_state']) ?> <?= sanitize($order['shipping_postal_code']) ?></p>
                <p><?= sanitize($order['shipping_country']) ?></p>
            </div>
        </div>

        <div class="invoice-info">
            <div class="section">
                <h3>Order Details</h3>
                <p><strong>Order Number:</strong> <?= $order['order_number'] ?></p>
                <p><strong>Order Date:</strong> <?= formatDate($order['created_at']) ?></p>
                <p><strong>Payment Method:</strong> <?= ucfirst($order['payment_method'] ?? 'N/A') ?></p>
            </div>
            <div class="section">
                <h3>Payment Status</h3>
                <p>
                    <span class="status <?= $order['payment_status'] === 'paid' ? 'status-paid' : 'status-pending' ?>">
                        <?= strtoupper($order['payment_status']) ?>
                    </span>
                </p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order['items'] as $item): ?>
                <tr>
                    <td>
                        <?= sanitize($item['product_name']) ?>
                        <?php if ($item['variant_info']): ?>
                        <br><small style="color: #666;"><?= $item['variant_info'] ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= $item['product_sku'] ?: 'N/A' ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= formatPrice($item['unit_price']) ?></td>
                    <td><?= formatPrice($item['total_price']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td>Subtotal</td>
                    <td><?= formatPrice($order['subtotal']) ?></td>
                </tr>
                <?php if ($order['discount_amount'] > 0): ?>
                <tr>
                    <td>Discount</td>
                    <td>-<?= formatPrice($order['discount_amount']) ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td>Shipping</td>
                    <td><?= formatPrice($order['shipping_amount']) ?></td>
                </tr>
                <?php if ($order['tax_amount'] > 0): ?>
                <tr>
                    <td>Tax</td>
                    <td><?= formatPrice($order['tax_amount']) ?></td>
                </tr>
                <?php endif; ?>
                <tr class="total-row">
                    <td>Total</td>
                    <td><?= formatPrice($order['total_amount']) ?></td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>Thank you for shopping with Waslah!</p>
            <p>Questions? Contact us at <?= SITE_EMAIL ?></p>
            <p style="margin-top: 10px;">&copy; <?= date('Y') ?> Waslah Fashion. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
