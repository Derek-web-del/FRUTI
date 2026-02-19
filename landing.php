<?php
declare(strict_types=1);
require __DIR__ . '/auth.php';

/** @var array<string,mixed>|null $currentUser */
$currentUser = $_SESSION['user'] ?? null;
$cartCount = fruti_cart_count();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fruti Storefront</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="landing-body">
<header class="topbar">
    <div class="topbar-left">
        <a href="landing.php">
            <img src="images/fruti_logo.jpg" alt="Fruti logo" class="logo">
        </a>
    </div>
    <nav class="main-nav">
        <a href="landing.php" class="nav-link active">Home</a>
        <a href="index.php" class="nav-link">Shop</a>
        <a href="#" class="nav-link">Best Sellers</a>
        <a href="#" class="nav-link">About</a>
        <a href="#" class="nav-link">Contact</a>
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

<main class="landing-main">
    <section class="hero">
        <div class="hero-overlay">
            <div class="hero-text">
                <p class="hero-kicker">Live healthy,</p>
                <h1 class="hero-title">Go Fruti</h1>
                <p class="hero-subtitle">
                    Fruti made from carefully selected, high‑quality fruits that are
                    prepared in the safest possible way.
                </p>
                <a href="index.php" class="hero-btn">Shop Now</a>
            </div>
        </div>
    </section>

    <section class="featured">
        <h2 class="section-heading">Featured Products</h2>
        <div class="featured-row">
            <article class="featured-card">
                <div class="featured-image"></div>
                <div class="featured-info">
                    <h3>Fruti Cup</h3>
                    <p class="featured-price">₱75</p>
                    <a href="index.php" class="featured-cta">Add to Cart</a>
                </div>
            </article>
            <article class="featured-card">
                <div class="featured-image"></div>
                <div class="featured-info">
                    <h3>Fruti Mix</h3>
                    <p class="featured-price">₱89</p>
                    <a href="index.php" class="featured-cta">Add to Cart</a>
                </div>
            </article>
        </div>
    </section>

    <section class="promo">
        <div class="promo-badge">SUMMER DEAL</div>
        <h2 class="section-heading">Summer Splash Promo</h2>
        <p class="promo-text">
            ☀️ Buy any 2 Fruti cups and get the <strong>3rd at 50% off</strong>!  
            Mix and match your favorite flavors and chill all summer long.
        </p>
        <div class="promo-strip">
            <span class="promo-pill">₱75 → <strong>₱35</strong></span>
            <span class="promo-pill">Auguest 1 - 31</span>
            <span class="promo-pill promo-pill-accent">Limited Time</span>
        </div>
    </section>

    <section class="search-intro">
        <h2 class="section-heading">Search Results</h2>
        <p class="search-copy">Browse all Fruti products and find your new favorite mix.</p>
        <a href="index.php" class="hero-btn hero-btn-secondary">View All Products</a>
    </section>
</main>
</body>
</html>

