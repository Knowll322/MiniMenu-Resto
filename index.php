<?php
require_once 'config/functions.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restoran bai - Sistem Order Online</title>
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
                        <li><a href="index.php" class="active">🏠 Beranda</a></li>
                        <li><a href="menu.php">📋 Menu</a></li>
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

    <!-- Hero Section -->
    <section style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://picsum.photos/seed/restaurant/1200/400.jpg'); background-size: cover; background-position: center; color: white; text-align: center; padding: 5rem 0; margin-bottom: 3rem;">
        <div class="container">
            <h1 style="font-size: 3rem; margin-bottom: 1rem; font-weight: 700;">Selamat Datang di Restoran Kami</h1>
            <p style="font-size: 1.3rem; margin-bottom: 2rem; opacity: 0.9;">Nikmati hidangan lezat dengan pemesanan mudah dan cepat</p>
            <a href="menu.php" class="btn btn-success" style="font-size: 1.1rem; padding: 1rem 2rem;">Lihat Menu Kami</a>
        </div>
    </section>

    <div class="container">
        <!-- Featured Menu -->
        <div class="card">
            <div class="card-header">
                <h2 style="margin: 0;">⭐ Menu Favorit</h2>
            </div>
            <div class="card-body">
                <div class="menu-grid">
                    <?php
                    $db = getDB();
                    $stmt = $db->query("SELECT * FROM menu WHERE available = 1 LIMIT 4");
                    while ($menu = $stmt->fetch()):
                    ?>
                    <div class="menu-item">
                        <img src="<?php echo getMenuImage($menu['id']); ?>" class="menu-item-image" alt="<?php echo htmlspecialchars($menu['name']); ?>">
                        <div class="menu-item-content">
                            <h3 class="menu-item-title"><?php echo htmlspecialchars($menu['name']); ?></h3>
                            <div class="menu-item-category"><?php echo htmlspecialchars($menu['category']); ?></div>
                            <div class="menu-item-footer">
                                <div class="menu-price"><?php echo formatRupiah($menu['price']); ?></div>
                                <a href="menu.php" class="btn">Pesan Sekarang</a>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <!-- About Section -->
        <div class="card mt-4">
            <div class="card-body text-center">
                <h2 style="margin-bottom: 1rem;">Tentang Restoran Kami</h2>
                <p style="color: #666; font-size: 1.1rem; max-width: 800px; margin: 0 auto;">
                    Restoran kami menyajikan hidangan lezat dengan bahan berkualitas tinggi. 
                    Dengan sistem pemesanan online yang modern, Anda dapat dengan mudah memesan makanan favorit Anda kapan saja.
                </p>
                <div style="margin-top: 2rem; display: flex; justify-content: center; gap: 2rem; flex-wrap: wrap;">
                    <div style="text-align: center;">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">🍴</div>
                        <h4>Makanan Berkualitas</h4>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">⚡</div>
                        <h4>Pelayanan Cepat</h4>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">💳</div>
                        <h4>Pembayaran Mudah</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>