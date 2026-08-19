<?php
/* start session to track user */
session_start();

/* include database connection */
include('includes/db_conn.php');

/* security: redirect if not logged in */
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

/* fetch all products to show stock status */
$sql = "SELECT * FROM products ORDER BY stock_level ASC";
$result = mysqli_query($conn, $sql);

/* count low stock items for the header badge */
$low_stock_res = mysqli_query($conn, "SELECT id FROM products WHERE stock_level <= low_stock_threshold");
$low_count = mysqli_num_rows($low_stock_res);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Tracking - Smart retail System</title>
    <!-- link stylesheet with versioning -->
    <link rel="stylesheet" href="assets/css/style.css?v=3.0">
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar Navigation -->
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

        <div class="content-area">
            <header class="main-header">
                <h2 style="font-size: 20px;">Inventory Management</h2>
                <div class="user-nav">
                    <span style="background:#fee2e2; color:#ef4444; padding:5px 12px; border-radius:20px; font-size:12px; font-weight:700;">
                        <?php echo $low_count; ?> Low Stock Alerts
                    </span>
                </div>
            </header>

            <main style="padding: 30px;">
                <div style="margin-bottom: 25px;">
                    <h2 style="font-size: 24px;">Stock Levels</h2>
                    <p style="color: #6b7280;">Monitor and track real-time product availability.</p>
                </div>

                <div class="table-card">
                    <table class="product-table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>SKU</th>
                                <th>Current Stock</th>
                                <th>Threshold</th>
                                <th>Status</th>
                                <th style="text-align:center;">Quick Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($result)): 
                                $is_low = ($row['stock_level'] <= $row['low_stock_threshold']);
                            ?>
                            <tr style="<?php if($is_low) echo 'background:#fff5f5;'; ?>">
                                <td style="font-weight: 600;"><?php echo $row['product_name']; ?></td>
                                <td style="color:#6b7280; font-family:monospace;"><?php echo $row['sku']; ?></td>
                                <td style="font-weight: 700; font-size:16px;">
                                    <?php echo $row['stock_level']; ?>
                                </td>
                                <td style="color:#9ca3af;"><?php echo $row['low_stock_threshold']; ?></td>
                                <td>
                                    <?php if($is_low): ?>
                                        <span class="badge" style="background:#fee2e2; color:#ef4444;">REORDER NOW</span>
                                    <?php else: ?>
                                        <span class="badge" style="background:#dcfce7; color:#10b981;">HEALTHY</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align:center;">
                                    <a href="edit_product.php?id=<?php echo $row['id']; ?>" style="text-decoration:none; color:#2563eb; font-size:12px; font-weight:600;">Update Stock</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>
</body>
</html>