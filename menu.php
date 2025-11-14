<?php
require_once 'config/functions.php';

// Handle add to cart
if (isset($_GET['add'])) {
    $menu_id = $_GET['add'];
    if (addToCart($menu_id)) {
        setAlert('success', 'Item berhasil ditambahkan ke keranjang!');
        header('Location: menu.php');
        exit();
    }
}

// Get category filter
 $category = isset($_GET['category']) ? $_GET['category'] : 'all';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Restoran bai</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    🍽️ Restoran bai
                </div>
                <nav>
                    <ul class="nav">
                        <li><a href="index.php">🏠 Beranda</a></li>
                        <li><a href="menu.php" class="active">📋 Menu</a></li>
                        <li>
                            <a href="cart.php">
                                🛒 Keranjang
                                <?php if (getCartCount() > 0): ?>
                                    <span class="cart-count"><?php echo getCartCount(); ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li><a href="admin/">👤 Admin</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <div class="container mt-4">
        <!-- Alert -->
        <?php $alert = getAlert(); if ($alert): ?>
        <div class="alert alert-<?php echo $alert['type']; ?>">
            <?php echo $alert['message']; ?>
        </div>
        <?php endif; ?>

        <!-- Category Filter -->
        <div style="margin-bottom: 2rem; text-align: center;">
            <a href="menu.php" class="btn <?php echo $category == 'all' ? 'btn-success' : ''; ?>">🍽️ Semua Menu</a>
            <a href="menu.php?category=Makanan" class="btn <?php echo $category == 'Makanan' ? 'btn-success' : ''; ?>">🍛️ Makanan</a>
            <a href="menu.php?category=Minuman" class="btn <?php echo $category == 'Minuman' ? 'btn-success' : ''; ?>">🥤 Minuman</a>
            <a href="menu.php?category=Dessert" class="btn <?php echo $category == 'Dessert' ? 'btn-success' : ''; ?>">🍰 Dessert</a>
        </div>

        <!-- Menu Grid -->
        <div class="menu-grid">
            <?php
            $db = getDB();
            $sql = "SELECT * FROM menu WHERE available = 1";
            if ($category != 'all') {
                $sql .= " AND category = '$category'";
            }
            $sql .= " ORDER BY category, name";
            $stmt = $db->query($sql);
            
            while ($menu = $stmt->fetch()):
            ?>
            <div class="menu-item">
                <img src="<?php echo getMenuImage($menu['id']); ?>" class="menu-item-image" alt="<?php echo htmlspecialchars($menu['name']); ?>">
                <div class="menu-item-content">
                    <h3 class="menu-item-title"><?php echo htmlspecialchars($menu['name']); ?></h3>
                    <div class="menu-item-category"><?php echo htmlspecialchars($menu['category']); ?></div>
                    <div class="menu-item-footer">
                        <div class="menu-price"><?php echo formatRupiah($menu['price']); ?></div>
                        <a href="menu.php?add=<?php echo $menu['id']; ?>" class="btn" onclick="addToCart(this)">+ Tambah</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>