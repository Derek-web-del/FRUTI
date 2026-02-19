<?php
declare(strict_types=1);

require __DIR__ . '/auth.php';

$errors = [];
$cartCount = fruti_cart_count();

if (isset($_SESSION['user'])) {
    header('Location: landing.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName  = trim($_POST['last_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = (string) ($_POST['password'] ?? '');

    if ($firstName === '') {
        $errors[] = 'First name is required.';
    }
    if ($lastName === '') {
        $errors[] = 'Surname is required.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email address is required.';
    }
    if ($password === '' || strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if (fruti_find_user_by_email($email) !== null) {
        $errors[] = 'An account with this email already exists.';
    }

    if ($errors === []) {
        $users   = fruti_load_users();
        $users[] = [
            'first_name'    => $firstName,
            'last_name'     => $lastName,
            'email'         => strtolower($email),
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ];
        fruti_save_users($users);

        header('Location: login.php?registered=1');
        exit;
    }
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fruti Registration</title>
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
            <div class="auth-card auth-card-register">
                <h2 class="auth-title auth-title-register">Registration</h2>

                <?php if ($errors !== []): ?>
                    <ul class="auth-error">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <form method="post" class="auth-form">
                    <div class="auth-row auth-row-two">
                        <label class="auth-field">
                            <span>First name</span>
                            <input type="text" name="first_name" class="auth-input"
                                   value="<?= htmlspecialchars($firstName ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                        </label>
                        <label class="auth-field">
                            <span>Surname</span>
                            <input type="text" name="last_name" class="auth-input"
                                   value="<?= htmlspecialchars($lastName ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                        </label>
                    </div>

                    <label class="auth-field auth-field-inline">
                        <span>Date of birth</span>
                        <div class="auth-dob-row">
                            <select name="dob_day" class="auth-input auth-input-select">
                                <?php for ($d = 1; $d <= 31; $d++): ?>
                                    <option value="<?= $d ?>"><?= $d ?></option>
                                <?php endfor; ?>
                            </select>
                            <select name="dob_month" class="auth-input auth-input-select">
                                <?php
                                $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                                foreach ($months as $m): ?>
                                    <option value="<?= $m ?>"><?= $m ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select name="dob_year" class="auth-input auth-input-select">
                                <?php for ($y = (int) date('Y'); $y >= 1950; $y--): ?>
                                    <option value="<?= $y ?>"><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </label>

                    <label class="auth-field">
                        <span>Gender</span>
                        <div class="auth-gender-row">
                            <label class="auth-radio">
                                <input type="radio" name="gender" value="female" checked>
                                <span>Female</span>
                            </label>
                            <label class="auth-radio">
                                <input type="radio" name="gender" value="male">
                                <span>Male</span>
                            </label>
                            <label class="auth-radio">
                                <input type="radio" name="gender" value="custom">
                                <span>Custom</span>
                            </label>
                        </div>
                    </label>

                    <label class="auth-field">
                        <span>Mobile number or email address</span>
                        <input type="email" name="email" class="auth-input"
                               value="<?= htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </label>

                    <label class="auth-field">
                        <span>New Password</span>
                        <input type="password" name="password" class="auth-input" required>
                    </label>

                    <p class="auth-fine-print">
                        By clicking Sign up, you agree to our Terms, Privacy Policy and Cookies
                        Policy. You may receive SMS notifications from us and can opt out at any time.
                    </p>

                    <button type="submit" class="auth-btn auth-btn-register">Sign up</button>
                </form>

                <p class="auth-switch auth-switch-register">
                    Already have an account?
                    <a href="login.php">Log in</a>
                </p>
            </div>
        </div>
    </section>
</main>
</body>
</html>

