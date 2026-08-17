<?php
/* start session and include db connection */
session_start();
include('includes/db_conn.php');

/* security: guard check */
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

/* fetch all sales from database - latest first */
$sql = "SELECT * FROM sales ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales History - Smart Shop ERP</title>
    <link rel="stylesheet" href="assets/css/style.css?v=2.3">
</head>
<body>
    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <div class="brand-logo">SuperMarket</div>
            <nav class="nav-links">
                <a href="dashboard.php" class="nav-item">Dashboard</a>
                <a href="products.php" class="nav-item">Products</a>
                <a href="categories.php" class="nav-item">Categories</a>
                <a href="pos.php" class="nav-item">Billing Counter</a>
                <a href="reports.php" class="nav-item active">Reports</a>
                <a href="logout.php" class="nav-item" style="color: #ef4444; margin-top: auto;">Logout</a>
            </nav>
        </aside>

        <div class="content-area">
            <header class="main-header">
                <h2 style="font-size: 20px;">Sales History & Reports</h2>
                <div class="user-nav">Admin: <strong><?php echo $_SESSION['user_name']; ?></strong></div>
            </header>

            <main style="padding: 30px;">
                <div style="margin-bottom: 25px;">
                    <h2 style="font-size: 24px;">All Transactions</h2>
                    <p style="color: #6b7280;">View and track all generated invoices.</p>
                </div>

                <div class="table-card">
                    <table class="product-table">
                        <thead>
                            <tr>
                                <th>Invoice No</th>
                                <th>Date & Time</th>
                                <th>Customer</th>
                                <th>Method</th>
                                <th style="text-align: right;">Total Amount</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td style="font-weight: 700; color: #2563eb;"><?php echo $row['invoice_no']; ?></td>
                                    <td style="font-size: 13px; color: #6b7280;"><?php echo date('d M Y, h:i A', strtotime($row['created_at'])); ?></td>
                                    <td style="font-weight: 600;"><?php echo $row['customer_name']; ?></td>
                                    <td><span class="badge" style="background:#f3f4f6; color:#374151;"><?php echo $row['payment_method']; ?></span></td>
                                    <td style="text-align: right; font-weight: 700;">₹<?php echo number_format($row['grand_total'], 2); ?></td>
                                    <td style="text-align: center;">
                                        <a href="print_bill.php?id=<?php echo $row['id']; ?>" target="_blank" style="text-decoration:none; color:#111827; font-size:12px; font-weight:600; border:1px solid #ddd; padding:5px 10px; border-radius:5px;">View Receipt</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" style="text-align:center; padding: 50px;">No sales recorded yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>
</body>
</html>