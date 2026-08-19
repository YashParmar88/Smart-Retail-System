<?php
/* start session to track user login */
session_start();

/* prevent browser from caching this page (stops old data from showing after logout) */
header("Cache-Control: no-cache, no-store, must-revalidate"); 
header("Pragma: no-cache"); 
header("Expires: 0"); 

/* include database connection logic */
include('includes/db_conn.php');

/* variable to store login errors */
$error = "";

/* process login form when button is clicked */
if (isset($_POST['login_btn'])) {
    
    /* sanitize inputs to prevent SQL Injection */
    $email = mysqli_real_escape_string($conn, $_POST['user_email']);
    $password = mysqli_real_escape_string($conn, $_POST['user_password']);

    /* query to find the user in database */
    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    /* check if user credentials are valid */
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        /* store user information in session variables */
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'];

        /* redirect to main dashboard */
        header("Location: dashboard.php");
        exit();
    } else {
        /* set error message for invalid login */
        $error = "Invalid Email or Password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Shree Ram Hardware</title>
    <!-- main stylesheet with version for cache busting -->
    <link rel="stylesheet" href="assets/css/style.css?v=3.0">
</head>
<body>

    <!-- main login screen container -->
    <div class="login-wrapper">
        
        <!-- left side: brand branding area -->
        <div class="login-left">
            <div class="brand-info">
                <h1 style="font-size: 36px; margin-bottom: 15px;">Shree Ram Hardware</h1>
                <p>Precision-engineered management system. Track inventory, billing, and sales with absolute clarity.</p>
            </div>
        </div>

        <!-- right side: authentication form -->
        <div class="login-right">
            <div class="form-card">
                <h2>Welcome Back</h2>
                <p>Enter your credentials to access the terminal.</p>

                <!-- added autocomplete="off" to keep the form clean -->
                <form action="index.php" method="POST" autocomplete="off">
               
                    <!-- display login errors if any -->
                    <?php if($error != ""): ?>
                        <div style="color: red; background: #fee2e2; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 13px; text-align: center; border: 1px solid #fecaca;">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <div class="input-group">
                        <label>EMAIL ADDRESS</label>
                        <!-- added autocomplete="off" -->
                        <input type="email" name="user_email" placeholder="admin@shreeram.com" autocomplete="off" required>
                    </div>

                    <div class="input-group">
                        <label>PASSWORD</label>
                        <!-- added autocomplete="new-password" to trick modern browsers -->
                        <input type="password" name="user_password" placeholder="••••••••" autocomplete="new-password" required>
                    </div>

                   <button type="submit" name="login_btn" class="login-btn">Login to Dashboard &rarr;</button>
                </form>

            </div>
        </div>

    </div>

</body>
</html>