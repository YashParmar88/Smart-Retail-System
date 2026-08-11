<?php
/* start session to access user details */
session_start();

/* include database connection file */
include('includes/db_conn.php');

/* security check: redirect to login if not authenticated */
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

/* fetch all products from the database, newest first */
$sql = "SELECT * FROM products ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Smart Shop ERP</title>
    <!-- linking the main professional stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- main dashboard layout container -->
    <div class="dashboard-wrapper">
        
        <!-- sidebar navigation menu -->
        <aside class="sidebar">
            <div class="brand-logo">Smart Shop ERP</div>
            
            <nav class="nav-links">
                <a href="dashboard.php" class="nav-item">Dashboard</a>
                <a href="products.php" class="nav-item active">Products</a>
                <a href="#" class="nav-item">Categories</a>
                <a href="#" class="nav-item">Suppliers</a>
                <a href="#" class="nav-item">Customers</a>
                <a href="logout.php" class="nav-item" style="color: #ef4444; margin-top: auto;">Logout</a>
            </nav>
        </aside>

        <!-- main content area for table and header -->
        <div class="content-area">
            
            <!-- top navigation header -->
            <header class="main-header">
                <div class="header-search">
                    <input type="text" placeholder="Search products by name or SKU...">
                </div>

                <div class="user-nav">
                    <span class="user-name">Welcome, <?php echo $_SESSION['user_name']; ?></span>
                </div>
            </header>

            <!-- main product management area -->
            <main style="padding: 30px;">
                
                <!-- page title and action button -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                    <div>
                        <h2 style="font-size: 24px; color: #111827;">Product Catalog</h2>
                        <p style="color: #6b7280; font-size: 14px;">Manage your inventory catalog and stock levels.</p>
                    </div>
                    <button style="padding: 12px 24px; background: #000; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">+ Add New Product</button>
                </div>

                <!-- product data table card -->
                <div class="table-card">
                    <table class="product-table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>SKU</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            /* checking if there are any products in database */
                            if(mysqli_num_rows($result) > 0) {
                                
                                /* loop through each product row */
                                while($row = mysqli_fetch_assoc($result)) { 
                                    
                                    /* low stock detection logic */
                                    $is_low = ($row['stock_level'] <= $row['low_stock_threshold']);
                                    $color_class = $is_low ? 'bg-red' : 'bg-green';
                                    $text_class = $is_low ? 'text-red' : 'text-green';
                            ?>
                            <tr>
                                <!-- product name column -->
                                <td style="font-weight: 600; color: #111827;">
                                    <?php echo $row['product_name']; ?>
                                </td>

                                <!-- sku column -->
                                <td style="color: #6b7280; font-family: monospace;">
                                    <?php echo $row['sku']; ?>
                                </td>

                                <!-- category column -->
                                <td><?php echo $row['category']; ?></td>

                                <!-- price column with formatting -->
                                <td style="font-weight: 700;">
                                    $<?php echo number_format($row['price'], 2); ?>
                                </td>

                                <!-- stock bar and units column -->
                                <td>
                                    <div class="stock-bar-container">
                                        <div class="stock-bar-fill <?php echo $color_class; ?>" style="width: <?php echo min($row['stock_level'], 100); ?>%;"></div>
                                    </div>
                                    <span class="<?php echo $text_class; ?>">
                                        <?php echo $row['stock_level']; ?> Units
                                    </span>
                                </td>
                            </tr>
                            <?php 
                                } /* end of while loop */
                            } else {
                                /* show message if table is empty */
                                echo "<tr><td colspan='5' style='text-align:center; padding: 50px; color: #6b7280;'>No products found in the database.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            </main>
        </div> <!-- end of content-area -->
    </div> <!-- end of dashboard-wrapper -->

</body>
</html>