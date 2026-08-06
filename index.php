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
                    
                    <div class="input-group">
                        <label>EMAIL ADDRESS</label>
                        <input type="email" name="user_email" placeholder="admin@smartshop.com" required>
                    </div>

                    <div class="input-group">
                        <label>PASSWORD</label>
                        <input type="password" name="user_password" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="login-btn">Login to Dashboard &rarr;</button>
                </form>

                <p class="footer-text">New to the platform? <a href="#">Request Access</a></p>
            </div>
        </div>

    </div>

</body>
</html>