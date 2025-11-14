<?php
require_once 'database.php';

// Start session
session_start();

// Get database connection
function getDB() {
    return Database::getInstance()->getConnection();
}

// Helper functions
function formatRupiah($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function generateOrderNumber() {
    return 'ORD-' . date('YmdHis');
}

function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

// Upload configuration
define('UPLOAD_PATH', __DIR__ . '/../uploads/menu/');
define('UPLOAD_URL', 'uploads/menu/');

// Upload function
function uploadImage($file, $old_image = null) {
    $target_dir = UPLOAD_PATH;
    
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    if (!isset($file) || $file['error'] != 0) {
        return $old_image;
    }
    
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array($imageFileType, $allowed_types)) {
        throw new Exception("Only JPG, JPEG, PNG, GIF & WEBP files are allowed.");
    }
    
    $filename = uniqid() . '.' . $imageFileType;
    $target_file = $target_dir . $filename;
    
    if ($old_image && file_exists($target_dir . $old_image)) {
        unlink($target_dir . $old_image);
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $filename;
    } else {
        throw new Exception("Error uploading file.");
    }
}

// Cart functions
function getCart() {
    return isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
}

function addToCart($menu_id, $quantity = 1) {
    $db = getDB();
    
    $stmt = $db->prepare("SELECT * FROM menu WHERE id = ? AND available = 1");
    $stmt->execute([$menu_id]);
    $menu = $stmt->fetch();
    
    if (!$menu) return false;
    
    $cart = getCart();
    
    if (isset($cart[$menu_id])) {
        $cart[$menu_id]['quantity'] += $quantity;
    } else {
        $cart[$menu_id] = [
            'name' => $menu['name'],
            'price' => $menu['price'],
            'quantity' => $quantity,
            'image' => $menu['image']
        ];
    }
    
    $_SESSION['cart'] = $cart;
    return true;
}

function updateCart($menu_id, $quantity) {
    $cart = getCart();
    
    if ($quantity <= 0) {
        unset($cart[$menu_id]);
    } elseif (isset($cart[$menu_id])) {
        $cart[$menu_id]['quantity'] = $quantity;
    }
    
    $_SESSION['cart'] = $cart;
}

function removeFromCart($menu_id) {
    $cart = getCart();
    unset($cart[$menu_id]);
    $_SESSION['cart'] = $cart;
}

function getCartTotal() {
    $cart = getCart();
    $total = 0;
    
    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    
    return $total;
}

function getCartCount() {
    $cart = getCart();
    $count = 0;
    
    foreach ($cart as $item) {
        $count += $item['quantity'];
    }
    
    return $count;
}

// Order functions
function createOrder($customer_name, $notes = '') {
    $db = getDB();
    
    try {
        $db->beginTransaction();
        
        $order_number = generateOrderNumber();
        $total = getCartTotal();
        
        $stmt = $db->prepare("INSERT INTO orders (order_number, customer_name, total_amount, notes) VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_number, $customer_name, $total, $notes]);
        
        $order_id = $db->lastInsertId();
        
        foreach (getCart() as $menu_id => $item) {
            $stmt = $db->prepare("INSERT INTO order_items (order_id, menu_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$order_id, $menu_id, $item['quantity'], $item['price']]);
        }
        
        $db->commit();
        
        $_SESSION['cart'] = [];
        
        return [
            'success' => true,
            'order_id' => $order_id,
            'order_number' => $order_number
        ];
        
    } catch (Exception $e) {
        $db->rollBack();
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

function updateOrderStatus($order_id, $status) {
    $db = getDB();
    
    $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
    return $stmt->execute([$status, $order_id]);
}

function updateOrderItemStatus($item_id, $status) {
    $db = getDB();
    
    $stmt = $db->prepare("UPDATE order_items SET status = ? WHERE id = ?");
    return $stmt->execute([$status, $item_id]);
}

function getMenuImage($menu_id) {
    $db = getDB();
    
    $stmt = $db->prepare("SELECT image FROM menu WHERE id = ?");
    $stmt->execute([$menu_id]);
    $menu = $stmt->fetch();
    
    if ($menu && $menu['image']) {
        return UPLOAD_URL . $menu['image'];
    }
    
    return 'https://picsum.photos/seed/' . $menu_id . '/300/200.jpg';
}

// Alert function
function setAlert($type, $message) {
    $_SESSION['alert'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getAlert() {
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        unset($_SESSION['alert']);
        return $alert;
    }
    return null;
}
?>