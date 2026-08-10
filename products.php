<?php
session_start();
include('includes/db_conn.php');
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Products - Smart Shop ERP</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar (Same as dashboard) -->
        <aside class="sidebar">
            <div class="brand-logo">Smart Shop ERP</div>
            <nav class="nav-links">
                <a href="dashboard.php" class="nav-item">Dashboard</a>
                <a href="products.php" class="nav-item active">Products</a>
                <a href="#" class="nav-item">Categories</a>
                <a href="logout.php" class="nav-item" style="color: #ef4444;">Logout</a>
            </nav>
        </aside>

        <div class="content-area">
            <header class="main-header">
                <div class="header-search"><input type="text" placeholder="Search products..."></div>
                <div class="user-nav"><span class="user-name"><?php echo $_SESSION['user_name']; ?></span></div>
            </header>

            <main style="padding: 30px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h2>Product Management</h2>
                    <button style="padding: 10px 20px; background: #000; color: #fff; border: none; border-radius: 8px; cursor: pointer;">+ Add Product</button>
                </div>
                
                <p style="margin-top: 10px; color: #6b7280;">Manage your inventory catalog and stock levels.</p>
                
                <!-- We will build the product table on Day 6 -->
                <div style="margin-top: 30px; text-align: center; padding: 50px; background: #fff; border: 1px dashed #ccc; border-radius: 12px;">
                    <p>Product Table will appear here.</p>
                </div>
            </main>
        </div>
    </div>
</body>
</html>