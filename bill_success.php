<?php
session_start();
if (!isset($_GET['id'])) { header("Location: pos.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bill Success - Smart retail System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body style="background:#f3f4f6; display:flex; align-items:center; justify-content:center; height:100vh;">
    <div style="background:#fff; padding:40px; border-radius:20px; text-align:center; box-shadow:0 10px 25px rgba(0,0,0,0.1); width:420px;">
        <div style="font-size: 60px; color:#10b981; margin-bottom:20px;">✅</div>
        <h2 style="margin-bottom:10px;">Bill Saved Successfully!</h2>
        <p style="color:#666; margin-bottom:30px;">Stock has been updated in your inventory automatically.</p>
        
        <!-- print receipt trigger -->
        <a href="print_bill.php?id=<?php echo $_GET['id']; ?>" target="_blank" class="login-btn" style="background:#6b7280; text-decoration:none; display:block; margin-bottom:12px;">Print Receipt</a>
        
        <!-- back to pos for next customer -->
        <a href="pos.php" class="login-btn" style="text-decoration:none; display:block; background:#2563eb;">Next Bill (Done) &rarr;</a>
    </div>
</body>
</html>
