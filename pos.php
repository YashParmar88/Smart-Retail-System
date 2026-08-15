<?php
session_start();
include('includes/db_conn.php');

/* security check */
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

/* 1. INITIALIZE CART IF NOT EXISTS */
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/* 2. LOGIC: ADD PRODUCT TO CART */
if (isset($_GET['add'])) {
    $pid = $_GET['add'];
    
    /* fetch product details from DB */
    $p_sql = "SELECT * FROM products WHERE id = $pid";
    $p_res = mysqli_query($conn, $p_sql);
    $product = mysqli_fetch_assoc($p_res);

    if ($product) {
        $id = $product['id'];
        /* if already in cart, increase quantity */
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty'] += 1;
        } else {
            /* add as new item */
            $_SESSION['cart'][$id] = [
                'name' => $product['product_name'],
                'price' => $product['price'],
                'qty' => 1
            ];
        }
    }
    /* redirect to clean the URL */
    header("Location: pos.php");
    exit();
}

/* 3. LOGIC: REMOVE PRODUCT FROM CART */
if (isset($_GET['remove'])) {
    $id = $_GET['remove'];
    unset($_SESSION['cart'][$id]);
    header("Location: pos.php");
    exit();
}

/* 4. LOGIC: CLEAR ENTIRE CART */
if (isset($_GET['clear'])) {
    unset($_SESSION['cart']);
    header("Location: pos.php");
    exit();
}

/* Fetch all products for the grid */
$sql = "SELECT * FROM products WHERE stock_level > 0 ORDER BY product_name ASC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>POS - Smart Shop ERP</title>
    <link rel="stylesheet" href="assets/css/style.css?v=1.7">
    <style>
        /* small helper styles */
        .cart-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f3f4f6; }
        .remove-link { color: #ef4444; text-decoration: none; font-weight: bold; margin-left: 10px; }
        .empty-msg { text-align: center; padding: 40px 0; color: #9ca3af; }
    </style>
</head>
<body style="overflow: hidden;">

    <div class="dashboard-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="brand-logo">Smart Shop ERP</div>
            <nav class="nav-links">
                <a href="dashboard.php" class="nav-item">Dashboard</a>
                <a href="products.php" class="nav-item">Products</a>
                <a href="pos.php" class="nav-item active">Billing (POS)</a>
                <a href="logout.php" class="nav-item" style="color: #ef4444; margin-top: auto;">Logout</a>
            </nav>
        </aside>

        <div class="content-area">
            <header class="main-header">
                <div class="header-search"><input type="text" placeholder="Scan or Search..."></div>
                <div class="user-nav"><span class="user-name">Terminal #01 | <?php echo $_SESSION['user_name']; ?></span></div>
            </header>

            <div class="pos-container">
                <!-- LEFT: PRODUCT GRID -->
                <div class="pos-products">
                    <div class="product-grid">
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <!-- Clicking this card sends ID to URL -->
                        <a href="pos.php?add=<?php echo $row['id']; ?>" style="text-decoration: none; color: inherit;">
                            <div class="pos-card">
                                <div style="font-size: 30px; margin-bottom: 10px;">📦</div>
                                <span class="p-name"><?php echo $row['product_name']; ?></span>
                                <span class="p-price">$<?php echo number_format($row['price'], 2); ?></span>
                                <span class="p-stock">Stock: <?php echo $row['stock_level']; ?></span>
                            </div>
                        </a>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- RIGHT: BILLING CART -->
                <div class="pos-billing">
                    <div class="bill-header" style="display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <h3 style="font-weight: 700;">Current Order</h3>
                            <p style="font-size: 11px;">#<?php echo strtoupper(date('dmy-His')); ?></p>
                        </div>
                        <a href="pos.php?clear=1" style="font-size:12px; color:#ef4444; text-decoration:none;">Clear All</a>
                    </div>

                    <div class="order-items">
                        <?php 
                        $subtotal = 0;
                        if (!empty($_SESSION['cart'])): 
                            foreach ($_SESSION['cart'] as $id => $item): 
                                $item_total = $item['price'] * $item['qty'];
                                $subtotal += $item_total;
                        ?>
                            <div class="cart-item">
                                <div>
                                    <strong><?php echo $item['name']; ?></strong><br>
                                    <small>$<?php echo number_format($item['price'], 2); ?> x <?php echo $item['qty']; ?></small>
                                </div>
                                <div style="text-align: right;">
                                    <span style="font-weight: 600;">$<?php echo number_format($item_total, 2); ?></span>
                                    <a href="pos.php?remove=<?php echo $id; ?>" class="remove-link">&times;</a>
                                </div>
                            </div>
                        <?php endforeach; else: ?>
                            <div class="empty-msg">No items in cart. Click a product to add.</div>
                        <?php endif; ?>
                    </div>

                    <div class="bill-footer">
                        <?php 
                            $tax = $subtotal * 0.18;
                            $grand_total = $subtotal + $tax;
                        ?>
                        <div class="total-row"><span>Subtotal</span><span>$<?php echo number_format($subtotal, 2); ?></span></div>
                        <div class="total-row"><span>GST (18%)</span><span>$<?php echo number_format($tax, 2); ?></span></div>
                        <div class="total-row grand-total"><span>Grand Total</span><span>$<?php echo number_format($grand_total, 2); ?></span></div>
                        
                        <button class="login-btn" style="margin-top:20px;" onclick="window.print()">Confirm & Print Bill</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>