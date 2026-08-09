<?php
/* Step 1: Start the session to remember user login */
session_start();

/* Step 2: Include the database connection file */
include('includes/db_conn.php');

/* Variable to store error messages */
$error = "";

/* Step 3: Check if the login button is clicked */
if (isset($_POST['login_btn'])) {
    
    /* Get data from form and protect from simple hackers */
    $email = mysqli_real_escape_string($conn, $_POST['user_email']);
    $password = mysqli_real_escape_string($conn, $_POST['user_password']);

    /* Step 4: Search for this user in database */
    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    /* Step 5: If user found (count is 1) */
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        /* Store user info in Session */
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'];

        /* Redirect to Dashboard (which we will build soon) */
        header("Location: dashboard.php");
        exit();
    } else {
        /* If no user found, show error */
        $error = "Invalid Email or Password!";
    }
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Smart Shop ERP</title>
    <!-- link to our css file -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- main container for split screen -->
    <div class="login-wrapper">
        
        <!-- left side branding -->
        <div class="login-left">
            <div class="brand-info">
                <h1>Smart Shop ERP</h1>
                <p>Precision-engineered for the modern retail ecosystem. Manage inventory, billing, and reporting with quiet competence.</p>
            </div>
        </div>

        <!-- right side login form -->
        <div class="login-right">
            <div class="form-card">
                <h2>Welcome Back</h2>
                <p>Please enter your credentials to access the terminal.</p>

                <form action="index.php" method="POST">
               
                <!-- show error message if login fails -->
<?php if($error != "") { ?>
    <div style="color: red; background: #fee2e2; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 13px; text-align: center; border: 1px solid #fecaca;">
        <?php echo $error; ?>
    </div>
<?php } ?>
                    <div class="input-group">
                        <label>EMAIL ADDRESS</label>
                        <input type="email" name="user_email" placeholder="admin@smartshop.com" required>
                    </div>

                    <div class="input-group">
                        <label>PASSWORD</label>
                        <input type="password" name="user_password" placeholder="••••••••" required>
                    </div>

                   <button type="submit" name="login_btn" class="login-btn">Login to Dashboard &rarr;</button>
                </form>

                <p class="footer-text">New to the platform? <a href="#">Request Access</a></p>
            </div>
        </div>

    </div>

</body>
</html>