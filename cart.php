<?php
require_once 'config/functions.php';

// Handle cart actions
if (isset($_GET['action'])) {
    $menu_id = $_GET['id'];
    
    switch ($_GET['action']) {
        case 'update':
            $quantity = $_GET['quantity'];
            updateCart($menu_id, $quantity);
            break;
        case 'remove':
            removeFromCart($menu_id);
            setAlert('success', 'Item berhasil dihapus dari keranjang!');
            break;
    }
    
    header('Location: cart.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang - Restoran bai</title>
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
                        <li><a href="menu.php">📋 Menu</a></li>
                        <li>
                            <a href="cart.php" class="active">
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
        <h2 style="margin-bottom: 2rem;">🛒 Keranjang Belanja</h2>
        
        <?php $cart = getCart(); ?>
        
        <?php if (empty($cart)): ?>
        <div class="card">
            <div class="card-body text-center" style="padding: 3rem;">
                <div style="font-size: 4rem; margin-bottom: 1rem;">🛒</div>
                <h3>Keranjang Anda Kosong</h3>
                <p style="color: #666; margin: 1rem 0;">Silakan pilih menu terlebih dahulu</p>
                <a href="menu.php" class="btn btn-success">Lihat Menu</a>
            </div>
        </div>
        <?php else: ?>
        <div class="row">
            <div class="col-8">
                <div class="card">
                    <div class="card-body">
                        <table class="cart-table">
                            <thead>
                                <tr>
                                    <th>Menu</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart as $menu_id => $item): ?>
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center;">
                                            <img src="<?php echo getMenuImage($menu_id); ?>" class="cart-item-image" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                            <div style="margin-left: 1rem;">
                                                <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo formatRupiah($item['price']); ?></td>
                                    <td>
                                        <div class="quantity-control">
                                            <a href="cart.php?action=update&id=<?php echo $menu_id; ?>&quantity=<?php echo $item['quantity'] - 1; ?>" class="quantity-btn">-</a>
                                            <input type="text" class="quantity-input" value="<?php echo $item['quantity']; ?>" readonly>
                                            <a href="cart.php?action=update&id=<?php echo $menu_id; ?>&quantity=<?php echo $item['quantity'] + 1; ?>" class="quantity-btn">+</a>
                                        </div>
                                    </td>
                                    <td><?php echo formatRupiah($item['price'] * $item['quantity']); ?></td>
                                    <td>
                                        <a href="cart.php?action=remove&id=<?php echo $menu_id; ?>" class="btn btn-danger" style="padding: 0.5rem 1rem; font-size: 0.9rem;" onclick="return confirm('Hapus item ini?')">🗑️</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <h3 style="margin-bottom: 1.5rem;">📋 Ringkasan Pesanan</h3>
                        <hr>
                        <div class="form-group">
                            <label class="form-label">Nama Pemesan</label>
                            <input type="text" class="form-control" id="customer_name" placeholder="Masukkan nama Anda" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control" id="notes" rows="3" placeholder="Catatan pesanan..."></textarea>
                        </div>
                        <hr>
                        <div class="text-right">
                            <h4>Total: <span style="color: #27ae60;"><?php echo formatRupiah(getCartTotal()); ?></span></h4>
                        </div>
                        <button class="btn btn-success btn-block mt-3" onclick="placeOrder()">
                            ✅ Pesan Sekarang
                        </button>
                        <a href="menu.php" class="btn btn-block mt-2">+ Tambah Menu Lain</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php $alert = getAlert(); if ($alert): ?>
    <div class="alert alert-<?php echo $alert['type']; ?>" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
        <?php echo $alert['message']; ?>
    </div>
    <?php endif; ?>

    <script src="assets/js/script.js"></script>
</body>
</html>