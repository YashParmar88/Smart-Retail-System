<?php
session_start();
include('includes/db_conn.php');

if (isset($_POST['generate_bill']) && !empty($_SESSION['cart'])) {
    
    $customer = mysqli_real_escape_string($conn, $_POST['customer_name']);
    if (empty($customer)) { $customer = "Guest"; }

    $payment  = $_POST['payment_method'];
    $total    = $_POST['total_amount'];
    $invoice  = "INV-" . strtoupper(substr(md5(time()), 0, 6));

    /* 1. Save summary */
    $sql_sale = "INSERT INTO sales (invoice_no, customer_name, payment_method, grand_total) 
                 VALUES ('$invoice', '$customer', '$payment', '$total')";
    
    if (mysqli_query($conn, $sql_sale)) {
        $sale_id = mysqli_insert_id($conn);

        /* 2. Save items & Update Stock */
        foreach ($_SESSION['cart'] as $pid => $item) {
            $qty = $item['qty'];
            $price = $item['price'];
            mysqli_query($conn, "INSERT INTO sales_items (sale_id, product_id, quantity, unit_price) VALUES ($sale_id, $pid, $qty, $price)");
            mysqli_query($conn, "UPDATE products SET stock_level = stock_level - $qty WHERE id = $pid");
        }

        header("Location: pos.php?success=1&id=" . $sale_id);
        exit();
    }
}
?>