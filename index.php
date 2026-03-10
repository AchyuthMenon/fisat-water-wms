<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FISAT WMS</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body class="flex-center">
    <!-- Decorative background elements -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="login-wrapper glass-panel">
        <div class="login-header">
            <div class="logo-icon pulse-animation">
                <i class="ph-fill ph-drop"></i>
            </div>
            <h1>FISAT WMS</h1>
            <p>Federal Institute of Science and Technology<br>Water Management System</p>
        </div>
        
        <form action="login.php" method="POST" class="login-form">
            <?php if(isset($_GET['error'])): ?>
                <div style="color: red; margin-bottom: 1rem; text-align: center;">Invalid username or password</div>
            <?php endif; ?>
            <div class="input-group">
                <label for="username">Username</label>
                <div class="input-with-icon">
                    <i class="ph ph-user"></i>
                    <input type="text" id="username" name="username" placeholder="Enter your username (admin)" required>
                </div>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <div class="input-with-icon">
                    <i class="ph ph-lock-key"></i>
                    <input type="password" id="password" name="password" placeholder="Enter your password (admin123)" required>
                </div>
            </div>
            
            <button type="submit" class="btn-primary w-full hover-glow" style="margin-top: 1rem;">
                Login to System <i class="ph ph-arrow-right"></i>
            </button>
        </form>
        
        <div class="login-footer">
            <p>Authorized access only. No Sign-up permitted.</p>
        </div>
    </div>
</body>
</html>
