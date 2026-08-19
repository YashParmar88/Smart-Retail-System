<?php
/* start session and include db connection */
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

/* --- TASK: SEARCH LOGIC --- */
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
    $sql = "SELECT * FROM products WHERE product_name LIKE '%$search_query%' OR sku LIKE '%$search_query%' ORDER BY id DESC";
} else {
    $sql = "SELECT * FROM products ORDER BY id DESC";
}
$result = mysqli_query($conn, $sql);

/* --- FETCH CATEGORIES FOR THE DROPDOWN --- */
$cat_list = mysqli_query($conn, "SELECT category_name FROM categories ORDER BY category_name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory - Shree Ram Hardware</title>
    <link rel="stylesheet" href="assets/css/style.css?v=3.1">
</head>
<body>

    <div class="dashboard-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="brand-logo">
                <span>🛠️</span> Shree Ram Hardware
            </div>
            <nav class="nav-links">
                <a href="dashboard.php" class="nav-item">Dashboard</a>
                <a href="pos.php" class="nav-item">Billing Counter</a>
                <a href="customers.php" class="nav-item">Customers</a>
                <?php if($_SESSION['user_role'] == 'admin'): ?>
                    <a href="products.php" class="nav-item active">Products</a>
                    <a href="categories.php" class="nav-item">Categories</a>
                    <a href="suppliers.php" class="nav-item">Suppliers</a>
                    <a href="inventory.php" class="nav-item">Inventory</a>
                    <a href="reports.php" class="nav-item">Sales History</a>
                <?php endif; ?>
                <a href="logout.php" class="nav-item" style="color: #ef4444; margin-top: auto;">Logout</a>
            </nav>
        </aside>

        <div class="content-area">
            <header class="main-header">
                <div class="header-search">
                    <form action="products.php" method="GET">
                        <input type="text" name="search" placeholder="Search by name or SKU..." value="<?php echo htmlspecialchars($search_query); ?>">
                    </form>
                </div>
                <div class="user-nav"><span class="user-name">Welcome, <?php echo $_SESSION['user_name']; ?></span></div>
            </header>

            <main style="padding: 30px;">
                <?php if($message != "" || isset($_GET['msg'])): ?>
                    <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">
                        <?php echo $message; if(isset($_GET['msg'])) echo $_GET['msg']; ?>
                    </div>
                <?php endif; ?>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                    <div>
                        <h2 style="font-size: 24px;">Product Catalog</h2>
                        <p style="color: #6b7280; font-size: 14px;">Manage hardware inventory and stock levels.</p>
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
                                <td style="font-weight: 600; color: #111827;"><?php echo $row['product_name']; ?></td>
                                <td style="color: #6b7280; font-family: monospace;"><?php echo $row['sku']; ?></td>
                                <td><?php echo $row['category']; ?></td>
                                <td style="font-weight: 700;">₹<?php echo number_format($row['price'], 2); ?></td>
                                <td>
                                    <div class="stock-bar-container">
                                        <div class="stock-bar-fill <?php echo $is_low ? 'bg-red':'bg-green'; ?>" style="width: <?php echo min($row['stock_level'], 100); ?>%;"></div>
                                    </div>
                                    <span class="<?php echo $is_low ? 'text-red':'text-green'; ?>"><?php echo $row['stock_level']; ?> Units</span>
                                </td>
                                <td style="text-align: right;">
                                    <div class="action-btns" style="justify-content: flex-end;">
                                     <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn-icon btn-edit">Edit</a>
                                        <a href="delete_product.php?id=<?php echo $row['id']; ?>" class="btn-icon btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
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

    <!-- --- ADD PRODUCT MODAL (UPDATED FOR HARDWARE) --- -->
    <div class="modal-overlay" id="productModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 style="font-weight: 700;">Add Hardware Product</h3>
                <span class="close-modal" onclick="closeModal()">&times;</span>
            </div>
            
            <form action="products.php" method="POST">
                <div class="form-grid">
                    <div class="input-group full-width">
                        <label>Product Name</label>
                        <!-- Updated placeholder -->
                        <input type="text" name="p_name" placeholder="e.g. Astral CPVC Pipe 1 inch" required>
                    </div>
                    <div class="input-group">
                        <label>SKU / Barcode</label>
                        <input type="text" name="p_sku" placeholder="AST-100" required>
                    </div>
                    <div class="input-group">
                        <label>Category</label>
                        <!-- UPDATED: Category list now comes from database -->
                        <select name="p_cat" style="width: 100%; padding: 12px; border-radius: 8px; border: 1.5px solid #e5e7eb; outline:none;">
                            <?php while($cat = mysqli_fetch_assoc($cat_list)): ?>
                                <option value="<?php echo $cat['category_name']; ?>"><?php echo $cat['category_name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Selling Price (₹)</label>
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
                    <button type="submit" name="save_product" class="login-btn" style="width: auto;">Save to Inventory</button>
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