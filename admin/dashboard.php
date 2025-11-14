<?php
require_once '../config/functions.php';

if (!isLoggedIn()) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Restoran bai</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    📊 Dashboard Admin
                </div>
                <nav>
                    <ul class="nav">
                        <li><a href="dashboard.php" class="active">🏠 Dashboard</a></li>
                        <li><a href="kitchen.php">🔥 Kitchen Display</a></li>
                        <li><a href="menu_management.php">📋 Kelola Menu</a></li>
                        <li><a href="logout.php">👋 Logout (<?php echo htmlspecialchars($_SESSION['admin_name']); ?>)</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <div class="container mt-4">
        <!-- Stats -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
            <div class="card">
                <div class="card-body text-center">
                    <h3 style="font-size: 2.5rem; margin-bottom: 0.5rem; color: #3498db;">
                        <?php
                        $db = getDB();
                        $stmt = $db->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()");
                        echo $stmt->fetchColumn();
                        ?>
                    </h3>
                    <p style="color: #666;">Pesanan Hari Ini</p>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body text-center">
                    <h3 style="font-size: 2.5rem; margin-bottom: 0.5rem; color: #f39c12;">
                        <?php
                        $stmt = $db->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
                        echo $stmt->fetchColumn();
                        ?>
                    </h3>
                    <p style="color: #666;">Menunggu</p>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body text-center">
                    <h3 style="font-size: 2.5rem; margin-bottom: 0.5rem; color: #e74c3c;">
                        <?php
                        $stmt = $db->query("SELECT COUNT(*) FROM orders WHERE status = 'preparing'");
                        echo $stmt->fetchColumn();
                        ?>
                    </h3>
                    <p style="color: #666;">Diproses</p>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body text-center">
                    <h3 style="font-size: 2.5rem; margin-bottom: 0.5rem; color: #27ae60;">
                        <?php
                        $stmt = $db->query("SELECT COUNT(*) FROM orders WHERE status = 'completed'");
                        echo $stmt->fetchColumn();
                        ?>
                    </h3>
                    <p style="color: #666;">Selesai</p>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="card">
            <div class="card-header">
                <h3 style="margin: 0;">📋 Pesanan Terbaru</h3>
            </div>
            <div class="card-body">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>No. Pesanan</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $db->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 10");
                        while ($order = $stmt->fetch()):
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                            <td><?php echo formatRupiah($order['total_amount']); ?></td>
                            <td>
                                <span class="status status-<?php echo $order['status']; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('H:i', strtotime($order['created_at'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>