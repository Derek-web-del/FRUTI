<?php
declare(strict_types=1);

require __DIR__ . '/auth.php';

/** @var array<string,mixed>|null $currentUser */
$currentUser = $_SESSION['user'] ?? null;

$categoryOptions = ['All', 'Citrus', 'Berries', 'Tropical', 'Melons'];
$fruitItems = [
    ['name' => 'Mango', 'category' => 'Tropical', 'price' => 136, 'stock' => 64, 'rating' => 4.8],
    ['name' => 'Avocado', 'category' => 'Tropical', 'price' => 250, 'stock' => 40, 'rating' => 4.6],
    ['name' => 'Grapes', 'category' => 'Berries', 'price' => 300, 'stock' => 58, 'rating' => 4.5],
    ['name' => 'Strawberry', 'category' => 'Berries', 'price' => 370, 'stock' => 31, 'rating' => 4.9],
    ['name' => 'Orange', 'category' => 'Citrus', 'price' => 50, 'stock' => 76, 'rating' => 4.4],
    ['name' => 'Pineapple', 'category' => 'Tropical', 'price' => 275, 'stock' => 25, 'rating' => 4.7],
    ['name' => 'Watermelon', 'category' => 'Melons', 'price' => 100, 'stock' => 18, 'rating' => 4.3],
    ['name' => 'Lemon', 'category' => 'Citrus', 'price' => 180, 'stock' => 92, 'rating' => 4.2],
];

$message = null;
$cartCount = fruti_cart_count();

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['action'], $_POST['item'])
    && $_POST['action'] === 'purchase'
) {
    $itemName = (string) $_POST['item'];

    foreach ($fruitItems as &$fruit) {
        if ($fruit['name'] === $itemName) {
            if ($fruit['stock'] > 0) {
                $fruit['stock']--;
                if (!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
                }
                if (!isset($_SESSION['cart'][$itemName])) {
                    $_SESSION['cart'][$itemName] = 0;
                }
                $_SESSION['cart'][$itemName]++;
                $cartCount = fruti_cart_count();
                $message = 'Added 1 ' . $fruit['name'] . ' to cart!';
            } else {
                $message = $fruit['name'] . ' is out of stock.';
            }
            break;
        }
    }
    unset($fruit);
}

$sortBy = $_GET['sort'] ?? 'price_asc';

// Selected filters from query string
$selectedCategories = isset($_GET['categories']) && is_array($_GET['categories'])
    ? array_values(array_intersect($_GET['categories'], $categoryOptions))
    : [];

$selectedFruits = isset($_GET['fruits']) && is_array($_GET['fruits'])
    ? array_map('strval', $_GET['fruits'])
    : [];

// Remove "All" from category-specific selections
$selectedCategories = array_values(array_filter(
    $selectedCategories,
    static fn(string $cat): bool => $cat !== 'All'
));

// Unique fruit names for filters
$fruitNames = array_values(array_unique(array_column($fruitItems, 'name')));

// Apply filters
$visibleItems = array_values(array_filter(
    $fruitItems,
    static function (array $item) use ($selectedCategories, $selectedFruits): bool {
        $categoryMatch = $selectedCategories === [] || in_array($item['category'], $selectedCategories, true);
        $fruitMatch    = $selectedFruits === [] || in_array($item['name'], $selectedFruits, true);
        return $categoryMatch && $fruitMatch;
    }
));

usort(
    $visibleItems,
    static function (array $a, array $b) use ($sortBy): int {
        return match ($sortBy) {
            'price_desc'  => $b['price'] <=> $a['price'],
            'name_asc'    => strcmp($a['name'], $b['name']),
            'rating_desc' => $b['rating'] <=> $a['rating'],
            default       => $a['price'] <=> $b['price'],
        };
    }
);

$totalStock = array_sum(array_column($visibleItems, 'stock'));
$avgPrice   = count($visibleItems) > 0
    ? array_sum(array_column($visibleItems, 'price')) / count($visibleItems)
    : 0.0;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fruit Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header class="topbar">
    <div class="topbar-left">
        <a href="landing.php">
            <img src="images/fruti_logo.jpg" alt="Fruti logo" class="logo">
        </a>
    </div>
    <nav class="main-nav">
        <a href="landing.php" class="nav-link">Home</a>
        <a href="index.php" class="nav-link active">Shop</a>
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

<main class="wrap">
    <?php if ($message !== null): ?>
        <div class="flash">
            <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <section class="stats">
        <article class="stat">Visible Fruits<strong><?= count($visibleItems) ?></strong></article>
        <article class="stat">Average Price<strong>₱<?= number_format($avgPrice, 2) ?></strong></article>
        <article class="stat">Total Stock<strong><?= $totalStock ?> units</strong></article>
    </section>

    <section class="board">
        <aside class="filters">
            <form method="get" class="filter-form">
                <input type="hidden" name="sort"
                       value="<?= htmlspecialchars($sortBy, ENT_QUOTES, 'UTF-8') ?>">

                <div class="filters-group">
                    <h3>Categories</h3>
                    <ul class="pill-list">
                        <?php foreach ($categoryOptions as $option):
                            if ($option === 'All') {
                                continue;
                            }
                            $checked = in_array($option, $selectedCategories, true);
                            ?>
                            <li>
                                <label class="pill-option">
                                    <input type="checkbox"
                                           name="categories[]"
                                           value="<?= htmlspecialchars($option, ENT_QUOTES, 'UTF-8') ?>"
                                           <?= $checked ? 'checked' : '' ?>>
                                    <span class="pill-square"></span>
                                    <span class="pill-label-text">
                                        <?= htmlspecialchars($option, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="filters-group">
                    <h3>Fruits</h3>
                    <ul class="pill-list">
                        <?php foreach ($fruitNames as $fruitName):
                            $checked = in_array($fruitName, $selectedFruits, true);
                            ?>
                            <li>
                                <label class="pill-option">
                                    <input type="checkbox"
                                           name="fruits[]"
                                           value="<?= htmlspecialchars($fruitName, ENT_QUOTES, 'UTF-8') ?>"
                                           <?= $checked ? 'checked' : '' ?>>
                                    <span class="pill-square"></span>
                                    <span class="pill-label-text">
                                        <?= htmlspecialchars($fruitName, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <button type="submit" class="apply-btn">Apply</button>
                </div>
            </form>
        </aside>

        <section class="catalog">
            <div class="catalog-head">
                <h2>PRODUCT CATALOG</h2>
                <form method="get" class="sort-form">
                    <?php foreach ($selectedCategories as $cat): ?>
                        <input type="hidden" name="categories[]"
                               value="<?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?>">
                    <?php endforeach; ?>
                    <?php foreach ($selectedFruits as $fruitName): ?>
                        <input type="hidden" name="fruits[]"
                               value="<?= htmlspecialchars($fruitName, ENT_QUOTES, 'UTF-8') ?>">
                    <?php endforeach; ?>
                    <label for="sort" class="sort-label">
                        SORT BY:
                        <select id="sort" name="sort" class="sort-select">
                            <option value="price_asc" <?= $sortBy === 'price_asc' ? 'selected' : '' ?>>Price ↑</option>
                            <option value="price_desc" <?= $sortBy === 'price_desc' ? 'selected' : '' ?>>Price ↓</option>
                            <option value="name_asc" <?= $sortBy === 'name_asc' ? 'selected' : '' ?>>Name A → Z</option>
                            <option value="rating_desc" <?= $sortBy === 'rating_desc' ? 'selected' : '' ?>>Top Rated</option>
                        </select>
                    </label>
                </form>
            </div>

            <div class="grid">
                <?php if (empty($visibleItems)): ?>
                    <p class="empty-state">No fruits found for this filter.</p>
                <?php else: ?>
                    <?php foreach ($visibleItems as $item): ?>
                        <article class="card">
                            <div class="card-image-placeholder">
                                <span class="x-line x-line-1"></span>
                                <span class="x-line x-line-2"></span>
                            </div>
                            <div class="card-body">
                                <strong class="card-title">
                                    <?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>
                                </strong>
                                <div class="card-meta">
                                    <span><?= htmlspecialchars($item['category'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <span>⭐ <?= number_format($item['rating'], 1) ?></span>
                                </div>
                                <div class="price-row">
                                    <span class="price-tag">₱<?= number_format($item['price'], 2) ?></span>
                                    <span class="stock-tag">Stock: <?= (int) $item['stock'] ?></span>
                                </div>
                                <form
                                    method="post"
                                    class="buy-form"
                                >
                                    <input type="hidden" name="action" value="purchase">
                                    <input type="hidden" name="item"
                                           value="<?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit"
                                            class="buy-btn"
                                            <?= $item['stock'] <= 0 ? 'disabled' : '' ?>>
                                        <?= $item['stock'] > 0 ? 'Add to cart' : 'Out of stock' ?>
                                    </button>
                                </form>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </section>
</main>
</body>
</html>

