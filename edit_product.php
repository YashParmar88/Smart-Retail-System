<?php
session_start();
include('includes/db_conn.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id']; // get id from URL
$message = "";

/* --- LOGIC: FETCH OLD DATA --- */
$fetch_sql = "SELECT * FROM products WHERE id = $id";
$res = mysqli_query($conn, $fetch_sql);
$data = mysqli_fetch_assoc($res);

/* --- LOGIC: UPDATE DATA ON SUBMIT --- */
if (isset($_POST['update_product'])) {
    $name  = mysqli_real_escape_string($conn, $_POST['p_name']);
    $sku   = mysqli_real_escape_string($conn, $_POST['p_sku']);
    $cat   = mysqli_real_escape_string($conn, $_POST['p_cat']);
    $price = $_POST['p_price'];
    $stock = $_POST['p_stock'];

    $update_sql = "UPDATE products SET 
                   product_name = '$name', 
                   sku = '$sku', 
                   category = '$cat', 
                   price = '$price', 
                   stock_level = '$stock' 
                   WHERE id = $id";

    if (mysqli_query($conn, $update_sql)) {
        header("Location: products.php?msg=Product updated successfully!");
        exit();
    } else {
        $message = "Update failed: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product - Smart retail System</title>
    <link rel="stylesheet" href="assets/css/style.css?v=3.0">
</head>
<body>
    <div class="dashboard-wrapper">
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
            <main style="padding: 40px; max-width: 800px;">
                <div style="margin-bottom: 30px;">
                    <a href="products.php" style="text-decoration:none; color:#6b7280; font-size:14px;">&larr; Back to List</a>
                    <h2 style="margin-top:10px;">Edit Product: <?php echo $data['product_name']; ?></h2>
                </div>

                <div class="table-card" style="padding: 30px;">
                    <form action="" method="POST">
                        <div class="stats-grid" style="grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="input-group" style="grid-column: span 2;">
                                <label>Product Name</label>
                                <input type="text" name="p_name" value="<?php echo $data['product_name']; ?>" required>
                            </div>
                            <div class="input-group">
                                <label>SKU</label>
                                <input type="text" name="p_sku" value="<?php echo $data['sku']; ?>" required>
                            </div>
                            <div class="input-group">
                                <label>Category</label>
                                <select name="p_cat" style="width:100%; padding:12px; border-radius:8px; border:1px solid #ddd;">
                                    <option <?php if($data['category'] == 'Electronics') echo 'selected'; ?>>Electronics</option>
                                    <option <?php if($data['category'] == 'Beverages') echo 'selected'; ?>>Beverages</option>
                                    <option <?php if($data['category'] == 'Fitness') echo 'selected'; ?>>Fitness</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label>Price ($)</label>
                                <input type="number" step="0.01" name="p_price" value="<?php echo $data['price']; ?>" required>
                            </div>
                            <div class="input-group">
                                <label>Stock Level</label>
                                <input type="number" name="p_stock" value="<?php echo $data['stock_level']; ?>" required>
                            </div>
                        </div>

                        <div style="margin-top: 30px;">
                            <button type="submit" name="update_product" class="login-btn" style="width: 200px;">Update Product</button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</body>
</html>