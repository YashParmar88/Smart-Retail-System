<?php
session_start();
include('includes/db_conn.php');

/* get sale id from URL */
$id = $_GET['id'];

/* 1. Fetch bill summary */
$sale_res = mysqli_query($conn, "SELECT * FROM sales WHERE id = $id");
$sale = mysqli_fetch_assoc($sale_res);

/* 2. Fetch items in this bill (JOINing with products to get names) */
$items_sql = "SELECT si.*, p.product_name FROM sales_items si 
              JOIN products p ON si.product_id = p.id 
              WHERE si.sale_id = $id";
$items_res = mysqli_query($conn, $items_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - <?php echo $sale['invoice_no']; ?></title>
    <style>
        body { font-family: sans-serif; padding: 20px; color: #333; }
        .receipt-box { width: 350px; border: 1px solid #eee; padding: 20px; margin: auto; }
        .center { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { border-bottom: 2px solid #333; text-align: left; padding: 5px; font-size: 14px; }
        td { padding: 5px; font-size: 14px; border-bottom: 1px solid #eee; }
        .total-area { margin-top: 20px; text-align: right; }
    </style>
</head>
<body onload="window.print()"> <!-- automatic print on load -->

    <div class="receipt-box">
        <h2 class="center">SMART SHOP ERP</h2>
        <p class="center" style="font-size: 12px;">Rajkot, Gujarat, India</p>
        <hr>
        <p><strong>Invoice:</strong> <?php echo $sale['invoice_no']; ?></p>
        <p><strong>Customer:</strong> <?php echo $sale['customer_name']; ?></p>
        <p><strong>Date:</strong> <?php echo $sale['created_at']; ?></p>
        
        <table>
            <thead>
                <tr><th>Item</th><th>Qty</th><th>Total</th></tr>
            </thead>
            <tbody>
                <?php while($item = mysqli_fetch_assoc($items_res)): ?>
                <tr>
                    <td><?php echo $item['product_name']; ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>₹<?php echo number_format($item['quantity'] * $item['unit_price'], 2); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="total-area">
            <h3>Grand Total: ₹<?php echo number_format($sale['grand_total'], 2); ?></h3>
            <p>Payment: <?php echo $sale['payment_method']; ?></p>
        </div>

        <p class="center" style="margin-top: 30px; font-size: 12px;">Thank you! Visit Again.</p>
    </div>

</body>
</html>