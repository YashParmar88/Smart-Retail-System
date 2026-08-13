<?php
/* start session to access user info */
session_start();

/* include database connection - VERY IMPORTANT */
/* this provides the $conn variable needed for queries */
include('includes/db_conn.php');

/* security check: if user is not logged in, redirect to login page */
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

/* Fetch real count of users from database to show in stats cards */
$user_query = "SELECT id FROM users";
$user_result = mysqli_query($conn, $user_query);
$total_users = mysqli_num_rows($user_result);
/* Fetch real count of products from database */
$prod_query = "SELECT id FROM products";
$prod_result = mysqli_query($conn, $prod_query);
$total_products = mysqli_num_rows($prod_result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Smart Shop ERP</title>
    <!-- link the main stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="dashboard-wrapper">
        
        <!-- sidebar section -->
        <aside class="sidebar">
            <div class="brand-logo">Smart Shop ERP</div>
            
            <nav class="nav-links">
                <a href="dashboard.php" class="nav-item active">Dashboard</a>
                <a href="products.php" class="nav-item">Products</a>
                <a href="#" class="nav-item">Categories</a>
                <a href="#" class="nav-item">Suppliers</a>
                <a href="#" class="nav-item">Customers</a>
                <a href="#" class="nav-item">Inventory</a>
                <a href="#" class="nav-item">Billing (POS)</a>
                <a href="#" class="nav-item">Reports</a>
            </nav>
        </aside>

        <!-- main content area -->
        <div class="content-area">
            
            <!-- top header -->
            <header class="main-header">
                <div class="header-search">
                    <input type="text" placeholder="Search orders, products, or customers...">
                </div>

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
                        <span class="stat-value">$3,452.00</span>
                        <span class="stat-meta text-green">↑ 12% vs yesterday</span>
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
                    <div class="stat-card" style="border-left: 4px solid #ef4444;">
                        <span class="stat-label" style="color: #ef4444;">Low Stock Alert</span>
                        <span class="stat-value">18 Items</span>
                        <span class="stat-meta">Needs reordering</span>
                    </div>

                </div>

            </main>

        </div> <!-- end of content-area -->
    </div> <!-- end of dashboard-wrapper -->

</body>
</html>