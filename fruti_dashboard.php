<?php
$categoryOptions = ['All', 'Citrus', 'Berries', 'Tropical', 'Melons'];
$fruitItems = [
    ['name' => 'Mango', 'category' => 'Tropical', 'price' => 3.50, 'stock' => 64, 'rating' => 4.8],
    ['name' => 'Avocado', 'category' => 'Tropical', 'price' => 2.90, 'stock' => 40, 'rating' => 4.6],
    ['name' => 'Grapes', 'category' => 'Berries', 'price' => 4.20, 'stock' => 58, 'rating' => 4.5],
    ['name' => 'Strawberry', 'category' => 'Berries', 'price' => 5.10, 'stock' => 31, 'rating' => 4.9],
    ['name' => 'Orange', 'category' => 'Citrus', 'price' => 2.20, 'stock' => 76, 'rating' => 4.4],
    ['name' => 'Pineapple', 'category' => 'Tropical', 'price' => 3.80, 'stock' => 25, 'rating' => 4.7],
    ['name' => 'Watermelon', 'category' => 'Melons', 'price' => 6.40, 'stock' => 18, 'rating' => 4.3],
    ['name' => 'Lemon', 'category' => 'Citrus', 'price' => 1.60, 'stock' => 92, 'rating' => 4.2],
];

$selectedCategory = $_GET['category'] ?? 'All';
$sortBy = $_GET['sort'] ?? 'price_asc';

if (!in_array($selectedCategory, $categoryOptions, true)) {
    $selectedCategory = 'All';
}

$visibleItems = array_values(array_filter($fruitItems, static function ($item) use ($selectedCategory) {
    return $selectedCategory === 'All' || $item['category'] === $selectedCategory;
}));

usort($visibleItems, static function ($a, $b) use ($sortBy) {
    return match ($sortBy) {
        'price_desc' => $b['price'] <=> $a['price'],
        'name_asc' => strcmp($a['name'], $b['name']),
        'rating_desc' => $b['rating'] <=> $a['rating'],
        default => $a['price'] <=> $b['price'],
    };
});

$totalStock = array_sum(array_column($visibleItems, 'stock'));
$avgPrice = count($visibleItems) > 0 ? array_sum(array_column($visibleItems, 'price')) / count($visibleItems) : 0;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fruit Dashboard</title>
    <style>
        :root {
            --cyan: #1fc2de;
            --cyan-dark: #0e96b1;
            --red: #ef3f4f;
            --ink: #073b4c;
            --cream: #f8f4e3;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Trebuchet MS", "Segoe UI", sans-serif;
            background: linear-gradient(180deg, #f9f7ef 0%, #f0ece0 100%);
            color: var(--ink);
        }
        .topbar {
            background: white;
            border-bottom: 4px solid #ffd166;
            padding: 14px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .brand {
            font-weight: 800;
            font-size: 1.7rem;
            letter-spacing: 1px;
            color: var(--cyan-dark);
        }
        .brand span { color: #ff8c42; }
        .meta {
            display: flex;
            gap: 22px;
            font-weight: 700;
            color: #555;
        }
        .awning {
            height: 52px;
            background: repeating-radial-gradient(circle at 22px -4px, var(--red) 0 20px, white 20px 40px);
            border-bottom: 4px solid #09abc8;
        }
        .wrap {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 18px 24px;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }
        .stat {
            background: #fff;
            border-radius: 14px;
            border: 2px solid #d9eef3;
            padding: 12px 16px;
            box-shadow: 0 6px 18px rgba(16, 86, 104, 0.08);
        }
        .stat strong { font-size: 1.6rem; display: block; margin-top: 3px; }
        .board {
            background: var(--cyan);
            border-radius: 16px;
            padding: 16px;
            display: grid;
            grid-template-columns: 240px 1fr;
            gap: 16px;
            box-shadow: 0 14px 28px rgba(14, 70, 85, .25);
        }
        .filters {
            background: rgba(255,255,255,0.17);
            border-radius: 12px;
            padding: 12px;
            color: #083542;
        }
        .filters h3 {
            margin: 0 0 10px;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        label { font-weight: 700; display: block; margin-top: 10px; margin-bottom: 5px; }
        select {
            width: 100%;
            border: none;
            border-radius: 8px;
            padding: 10px;
            font-size: .95rem;
            color: #114B5F;
        }
        button {
            width: 100%;
            margin-top: 14px;
            padding: 10px;
            border-radius: 8px;
            border: none;
            font-weight: 700;
            font-size: .95rem;
            color: white;
            cursor: pointer;
            background: linear-gradient(130deg, #ff8c42, #ef476f);
        }
        .catalog { background: rgba(255,255,255,0.12); border-radius: 12px; padding: 12px; }
        .catalog-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid rgba(255,255,255,0.5);
            padding-bottom: 10px;
            margin-bottom: 12px;
            color: #032d38;
        }
        .catalog-head h2 { margin: 0; font-size: 2rem; }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
            gap: 12px;
        }
        .card {
            background: #3fd4eb;
            border: 3px solid #96efff;
            border-radius: 10px;
            padding: 12px;
            text-align: center;
        }
        .thumb {
            font-size: 2.1rem;
            background: rgba(255,255,255,0.35);
            border-radius: 10px;
            padding: 14px 0;
            margin-bottom: 8px;
        }
        .price {
            margin-top: 8px;
            background: rgba(8, 70, 86, .35);
            border-radius: 999px;
            color: white;
            font-weight: 700;
            padding: 5px 8px;
        }
        @media (max-width: 900px) {
            .board { grid-template-columns: 1fr; }
            .stats { grid-template-columns: 1fr; }
            .catalog-head h2 { font-size: 1.5rem; }
        }
    </style>
</head>
<body>
<header class="topbar">
    <div class="brand"><span>FRU</span>XI</div>
    <div class="meta">
        <div>Fresh Picks</div>
        <div>Best Sellers</div>
        <div>Contact</div>
    </div>
</header>
<div class="awning"></div>

<main class="wrap">
    <section class="stats">
        <article class="stat">Visible Fruits<strong><?= count($visibleItems) ?></strong></article>
        <article class="stat">Average Price<strong>$<?= number_format($avgPrice, 2) ?></strong></article>
        <article class="stat">Total Stock<strong><?= $totalStock ?> units</strong></article>
    </section>

    <section class="board">
        <aside class="filters">
            <h3>Categories</h3>
            <form method="get">
                <label for="category">Category</label>
                <select id="category" name="category">
                    <?php foreach ($categoryOptions as $option): ?>
                        <option value="<?= htmlspecialchars($option) ?>" <?= $selectedCategory === $option ? 'selected' : '' ?>>
                            <?= htmlspecialchars($option) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="sort">Sort by</label>
                <select id="sort" name="sort">
                    <option value="price_asc" <?= $sortBy === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price_desc" <?= $sortBy === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                    <option value="name_asc" <?= $sortBy === 'name_asc' ? 'selected' : '' ?>>Name: A → Z</option>
                    <option value="rating_desc" <?= $sortBy === 'rating_desc' ? 'selected' : '' ?>>Top Rated</option>
                </select>
                <button type="submit">Apply Filters</button>
            </form>
        </aside>

        <section class="catalog">
            <div class="catalog-head">
                <h2>Product Catalog</h2>
                <strong>Sort: <?= htmlspecialchars(str_replace('_', ' ', $sortBy)) ?></strong>
            </div>

            <div class="grid">
                <?php foreach ($visibleItems as $item): ?>
                    <article class="card">
                        <div class="thumb">🍉</div>
                        <strong><?= htmlspecialchars($item['name']) ?></strong>
                        <div><?= htmlspecialchars($item['category']) ?></div>
                        <div>Rating: <?= number_format($item['rating'], 1) ?>/5</div>
                        <div class="price">$<?= number_format($item['price'], 2) ?></div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </section>
</main>
</body>
</html>
