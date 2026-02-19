<?php
declare(strict_types=1);

require __DIR__ . '/auth.php';

$errors = [];
$registered = isset($_GET['registered']);
$cartCount = fruti_cart_count();

if (isset($_SESSION['user'])) {
    header('Location: landing.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $errors[] = 'Email and password are required.';
    } else {
        $user = fruti_find_user_by_email($email);
        if ($user === null || !password_verify($password, (string) ($user['password_hash'] ?? ''))) {
            $errors[] = 'Invalid email or password.';
        } else {
            $_SESSION['user'] = [
                'first_name' => $user['first_name'] ?? '',
                'last_name'  => $user['last_name'] ?? '',
                'email'      => $user['email'] ?? '',
            ];

            header('Location: landing.php');
            exit;
        }
    }
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fruti Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="landing-body">
<?php $currentUser = $_SESSION['user'] ?? null; ?>
<header class="topbar">
    <div class="topbar-left">
        <a href="landing.php">
            <img src="images/fruti_logo.jpg" alt="Fruti logo" class="logo">
        </a>
    </div>
    <nav class="main-nav">
        <a href="landing.php" class="nav-link">Home</a>
        <a href="index.php" class="nav-link">Shop</a>
        <a href="#" class="nav-link">Best Sellers</a>
        <a href="#" class="nav-link">About</a>
        <a href="#" class="nav-link active">Contact</a>
    </nav>
    <div class="topbar-right">
        <?php if ($currentUser): ?>
            <button type="button" class="icon-btn cart-btn" aria-label="Shopping cart">
                🛒
                <span class="cart-count"><?= $cartCount ?></span>
            </button>
            <a href="logout.php" class="avatar-link" title="Log out">
                <img src="images/woman.png" alt="Profile" class="profile-avatar">
            </a>
        <?php else: ?>
            <div class="header-auth">
                <a href="login.php" class="header-auth-btn">Login</a>
                <a href="register.php" class="header-auth-btn header-auth-btn-secondary">Sign up</a>
            </div>
        <?php endif; ?>
    </div>
</header>
<div class="awning"></div>

<main class="auth-main auth-main-register">
    <section class="auth-layout">
        <div class="auth-left">
            <img src="images/tagline.png" alt="Live healthy, Go Fruti" class="tagline-image">
        </div>

        <div class="auth-right">
            <div class="auth-card auth-card-login">
            <h2 class="auth-title auth-title-register">Login</h2>

            <?php if ($registered): ?>
                <p class="auth-success">Account created! Please log in.</p>
            <?php endif; ?>

            <?php if ($errors !== []): ?>
                <ul class="auth-error">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form method="post" class="auth-form">
                <label class="auth-field auth-field-with-icon">
                    <span>Username or email address *</span>
                    <span class="auth-input-wrap">
                        <span class="auth-icon">&#128100;</span>
                        <input type="email" name="email" class="auth-input"
                               value="<?= htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </span>
                </label>

                <label class="auth-field auth-field-with-icon">
                    <span>Password *</span>
                    <span class="auth-input-wrap">
                        <span class="auth-icon">&#128274;</span>
                        <input type="password" name="password" id="login-password" class="auth-input" required>
                        <button type="button" class="auth-password-toggle" aria-label="Toggle password visibility"
                                onclick="var p=document.getElementById('login-password');p.type=p.type==='password'?'text':'password';this.textContent=p.type==='password'?'&#128065;':'&#128064;';">
                            &#128065;
                        </button>
                    </span>
                </label>

                <label class="auth-remember auth-remember-login">
                    <input type="checkbox" name="remember">
                    <span>Remember me</span>
                </label>

                <button type="submit" class="auth-btn auth-btn-login">Log In</button>
            </form>

            <p class="auth-switch auth-switch-register">
                <a href="#" class="auth-forgot">Lost your password?</a>
            </p>
            <p class="auth-switch auth-switch-register">
                Don’t have an account?
                <a href="register.php">Create one</a>
            </p>
            </div>
        </div>
    </section>
</main>
</body>
</html>

