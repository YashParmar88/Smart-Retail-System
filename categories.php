<?php
session_start();
include('includes/db_conn.php');

/* security: guard check */
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$message = "";

/* --- LOGIC: ADD CATEGORY --- */
if (isset($_POST['add_cat'])) {
    $name = mysqli_real_escape_string($conn, $_POST['cat_name']);
    $desc = mysqli_real_escape_string($conn, $_POST['cat_desc']);

    $check = mysqli_query($conn, "SELECT id FROM categories WHERE category_name = '$name'");
    if(mysqli_num_rows($check) > 0) {
        $message = "Error: Category already exists!";
    } else {
        mysqli_query($conn, "INSERT INTO categories (category_name, description) VALUES ('$name', '$desc')");
        $message = "Category added successfully!";
    }
}

/* --- LOGIC: DELETE CATEGORY --- */
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM categories WHERE id = $id");
    header("Location: categories.php?msg=Deleted");
    exit();
}

/* --- TASK: SEARCH LOGIC --- */
$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    /* filter categories by name using LIKE */
    $sql = "SELECT * FROM categories WHERE category_name LIKE '%$search%' ORDER BY category_name ASC";
} else {
    /* default: fetch all categories */
    $sql = "SELECT * FROM categories ORDER BY category_name ASC";
}
$cats = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Categories - Smart retail System</title>
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
            <header class="main-header">
                <!-- UPDATED SEARCH FORM -->
                <div class="header-search">
                    <form action="categories.php" method="GET">
                        <input type="text" name="search" placeholder="Search categories..." value="<?php echo htmlspecialchars($search); ?>">
                    </form>
                </div>
                <div class="user-nav"><span class="user-name">Admin: <?php echo $_SESSION['user_name']; ?></span></div>
            </header>

            <main style="padding: 30px;">
                <?php if($message != ""): ?>
                    <div style="background:#fee2e2; color:#b91c1c; padding:15px; border-radius:8px; margin-bottom:20px; font-weight:600;">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                    <div>
                        <h2 style="font-size: 24px;">Categories</h2>
                        <p style="color: #6b7280;">Manage product groupings and departments.</p>
                    </div>
                    <button onclick="openModal()" class="login-btn" style="width:auto; padding:12px 24px;">+ New Category</button>
                </div>

                <div class="table-card">
                    <table class="product-table">
                        <thead>
                            <tr><th>Name</th><th>Description</th><th style="text-align:right;">Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($cats) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($cats)): ?>
                                <tr>
                                    <td style="font-weight:600;"><?php echo $row['category_name']; ?></td>
                                    <td style="color:#6b7280;"><?php echo $row['description']; ?></td>
                                    <td style="text-align:right;">
                                        <a href="categories.php?delete=<?php echo $row['id']; ?>" 
                                           style="color:#ef4444; text-decoration:none; font-size:13px; font-weight:600;"
                                           onclick="return confirm('Deleting category will not delete products. Continue?')">Delete</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="3" style="text-align:center; padding:30px; color:#9ca3af;">No categories found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <!-- ADD MODAL REMAINS SAME -->
    <div class="modal-overlay" id="catModal">
        <div class="modal-content" style="width:400px;">
            <div class="modal-header">
                <h3>Add Category</h3>
                <span style="cursor:pointer;" onclick="closeModal()">&times;</span>
            </div>
            <form action="categories.php" method="POST">
                <div class="input-group">
                    <label>Category Name</label>
                    <input type="text" name="cat_name" required>
                </div>
                <div class="input-group">
                    <label>Description</label>
                    <input type="text" name="cat_desc">
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal()" class="logout-btn" style="background:#eee; color:#333;">Cancel</button>
                    <button type="submit" name="add_cat" class="login-btn" style="width:auto;">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() { document.getElementById('catModal').style.display='flex'; }
        function closeModal() { document.getElementById('catModal').style.display='none'; }
    </script>
</body>
</html>