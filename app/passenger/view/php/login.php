<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Use the same CSS you already built -->
    <link rel="stylesheet" href="/KengGo/app/passenger/view/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-title">Login</div>
        <div class="login-desc">The Fastest SHUTTLE<br>Service in Bakakeng</div>

        <!-- Error message injected by controller -->
        <?php if (!empty($error)) echo "<p style='color:red; text-align:center;'>$error</p>"; ?>

        <form class="login-form" method="POST" action="?page=login">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <a href="#" class="forgot">Forgot your password?</a>
            <button type="submit" class="login-btn">Sign in</button>
            <a href="?page=register" class="login-link">Create new account</a>
        </form>
    </div>
</body>
</html>
