<?php
/* start session to access user info */
session_start();

/* security check: if user is not logged in, redirect to login page */
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
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
                <a href="#" class="nav-item">Products</a>
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

            <!-- dynamic content starts here -->
            <main style="padding: 30px;">
                <h2>Dashboard Overview</h2>
                <p>Welcome back! Here's what's happening with your store today.</p>
                
                <!-- temporary info for teacher -->
                <div style="margin-top: 20px; padding: 20px; background: #fff; border: 1px solid #ddd; border-radius: 8px;">
                    <p>Current Role: <strong><?php echo $_SESSION['user_role']; ?></strong></p>
                    <p>Status: <span style="color: green;">System Online</span></p>
                </div>
            </main>

        </div>
    </div>

</body>
</html>