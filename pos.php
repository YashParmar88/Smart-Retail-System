<?php
session_start();
include('includes/db_conn.php');

if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

/* --- RESET LOGIC: Clearing cart for next customer --- */
if (isset($_GET['action']) && $_GET['action'] == 'reset') {
    unset($_SESSION['cart']);
    header("Location: pos.php");
    exit();
}

/* --- CART LOGIC --- */
if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }

/* Prevent adding items if current bill is already generated */
if (isset($_GET['add']) && !isset($_GET['success'])) {
    $pid = $_GET['add'];
    $p_res = mysqli_query($conn, "SELECT * FROM products WHERE id = $pid");
    $product = mysqli_fetch_assoc($p_res);
    if ($product) {
        if (isset($_SESSION['cart'][$pid])) { $_SESSION['cart'][$pid]['qty'] += 1; }
        else { $_SESSION['cart'][$pid] = ['name'=>$product['product_name'], 'price'=>$product['price'], 'qty'=>1]; }
    }
    header("Location: pos.php"); exit();
}

/* Remove item only if not success state */
if (isset($_GET['remove']) && !isset($_GET['success'])) { 
    unset($_SESSION['cart'][$_GET['remove']]); 
    header("Location: pos.php"); exit(); 
}

$products_list = mysqli_query($conn, "SELECT * FROM products WHERE stock_level > 0 ORDER BY product_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SuperMarket - Billing Counter</title>
    <link rel="stylesheet" href="assets/css/style.css?v=2.1">
    <style>
        .cart-item-row { display: flex; justify-content: space-between; border-bottom: 1px solid #f3f4f6; padding: 10px 0; font-size: 13px; }
        .remove-btn { color: #ef4444; text-decoration: none; font-weight: bold; margin-left: 8px; }
        .pos-card-link { text-decoration: none; color: inherit; display: block; <?php if(isset($_GET['success'])) echo 'pointer-events: none; opacity: 0.5;'; ?> }
        .btn-print { background: #6b7280 !important; margin-bottom: 10px; }
        .btn-done { background: #2563eb !important; }
    </style>
</head>
<body style="overflow: hidden;">

    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <div class="brand-logo">SuperMarket</div>
            <nav class="nav-links">
                <a href="dashboard.php" class="nav-item">Dashboard</a>
                <a href="products.php" class="nav-item">Products</a>
                <a href="categories.php" class="nav-item">Categories</a>
                <a href="pos.php" class="nav-item active">Billing Counter</a>
                <a href="reports.php" class="nav-item">Reports</a>
                <a href="logout.php" class="nav-item" style="color: #ef4444; margin-top: auto;">Logout</a>
            </nav>
        </aside>

        <div class="content-area">
            <header class="main-header">
                <h2 style="font-size: 20px;">Billing Counter</h2>
                <div class="user-nav">Terminal #01 | <strong><?php echo $_SESSION['user_name']; ?></strong></div>
            </header>

            <div class="pos-container">
                <div class="pos-products">
                    <div style="margin-bottom: 20px;">
                        <input type="text" placeholder="Search products..." style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #e5e7eb;">
                    </div>
                    <div class="product-grid">
                        <?php while($p = mysqli_fetch_assoc($products_list)): ?>
                        <a href="pos.php?add=<?php echo $p['id']; ?>" class="pos-card-link">
                            <div class="pos-card">
                                <div style="font-size: 24px; margin-bottom: 10px;">📦</div>
                                <span class="p-name"><?php echo $p['product_name']; ?></span>
                                <span class="p-price">₹<?php echo number_format($p['price'], 2); ?></span>
                                <span class="p-stock">Stock: <?php echo $p['stock_level']; ?></span>
                            </div>
                        </a>
                        <?php endwhile; ?>
                    </div>
                </div>

                <div class="pos-billing">
                    <form action="process_bill.php" method="POST">
                        <div class="bill-header">
                            <h3 style="margin-bottom: 15px;">Current Bill</h3>
                            <input type="text" name="customer_name" placeholder="Customer Name (Optional)" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #e5e7eb; margin-bottom: 10px;" <?php if(isset($_GET['success'])) echo 'readonly'; ?>>
                            <select name="payment_method" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #e5e7eb;" <?php if(isset($_GET['success'])) echo 'disabled'; ?>>
                                <option value="Cash">Cash Payment</option>
                                <option value="Card">Card Payment</option>
                            </select>
                        </div>

                        <div class="order-items" style="min-height: 250px;">
                            <div style="display: flex; justify-content: space-between; font-weight: 700; border-bottom: 2px solid #f3f4f6; padding-bottom: 8px;">
                                <span>Item</span><span>Qty</span><span>Total</span>
                            </div>
                            <?php 
                            $subtotal = 0;
                            if (!empty($_SESSION['cart'])):
                                foreach ($_SESSION['cart'] as $key => $item): 
                                    $line_total = $item['price'] * $item['qty'];
                                    $subtotal += $line_total;
                            ?>
                            <div class="cart-item-row">
                                <div style="width: 50%;"><strong><?php echo $item['name']; ?></strong></div>
                                <div style="width: 15%; text-align: center;"><?php echo $item['qty']; ?></div>
                                <div style="width: 35%; text-align: right;">
                                    ₹<?php echo number_format($line_total, 2); ?> 
                                    <?php if(!isset($_GET['success'])): ?>
                                        <a href="pos.php?remove=<?php echo $key; ?>" class="remove-btn">&times;</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; else: ?>
                                <p style="text-align: center; color: #9ca3af; margin-top: 50px;">Click products to add to bill</p>
                            <?php endif; ?>
                        </div>

                        <div class="bill-footer">
                            <?php $tax = $subtotal * 0.05; $grand = $subtotal + $tax; ?>
                            <div class="total-row"><span>Subtotal:</span><span>₹<?php echo number_format($subtotal, 2); ?></span></div>
                            <div class="total-row"><span>Tax (5%):</span><span>₹<?php echo number_format($tax, 2); ?></span></div>
                            <div class="total-row grand-total" style="color: #2563eb;"><span>Grand Total:</span><span>₹<?php echo number_format($grand, 2); ?></span></div>

                            <input type="hidden" name="total_amount" value="<?php echo $grand; ?>">
                            
                            <?php if(isset($_GET['success'])): ?>
                                <a href="print_bill.php?id=<?php echo $_GET['id']; ?>" target="_blank" class="login-btn btn-print" style="text-align:center; text-decoration:none; display:block;">Print Receipt</a>
                                <!-- This link will now trigger the Cart Reset -->
                                <a href="pos.php?action=reset" class="login-btn btn-done" style="text-align:center; text-decoration:none; display:block;">Next Bill (Done) &rarr;</a>
                            <?php else: ?>
                                <button type="submit" name="generate_bill" class="login-btn" style="background: #10b981; margin-top: 15px;">Generate Bill &check;</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert only at the very end -->
    <?php if(isset($_GET['success'])): ?>
        <script>setTimeout(() => { alert("Bill Saved Successfully!"); }, 100);</script>
    <?php endif; ?>

</body>
</html>