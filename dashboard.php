<?php
/* start session to track user */
session_start();

/* include database connection file */
include('includes/db_conn.php');

/* security: redirect if not logged in */
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

/* 1. Get total count of registered users */
$u_query = "SELECT id FROM users";
$u_result = mysqli_query($conn, $u_query);
$total_users = mysqli_num_rows($u_result);

/* 2. Get total count of products in inventory */
$p_query = "SELECT id FROM products";
$p_result = mysqli_query($conn, $p_query);
$total_products = mysqli_num_rows($p_result);

/* 3. Calculate total sales amount for today only */
$s_query = "SELECT SUM(grand_total) as today_total FROM sales WHERE DATE(created_at) = CURDATE()";
$s_result = mysqli_query($conn, $s_query);
$s_data = mysqli_fetch_assoc($s_result);
$today_sales = $s_data['today_total'] ?? 0;

/* 4. Count items that reached low stock threshold */
$l_query = "SELECT id FROM products WHERE stock_level <= low_stock_threshold";
$l_result = mysqli_query($conn, $l_query);
$low_stock_count = mysqli_num_rows($l_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Smart retail System</title>
    <!-- link the main stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css?v=3.0">
</head>
<body>

    <div class="dashboard-wrapper">
        
        <!-- sidebar section -->
        <aside class="sidebar">
         <div class="brand-logo">
    <span>🛠️</span> Shree Ram Hardware
</div>
            
           <!-- Sidebar Navigation Menu -->
<nav class="nav-links">
    
    <!-- 1. DASHBOARD: accessible by everyone -->
    <a href="dashboard.php" class="nav-item active">Dashboard</a>
    
    <!-- 2. BILLING COUNTER: accessible by everyone -->
    <a href="pos.php" class="nav-item">Billing Counter</a>
    
    <!-- 3. CUSTOMERS: accessible by everyone -->
    <a href="customers.php" class="nav-item">Customers</a>

    <!-- RESTRICTED ACCESS: only admin can see the following menus -->
    <?php if($_SESSION['user_role'] == 'admin'): ?>
        
        <a href="products.php" class="nav-item">Products</a>
        <a href="categories.php" class="nav-item">Categories</a>
        <a href="suppliers.php" class="nav-item">Suppliers</a>
        <a href="inventory.php" class="nav-item">Inventory</a>
        <a href="reports.php" class="nav-item">Sales History</a>
        
    <?php endif; ?>

</nav>
        </aside>

        <!-- main content area -->
        <div class="content-area">
            
            <!-- top header -->
           <header class="main-header">
    <!-- This empty div acts as a spacer to push the profile to the right -->
    <div></div> 

    <div class="user-nav">
        <span class="user-name">Welcome, <?php echo $_SESSION['user_name']; ?></span>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</header>

            <!-- main stats content -->
            <main style="padding: 30px;">
                
                <div class="dashboard-header">
                    <h2 style="font-size: 24px; color: #111827;">Dashboard Overview</h2>
                    <p style="color: #6b7280; margin-bottom: 25px;">Welcome back! Here's what's happening with your store today.</p>
                </div>

                <!-- Stats Cards Grid -->
                <div class="stats-grid">
                    
                    <!-- Card 1: Sales (Static for now) -->
                    <div class="stat-card">
                        <span class="stat-label">Today's Sales</span>
                      <span class="stat-value">₹<?php echo number_format($today_sales, 2); ?></span>
                       <span class="stat-meta text-green">Live tracking enabled</span>
                    </div>

                    <!-- Card 2: Total Users (Dynamic from Database) -->
                    <div class="stat-card">
                        <span class="stat-label">Total Users</span>
                        <span class="stat-value"><?php echo $total_users; ?></span>
                        <span class="stat-meta text-blue">Active in system</span>
                    </div>

                    <!-- Card 3: Products (Static for now) -->
                    <div class="stat-card">
                        <span class="stat-label">Total Products</span>
                        <span class="stat-value"><?php echo $total_products; ?></span>
                        <span class="stat-meta">24 added this week</span>
                    </div>

                    <!-- Card 4: Low Stock Alert (Danger style) -->
                    <!-- Card 4: Low Stock Alert (Now 100% Dynamic) -->
<div class="stat-card" style="border-left: 4px solid #ef4444;">
    <span class="stat-label" style="color: #ef4444;">Low Stock Alert</span>
    <!-- Now using the real count from database -->
    <span class="stat-value"><?php echo $low_stock_count; ?> Items</span>
    <span class="stat-meta">Items below threshold</span>
</div>
                </div>
<!-- --- SMART INVENTORY ALERTS (TABLE) --- -->
<div style="margin-top: 40px;">
    <h3 style="margin-bottom: 20px; font-size: 18px; color: #111827;">⚠️ Critical Stock Alerts</h3>
    <div class="table-card">
        <table class="product-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Current Stock</th>
                    <th>Alert Level</th>
                    <th style="text-align: center;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                /* fetch only items that need reordering (limit 5 for dashboard) */
                $alert_sql = "SELECT * FROM products WHERE stock_level <= low_stock_threshold ORDER BY stock_level ASC LIMIT 5";
                $alert_res = mysqli_query($conn, $alert_sql);
                
                if(mysqli_num_rows($alert_res) > 0):
                    while($item = mysqli_fetch_assoc($alert_res)): 
                ?>
                <tr style="background: #fff5f5;">
                    <td style="font-weight: 600; color: #b91c1c;"><?php echo $item['product_name']; ?></td>
                    <td style="font-weight: 700;"><?php echo $item['stock_level']; ?> Units</td>
                    <td style="color: #6b7280;"><?php echo $item['low_stock_threshold']; ?></td>
                    <td style="text-align: center;">
                        <a href="edit_product.php?id=<?php echo $item['id']; ?>" style="color: #2563eb; text-decoration: none; font-size: 12px; font-weight: 700; border: 1px solid #2563eb; padding: 4px 10px; border-radius: 4px;">Refill Stock</a>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="4" style="text-align: center; padding: 30px; color: #10b981; font-weight: 600;">✅ All hardware stock levels are healthy!</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
            </main>

        </div> <!-- end of content-area -->
    </div> <!-- end of dashboard-wrapper -->

</body>
</html>