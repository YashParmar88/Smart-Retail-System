<?php
/* start session and include connection */
session_start();
include('includes/db_conn.php');

/* get sale id from URL */
if(!isset($_GET['id'])) { die("Invalid Access"); }
$id = $_GET['id'];

/* 1. Fetch bill summary from sales table */
$sale_res = mysqli_query($conn, "SELECT * FROM sales WHERE id = $id");
$sale = mysqli_fetch_assoc($sale_res);

/* 2. Fetch items using JOIN to get names from products table */
$items_sql = "SELECT si.*, p.product_name FROM sales_items si 
              JOIN products p ON si.product_id = p.id 
              WHERE si.sale_id = $id";
$items_res = mysqli_query($conn, $items_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt - <?php echo $sale['invoice_no']; ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; padding: 20px; color: #000; line-height: 1.2; }
        .receipt-box { width: 300px; margin: auto; padding: 10px; border: 1px solid #ccc; }
        .center { text-align: center; }
        .hr { border-top: 1px dashed #000; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th { text-align: left; font-size: 13px; border-bottom: 1px solid #000; padding: 5px 0; }
        td { padding: 5px 0; font-size: 13px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 13px; }
        .bold { font-weight: bold; }
    </style>
</head>
<body onload="window.print()">

    <div class="receipt-box">
        <h2 class="center" style="margin:0;">Shree Ram Hardware</h2>
        <p class="center" style="font-size: 11px; margin:5px 0;">Rajkot, Gujarat, India</p>
        
        <div class="hr"></div>
        
        <div class="row"><span>Invoice:</span> <span><?php echo $sale['invoice_no']; ?></span></div>
        <div class="row"><span>Customer:</span> <span><?php echo $sale['customer_name']; ?></span></div>
        <div class="row"><span>Date:</span> <span><?php echo date('d-m-Y H:i', strtotime($sale['created_at'])); ?></span></div>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Item</th>
                    <th style="width: 20%; text-align: center;">Qty</th>
                    <th style="width: 30%; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $calculated_subtotal = 0;
                while($item = mysqli_fetch_assoc($items_res)): 
                    $line_total = $item['quantity'] * $item['unit_price'];
                    $calculated_subtotal += $line_total;
                ?>
                <tr>
                    <td><?php echo $item['product_name']; ?></td>
                    <td style="text-align: center;"><?php echo $item['quantity']; ?></td>
                    <td style="text-align: right;">₹<?php echo number_format($line_total, 2); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="hr"></div>

        <!-- NEW: Explicit Tax Breakdown -->
        <div class="row">
            <span>Subtotal:</span>
            <span>₹<?php echo number_format($calculated_subtotal, 2); ?></span>
        </div>
        <div class="row">
            <span>GST (5%):</span>
            <?php $tax_amount = $calculated_subtotal * 0.05; ?>
            <span>₹<?php echo number_format($tax_amount, 2); ?></span>
        </div>
        
        <div class="hr"></div>
        
        <div class="row bold" style="font-size: 16px;">
            <span>GRAND TOTAL:</span>
            <span>₹<?php echo number_format($calculated_subtotal + $tax_amount, 2); ?></span>
        </div>

        <div class="hr"></div>
        <p class="center" style="font-size: 11px;">Payment Method: <?php echo $sale['payment_method']; ?></p>
        <p class="center" style="font-size: 12px; margin-top: 20px;">--- Thank You! Visit Again ---</p>
    </div>

</body>
</html>