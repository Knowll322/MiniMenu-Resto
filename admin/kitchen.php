<?php
require_once '../config/functions.php';

if (!isLoggedIn()) {
    header('Location: index.php');
    exit();
}

// Update item status
if (isset($_POST['update_status'])) {
    $item_id = $_POST['item_id'];
    $status = $_POST['status'];
    
    updateOrderItemStatus($item_id, $status);
    
    // Check if all items in order are ready
    $db = getDB();
    $stmt = $db->prepare("SELECT order_id FROM order_items WHERE id = ?");
    $stmt->execute([$item_id]);
    $order_id = $stmt->fetchColumn();
    
    $stmt = $db->prepare("SELECT COUNT(*) FROM order_items WHERE order_id = ? AND status != 'ready'");
    $stmt->execute([$order_id]);
    $pending_count = $stmt->fetchColumn();
    
    if ($pending_count == 0) {
        updateOrderStatus($order_id, 'ready');
    }
    
    header('Location: kitchen.php');
    exit();
}

// Complete order
if (isset($_POST['complete_order'])) {
    $order_id = $_POST['order_id'];
    
    updateOrderStatus($order_id, 'completed');
    
    // Update all items to ready
    $db = getDB();
    $stmt = $db->prepare("UPDATE order_items SET status = 'ready' WHERE order_id = ?");
    $stmt->execute([$order_id]);
    
    setAlert('success', 'Pesanan telah ditandai sebagai selesai!');
    header('Location: kitchen.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Display - Restoran bai</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    🔥 Kitchen Display
                </div>
                <nav>
                    <ul class="nav">
                        <li><a href="dashboard.php">🏠 Dashboard</a></li>
                        <li><a href="kitchen.php" class="active">🔥 Kitchen Display</a></li>
                        <li><a href="menu_management.php">📋 Kelola Menu</a></li>
                        <li><a href="logout.php">👋 Logout (<?php echo htmlspecialchars($_SESSION['admin_name']); ?>)</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <div class="kitchen-header">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 class="kitchen-title">🔥 Kitchen Display System</h1>
                    <p class="kitchen-subtitle">Monitor pesanan dapur secara real-time</p>
                </div>
                <div class="text-right">
                    <div id="current-time" style="font-size: 1.5rem; font-weight: 600;"></div>
                    <small>Auto refresh: <span id="countdown">30</span>s</small>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Alert -->
        <?php $alert = getAlert(); if ($alert): ?>
        <div class="alert alert-<?php echo $alert['type']; ?>">
            <?php echo $alert['message']; ?>
        </div>
        <?php endif; ?>

        <!-- Filter -->
        <div style="margin-bottom: 2rem; text-align: center;">
            <a href="kitchen.php" class="btn <?php echo !isset($_GET['status']) || $_GET['status'] == 'all' ? 'btn-success' : ''; ?>">📋 Semua</a>
            <a href="kitchen.php?status=pending" class="btn <?php echo isset($_GET['status']) && $_GET['status'] == 'pending' ? 'btn-success' : ''; ?>">⏳ Pending</a>
            <a href="kitchen.php?status=preparing" class="btn <?php echo isset($_GET['status']) && $_GET['status'] == 'preparing' ? 'btn-success' : ''; ?>">🔥 Preparing</a>
            <a href="kitchen.php?status=ready" class="btn <?php echo isset($_GET['status']) && $_GET['status'] == 'ready' ? 'btn-success' : ''; ?>">✅ Ready</a>
            <a href="kitchen.php?status=completed" class="btn <?php echo isset($_GET['status']) && $_GET['status'] == 'completed' ? 'btn-success' : ''; ?>">✓ Selesai</a>
        </div>

        <!-- Orders -->
        <?php
        $db = getDB();
        $status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
        $sql = "SELECT o.*, oi.id as item_id, oi.quantity, oi.status as item_status, m.name as menu_name 
                FROM orders o 
                JOIN order_items oi ON o.id = oi.order_id 
                JOIN menu m ON oi.menu_id = m.id 
                WHERE o.status IN ('pending', 'preparing', 'ready', 'completed')";
        
        if ($status_filter != 'all') {
            $sql .= " AND o.status = '$status_filter'";
        }
        
        $sql .= " ORDER BY 
                    CASE o.status 
                        WHEN 'pending' THEN 1 
                        WHEN 'preparing' THEN 2 
                        WHEN 'ready' THEN 3 
                        WHEN 'completed' THEN 4
                    END,
                    o.created_at ASC";
        
        $stmt = $db->query($sql);
        $current_order = null;
        
        while ($row = $stmt->fetch()):
            if ($current_order != $row['id']):
                if ($current_order !== null) echo '</div></div>';
                $current_order = $row['id'];
        ?>
        <div class="kitchen-item <?php echo $row['status']; ?> <?php echo $row['status'] == 'completed' ? 'completed' : ''; ?>">
            <?php if ($row['status'] == 'completed'): ?>
            <div class="completed-badge">✓ SELESAI</div>
            <?php endif; ?>
            <div class="kitchen-item-header">
                <div>
                    <div class="kitchen-order-number">#<?php echo htmlspecialchars($row['order_number']); ?></div>
                    <div class="kitchen-order-meta">
                        <?php echo htmlspecialchars($row['customer_name']); ?> • 
                        <?php echo date('H:i', strtotime($row['created_at'])); ?>
                    </div>
                </div>
                <span class="status status-<?php echo $row['status']; ?>">
                    <?php 
                    switch($row['status']) {
                        case 'pending': echo '⏳ Pending'; break;
                        case 'preparing': echo '🔥 Preparing'; break;
                        case 'ready': echo '✅ Ready'; break;
                        case 'completed': echo '✓ Selesai'; break;
                    }
                    ?>
                </span>
            </div>
            <div class="kitchen-items">
            <?php endif; ?>
                <div class="kitchen-item-row">
                    <div>
                        <span class="kitchen-item-name"><?php echo htmlspecialchars($row['menu_name']); ?></span>
                        <span class="kitchen-item-quantity">x<?php echo $row['quantity']; ?></span>
                    </div>
                    <?php if ($row['status'] != 'completed'): ?>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                        <div class="status-controls">
                            <button type="submit" name="update_status" value="pending" 
                                    class="status-btn pending <?php echo $row['item_status'] == 'pending' ? 'active' : ''; ?>"
                                    <?php echo $row['item_status'] == 'pending' ? 'disabled' : ''; ?>>
                                Pending
                            </button>
                            <button type="submit" name="update_status" value="preparing" 
                                    class="status-btn preparing <?php echo $row['item_status'] == 'preparing' ? 'active' : ''; ?>"
                                    <?php echo $row['item_status'] == 'preparing' ? 'disabled' : ''; ?>>
                                Preparing
                            </button>
                            <button type="submit" name="update_status" value="ready" 
                                    class="status-btn ready <?php echo $row['item_status'] == 'ready' ? 'active' : ''; ?>"
                                    <?php echo $row['item_status'] == 'ready' ? 'disabled' : ''; ?>>
                                Ready
                            </button>
                        </div>
                    </form>
                    <?php else: ?>
                    <span class="status status-completed">✓ Selesai</span>
                    <?php endif; ?>
                </div>
            <?php if ($stmt->columnCount() == 0 || $stmt->fetchColumn() == 0): ?>
            </div>
            
            <?php if ($row['status'] == 'ready'): ?>
            <div class="order-actions">
                <form method="POST" onsubmit="return confirm('Apakah Anda yakin pesanan ini sudah selesai?');">
                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="complete_order" class="complete-btn">
                        ✓ Tandai Selesai
                    </button>
                </form>
            </div>
            <?php endif; ?>
            
        </div>
        <?php 
            endif;
        endwhile;
        
        if ($current_order !== null) echo '</div></div>';
        ?>
    </div>

    <button class="btn refresh-btn" onclick="location.reload()" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;">🔄 Refresh</button>

    <script src="../assets/js/script.js"></script>
</body>
</html>