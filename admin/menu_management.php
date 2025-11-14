<?php
require_once '../config/functions.php';

if (!isLoggedIn()) {
    header('Location: index.php');
    exit();
}

// Handle add/edit menu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $available = isset($_POST['available']) ? 1 : 0;
    
    // Handle image upload
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        try {
            $image = uploadImage($_FILES['image']);
        } catch (Exception $e) {
            setAlert('danger', $e->getMessage());
            header('Location: menu_management.php');
            exit();
        }
    } elseif ($id) {
        $stmt = getDB()->prepare("SELECT image FROM menu WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        $image = $result['image'];
    }
    
    $db = getDB();
    
    if ($id) {
        $sql = "UPDATE menu SET name = ?, price = ?, category = ?, available = ?";
        $params = [$name, $price, $category, $available];
        
        if ($image) {
            $sql .= ", image = ?";
            $params[] = $image;
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $id;
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        
        setAlert('success', 'Menu berhasil diperbarui!');
    } else {
        $stmt = $db->prepare("INSERT INTO menu (name, price, category, available, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $price, $category, $available, $image]);
        
        setAlert('success', 'Menu berhasil ditambahkan!');
    }
    
    header('Location: menu_management.php');
    exit();
}

// Handle delete menu
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $db = getDB();
    
    // Get image filename first
    $stmt = $db->prepare("SELECT image FROM menu WHERE id = ?");
    $stmt->execute([$id]);
    $menu = $stmt->fetch();
    
    // Delete image file
    if ($menu['image'] && file_exists(UPLOAD_PATH . $menu['image'])) {
        unlink(UPLOAD_PATH . $menu['image']);
    }
    
    // Delete from database
    $stmt = $db->prepare("DELETE FROM menu WHERE id = ?");
    $stmt->execute([$id]);
    
    setAlert('success', 'Menu berhasil dihapus!');
    header('Location: menu_management.php');
    exit();
}

// Get menu data for edit
 $edit_menu = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM menu WHERE id = ?");
    $stmt->execute([$id]);
    $edit_menu = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Menu - Restoran bai</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    📋 Kelola Menu
                </div>
                <nav>
                    <ul class="nav">
                        <li><a href="dashboard.php">🏠 Dashboard</a></li>
                        <li><a href="kitchen.php">🔥 Kitchen Display</a></li>
                        <li><a href="menu_management.php" class="active">📋 Kelola Menu</a></li>
                        <li><a href="logout.php">👋 Logout (<?php echo htmlspecialchars($_SESSION['admin_name']); ?>)</a></li>
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

        <!-- Add/Edit Menu Form -->
        <div class="card">
            <div class="card-header">
                <h3 style="margin: 0;"><?php echo $edit_menu ? 'Edit Menu' : 'Tambah Menu Baru'; ?></h3>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <?php if ($edit_menu): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_menu['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label class="form-label">Nama Menu</label>
                        <input type="text" class="form-control" name="name" required 
                               value="<?php echo $edit_menu ? htmlspecialchars($edit_menu['name']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Harga</label>
                        <input type="number" class="form-control" name="price" required 
                               value="<?php echo $edit_menu ? $edit_menu['price'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <select class="form-control" name="category" required>
                            <option value="Makanan" <?php echo ($edit_menu && $edit_menu['category'] == 'Makanan') ? 'selected' : ''; ?>>Makanan</option>
                            <option value="Minuman" <?php echo ($edit_menu && $edit_menu['category'] == 'Minuman') ? 'selected' : ''; ?>>Minuman</option>
                            <option value="Dessert" <?php echo ($edit_menu && $edit_menu['category'] == 'Dessert') ? 'selected' : ''; ?>>Dessert</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Gambar</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                        <?php if ($edit_menu && $edit_menu['image']): ?>
                        <div style="margin-top: 1rem;">
                            <img src="<?php echo UPLOAD_URL . $edit_menu['image']; ?>" 
                                 style="max-width: 200px; max-height: 200px; border-radius: 8px;" 
                                 alt="Current image">
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="available" value="1" 
                                   <?php echo (!$edit_menu || $edit_menu['available']) ? 'checked' : ''; ?>>
                            Tersedia
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-success">
                        <?php echo $edit_menu ? 'Update Menu' : 'Tambah Menu'; ?>
                    </button>
                    <?php if ($edit_menu): ?>
                    <a href="menu_management.php" class="btn" style="margin-left: 1rem;">Batal</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Menu List -->
        <div class="card mt-4">
            <div class="card-header">
                <h3 style="margin: 0;">Daftar Menu</h3>
            </div>
            <div class="card-body">
                <?php
                $db = getDB();
                $stmt = $db->query("SELECT * FROM menu ORDER BY category, name");
                while ($menu = $stmt->fetch()):
                ?>
                <div style="display: flex; align-items: center; padding: 1rem; border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 1rem;">
                    <img src="<?php echo $menu['image'] ? UPLOAD_URL . $menu['image'] : 'https://via.placeholder.com/80x80'; ?>" 
                         style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-right: 1rem;" 
                         alt="<?php echo htmlspecialchars($menu['name']); ?>">
                    <div style="flex: 1;">
                        <h4 style="margin: 0 0 0.5rem 0;"><?php echo htmlspecialchars($menu['name']); ?></h4>
                        <p style="margin: 0; color: #666;"><?php echo formatRupiah($menu['price']); ?> • <?php echo htmlspecialchars($menu['category']); ?></p>
                        <span class="status status-<?php echo $menu['available'] ? 'completed' : 'pending'; ?>">
                            <?php echo $menu['available'] ? 'Tersedia' : 'Tidak Tersedia'; ?>
                        </span>
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="menu_management.php?edit=<?php echo $menu['id']; ?>" class="btn">Edit</a>
                        <a href="menu_management.php?delete=<?php echo $menu['id']; ?>" 
                           class="btn btn-danger" onclick="return confirm('Hapus menu ini?')">Hapus</a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>