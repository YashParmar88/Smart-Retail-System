<?php
/* session and db connection */
session_start();
include('includes/db_conn.php');

/* security gate */
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$message = "";

/* logic: add new customer */
if (isset($_POST['add_customer'])) {
    $name    = mysqli_real_escape_string($conn, $_POST['c_name']);
    $phone   = mysqli_real_escape_string($conn, $_POST['c_phone']);
    $email   = mysqli_real_escape_string($conn, $_POST['c_email']);
    $address = mysqli_real_escape_string($conn, $_POST['c_addr']);

    /* check if phone number already exists */
    $check = mysqli_query($conn, "SELECT id FROM customers WHERE phone = '$phone'");
    if(mysqli_num_rows($check) > 0) {
        $message = "Error: Customer with this phone number already exists!";
    } else {
        $sql = "INSERT INTO customers (customer_name, phone, email, address) 
                VALUES ('$name', '$phone', '$email', '$address')";
        if (mysqli_query($conn, $sql)) {
            $message = "Customer registered successfully!";
        }
    }
}

/* logic: delete customer */
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM customers WHERE id = $id");
    header("Location: customers.php");
    exit();
}

/* --- TASK: SEARCH LOGIC FOR CUSTOMERS --- */
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
    /* query to filter customers by name or phone number */
    $sql = "SELECT * FROM customers WHERE customer_name LIKE '%$search_query%' OR phone LIKE '%$search_query%' ORDER BY id DESC";
} else {
    /* default query to fetch all */
    $sql = "SELECT * FROM customers ORDER BY id DESC";
}
$res = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customers - Smart retail System</title>
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
                <!-- UPDATED SEARCH FORM -->
                <div class="header-search">
                    <form action="customers.php" method="GET">
                        <input type="text" name="search" placeholder="Search customers by name or phone..." value="<?php echo htmlspecialchars($search_query); ?>">
                    </form>
                </div>
                <div class="user-nav">Welcome, <?php echo $_SESSION['user_name']; ?></div>
            </header>

            <main style="padding: 30px;">
                <!-- error message alert -->
                <?php if($message != "" && strpos($message, 'Error') !== false): ?>
                    <div style="background:#fee2e2; color:#b91c1c; padding:15px; border-radius:8px; margin-bottom:20px; font-weight:600;">
                        <?php echo $message; ?>
                    </div>
                <?php elseif($message != ""): ?>
                    <div style="background:#dcfce7; color:#166534; padding:15px; border-radius:8px; margin-bottom:20px; font-weight:600;">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                    <div>
                        <h2 style="font-size: 24px;">Customer Directory</h2>
                        <p style="color: #6b7280;">Manage your retail customer relationships and contact info.</p>
                    </div>
                    <button onclick="openModal()" class="login-btn" style="width:auto; padding:12px 24px;">+ New Customer</button>
                </div>

                <div class="table-card">
                    <table class="product-table">
                        <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>Phone Number</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($res) > 0): ?>
                                <?php while($c = mysqli_fetch_assoc($res)): ?>
                                <tr>
                                    <td style="font-weight: 700; color: #111827;"><?php echo $c['customer_name']; ?></td>
                                    <td style="font-family: monospace; font-weight: 600;"><?php echo $c['phone']; ?></td>
                                    <td style="color:#6b7280;"><?php echo $c['email']; ?></td>
                                    <td style="font-size: 13px; color:#4b5563;"><?php echo $c['address']; ?></td>
                                    <td style="text-align:right;">
                                        <a href="customers.php?delete=<?php echo $c['id']; ?>" 
                                           style="color:#ef4444; text-decoration:none; font-weight:600; font-size:13px;"
                                           onclick="return confirm('Remove this customer from records?')">Delete</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" style="text-align:center; padding:40px; color: #9ca3af;">No customers found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <!-- ADD CUSTOMER MODAL (Remaing same) -->
    <div class="modal-overlay" id="custModal">
        <div class="modal-content" style="width:450px;">
            <div class="modal-header">
                <h3>Register New Customer</h3>
                <span style="cursor:pointer;" onclick="closeModal()">&times;</span>
            </div>
            <form action="customers.php" method="POST">
                <div class="input-group">
                    <label>Full Name</label>
                    <input type="text" name="c_name" placeholder="John Doe" required>
                </div>
                <div class="input-group">
                    <label>Phone Number (Unique)</label>
                    <input type="text" name="c_phone" placeholder="10-digit number" required>
                </div>
                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="c_email" placeholder="john@example.com">
                </div>
                <div class="input-group">
                    <label>Home Address</label>
                    <textarea name="c_addr" style="width: 100%; padding: 12px; border-radius: 8px; border: 1.5px solid #e5e7eb; font-family:inherit;"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal()" class="logout-btn" style="background:#eee; color:#333;">Cancel</button>
                    <button type="submit" name="add_customer" class="login-btn" style="width:auto;">Register Customer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() { document.getElementById('custModal').style.display='flex'; }
        function closeModal() { document.getElementById('custModal').style.display='none'; }
    </script>
</body>
</html>