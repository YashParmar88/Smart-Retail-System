<?php
/* session and db connection */
session_start();
include('includes/db_conn.php');

/* gatekeeper security */
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$message = "";

/* logic: add new supplier */
if (isset($_POST['add_supplier'])) {
    $name    = mysqli_real_escape_string($conn, $_POST['s_name']);
    $contact = mysqli_real_escape_string($conn, $_POST['s_contact']);
    $phone   = mysqli_real_escape_string($conn, $_POST['s_phone']);
    $email   = mysqli_real_escape_string($conn, $_POST['s_email']);
    $address = mysqli_real_escape_string($conn, $_POST['s_addr']);

    $sql = "INSERT INTO suppliers (supplier_name, contact_person, phone, email, address) 
            VALUES ('$name', '$contact', '$phone', '$email', '$address')";
    
    if (mysqli_query($conn, $sql)) {
        $message = "Supplier added successfully!";
    }
}

/* logic: delete supplier */
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM suppliers WHERE id = $id");
    header("Location: suppliers.php");
    exit();
}

/* --- TASK: SEARCH LOGIC FOR SUPPLIERS --- */
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
    /* query to filter suppliers by name or contact person */
    $sql = "SELECT * FROM suppliers WHERE supplier_name LIKE '%$search_query%' OR contact_person LIKE '%$search_query%' ORDER BY id DESC";
} else {
    /* default: fetch all suppliers */
    $sql = "SELECT * FROM suppliers ORDER BY id DESC";
}
$res = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Suppliers - Smart retail System</title>
    <link rel="stylesheet" href="assets/css/style.css?v=3.0">
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
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
                    <form action="suppliers.php" method="GET">
                        <input type="text" name="search" placeholder="Search suppliers..." value="<?php echo htmlspecialchars($search_query); ?>">
                    </form>
                </div>
                <div class="user-nav">Welcome, <?php echo $_SESSION['user_name']; ?></div>
            </header>

            <main style="padding: 30px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                    <div>
                        <h2 style="font-size: 24px;">Suppliers Directory</h2>
                        <p style="color: #6b7280;">Manage your wholesale partners and supply chain contacts.</p>
                    </div>
                    <button onclick="openModal()" class="login-btn" style="width:auto; padding:12px 24px;">+ Add Supplier</button>
                </div>

                <div class="table-card">
                    <table class="product-table">
                        <thead>
                            <tr>
                                <th>Supplier Name</th>
                                <th>Contact Person</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($res) > 0): ?>
                                <?php while($s = mysqli_fetch_assoc($res)): ?>
                                <tr>
                                    <td style="font-weight: 700; color: #111827;"><?php echo $s['supplier_name']; ?></td>
                                    <td><?php echo $s['contact_person']; ?></td>
                                    <td style="font-family: monospace;"><?php echo $s['phone']; ?></td>
                                    <td><?php echo $s['email']; ?></td>
                                    <td style="text-align:right;">
                                        <a href="suppliers.php?delete=<?php echo $s['id']; ?>" 
                                           style="color:#ef4444; text-decoration:none; font-weight:600;"
                                           onclick="return confirm('Delete this supplier?')">Delete</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" style="text-align:center; padding:40px; color:#9ca3af;">No suppliers found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <!-- ADD SUPPLIER MODAL -->
    <div class="modal-overlay" id="supModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add New Supplier</h3>
                <span style="cursor:pointer;" onclick="closeModal()">&times;</span>
            </div>
            <form action="suppliers.php" method="POST">
                <div class="input-group">
                    <label>Supplier / Company Name</label>
                    <input type="text" name="s_name" required>
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                    <div class="input-group">
                        <label>Contact Person</label>
                        <input type="text" name="s_contact">
                    </div>
                    <div class="input-group">
                        <label>Phone Number</label>
                        <input type="text" name="s_phone">
                    </div>
                </div>
                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="s_email">
                </div>
                <div class="input-group">
                    <label>Office Address</label>
                    <input type="text" name="s_addr">
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal()" class="logout-btn" style="background:#eee; color:#333;">Cancel</button>
                    <button type="submit" name="add_supplier" class="login-btn" style="width:auto;">Save Supplier</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() { document.getElementById('supModal').style.display='flex'; }
        function closeModal() { document.getElementById('supModal').style.display='none'; }
    </script>
</body>
</html>