<?php
require_once 'config/functions.php';

if (!isset($_GET['order_id'])) {
    header('Location: index.php');
    exit();
}

 $order_id = $_GET['order_id'];
 $db = getDB();
 $stmt = $db->prepare("SELECT * FROM orders WHERE id = ?");
 $stmt->execute([$order_id]);
 $order = $stmt->fetch();

if (!$order) {
    header('Location: index.php');
    exit();
}

 $stmt = $db->prepare("SELECT oi.*, m.name FROM order_items oi JOIN menu m ON oi.menu_id = m.id WHERE oi.order_id = ?");
 $stmt->execute([$order_id]);
 $items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tagihan - Restoran bai</title>
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
                        <li><a href="cart.php">🛒 Keranjang</a></li>
                        <li><a href="admin/">👤 Admin</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header text-center">
                <h2 style="margin: 0;">🧾 TAGIHAN</h2>
            </div>
            <div class="card-body">
                <div style="display: flex; justify-content: space-between; margin-bottom: 2rem;">
                    <div>
                        <h3>Restoran bai</h3>
                        <p>Jl. karangrejo<br>Telp: (021) 123-4567</p>
                    </div>
                    <div class="text-right">
                        <h4>Detail Pesanan</h4>
                        <p>No. Pesanan: <?php echo htmlspecialchars($order['order_number']); ?></p>
                        <p>Pelanggan: <?php echo htmlspecialchars($order['customer_name']); ?></p>
                        <p>Tanggal: <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                    </div>
                </div>
                
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Menu</th>
                            <th class="text-right">Harga</th>
                            <th class="text-center">Qty</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td class="text-right"><?php echo formatRupiah($item['price']); ?></td>
                            <td class="text-center"><?php echo $item['quantity']; ?></td>
                            <td class="text-right"><?php echo formatRupiah($item['price'] * $item['quantity']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-right"><strong>TOTAL</strong></td>
                            <td class="text-right"><strong><?php echo formatRupiah($order['total_amount']); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
                
                <?php if ($order['notes']): ?>
                <div class="alert alert-success">
                    <strong>Catatan:</strong> <?php echo htmlspecialchars($order['notes']); ?>
                </div>
                <?php endif; ?>
                
                <div class="text-center mt-4">
                    <div class="mb-3">
                        <span class="status status-<?php echo $order['status']; ?>">
                            Status: <?php echo ucfirst($order['status']); ?>
                        </span>
                    </div>
                    
                    <button class="btn" onclick="window.print()">🖨️ Cetak Tagihan</button>
                    <a href="index.php" class="btn btn-success" style="margin-left: 1rem;">🏠 Pesan Lagi</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>