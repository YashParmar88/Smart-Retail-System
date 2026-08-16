<?php
session_start();
include('includes/db_conn.php');

/* security check: redirect if not logged in */
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

/* --- LOGIC: SAVE NEW PRODUCT --- */
$message = "";
if (isset($_POST['save_product'])) {
    $name     = mysqli_real_escape_string($conn, $_POST['p_name']);
    $sku      = mysqli_real_escape_string($conn, $_POST['p_sku']);
    $cat      = mysqli_real_escape_string($conn, $_POST['p_cat']);
    $price    = mysqli_real_escape_string($conn, $_POST['p_price']);
    $stock    = mysqli_real_escape_string($conn, $_POST['p_stock']);
    $alert    = mysqli_real_escape_string($conn, $_POST['p_alert']);

    $insert_sql = "INSERT INTO products (product_name, sku, category, price, stock_level, low_stock_threshold) 
                   VALUES ('$name', '$sku', '$cat', '$price', '$stock', '$alert')";

    if (mysqli_query($conn, $insert_sql)) {
        $message = "Product added successfully!";
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}

/* fetch all products newest first */
$sql = "SELECT * FROM products ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Smart Shop ERP</title>
    <!-- added versioning to force css refresh -->
    <link rel="stylesheet" href="assets/css/style.css?v=1.3">
</head>
<body>

    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <div class="brand-logo">Smart Shop ERP</div>
            <nav class="nav-links">
                <a href="dashboard.php" class="nav-item">Dashboard</a>
                <a href="products.php" class="nav-item active">Products</a>
                <a href="categories.php" class="nav-item">Categories</a>
                <a href="pos.php" class="nav-item">Billing (POS)</a>
                <a href="logout.php" class="nav-item" style="color: #ef4444; margin-top: auto;">Logout</a>
            </nav>
        </aside>

        <div class="content-area">
            <header class="main-header">
                <div class="header-search"><input type="text" placeholder="Search products..."></div>
                <div class="user-nav"><span class="user-name">Welcome, <?php echo $_SESSION['user_name']; ?></span></div>
            </header>

            <main style="padding: 30px;">
                <!-- success or delete message alert -->
                <?php if($message != "" || isset($_GET['msg'])): ?>
                    <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">
                        <?php echo $message; ?>
                        <?php if(isset($_GET['msg'])) echo $_GET['msg']; ?>
                    </div>
                <?php endif; ?>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                    <div>
                        <h2 style="font-size: 24px;">Product Catalog</h2>
                        <p style="color: #6b7280; font-size: 14px;">Manage your inventory catalog and stock levels.</p>
                    </div>
                    <button onclick="openModal()" class="login-btn" style="width: auto; padding: 12px 24px;">+ Add New Product</button>
                </div>

                <div class="table-card">
                    <table class="product-table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>SKU</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock Status</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($result)): 
                                $is_low = ($row['stock_level'] <= $row['low_stock_threshold']);
                            ?>
                            <tr>
                                <td style="font-weight: 600;"><?php echo $row['product_name']; ?></td>
                                <td style="font-family: monospace; color: #6b7280;"><?php echo $row['sku']; ?></td>
                                <td><?php echo $row['category']; ?></td>
                                <td style="font-weight: 700;">$<?php echo number_format($row['price'], 2); ?></td>
                                <td>
                                    <div class="stock-bar-container">
                                        <div class="stock-bar-fill <?php echo $is_low ? 'bg-red':'bg-green'; ?>" style="width: <?php echo min($row['stock_level'], 100); ?>%;"></div>
                                    </div>
                                    <span class="<?php echo $is_low ? 'text-red':'text-green'; ?>"><?php echo $row['stock_level']; ?> Units</span>
                                </td>
                                <!-- Added Action Buttons -->
                                <td style="text-align: right;">
                                    <div class="action-btns" style="justify-content: flex-end;">
                                     <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn-icon btn-edit">Edit</a>
                                        <a href="delete_product.php?id=<?php echo $row['id']; ?>" 
                                           class="btn-icon btn-delete" 
                                           onclick="return confirm('Are you sure you want to delete this product?')">
                                           Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <!-- --- ADD PRODUCT MODAL --- -->
    <div class="modal-overlay" id="productModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 style="font-weight: 700;">Add New Product</h3>
                <span class="close-modal" onclick="closeModal()">&times;</span>
            </div>
            
            <form action="products.php" method="POST">
                <div class="form-grid">
                    <div class="input-group full-width">
                        <label>Product Name</label>
                        <input type="text" name="p_name" placeholder="e.g. Wireless Mouse" required>
                    </div>
                    <div class="input-group">
                        <label>SKU / Barcode</label>
                        <input type="text" name="p_sku" placeholder="SKU-001" required>
                    </div>
                    <div class="input-group">
                        <label>Category</label>
                        <select name="p_cat" style="width: 100%; padding: 12px; border-radius: 8px; border: 1.5px solid #e5e7eb;">
                            <option>Electronics</option>
                            <option>Beverages</option>
                            <option>Fitness</option>
                            <option>Groceries</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Selling Price ($)</label>
                        <input type="number" step="0.01" name="p_price" placeholder="0.00" required>
                    </div>
                    <div class="input-group">
                        <label>Initial Stock</label>
                        <input type="number" name="p_stock" placeholder="0" required>
                    </div>
                    <div class="input-group full-width">
                        <label>Low Stock Alert Level</label>
                        <input type="number" name="p_alert" value="10" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" name="save_product" class="login-btn" style="width: auto;">Save Product</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() { document.getElementById('productModal').style.display = 'flex'; }
        function closeModal() { document.getElementById('productModal').style.display = 'none'; }
    </script>

</body>
</html>