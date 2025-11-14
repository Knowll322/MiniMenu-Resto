<?php
require_once '../config/functions.php';

// Simpan nama admin untuk ditampilkan
 $admin_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin';

// Hapus semua session
session_destroy();

// Tampilkan halaman logout sukses
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout Berhasil - Restoran bai</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .logout-container {
            max-width: 500px;
            margin: 0 auto;
            text-align: center;
            padding: 0 20px;
        }
        
        .logout-card {
            background: white;
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        
        .logout-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .countdown {
            font-size: 1.1rem;
            color: #666;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <div class="logout-card">
            <div class="logout-icon">👋</div>
            <h2>Logout Berhasil!</h2>
            <p style="color: #666; margin: 1rem 0;">
                Terima kasih <?php echo htmlspecialchars($admin_name); ?>,<br>
                Anda telah berhasil logout dari sistem.
            </p>
            
            <div style="margin: 2rem 0;">
                <a href="../index.php" class="btn btn-success">
                    🏠 Kembali ke Beranda
                </a>
                <a href="index.php" class="btn" style="margin-left: 1rem;">
                    🔐 Login Kembali
                </a>
            </div>
            
            <div class="countdown">
                Anda akan diarahkan ke halaman beranda dalam <span id="countdown">5</span> detik...
            </div>
        </div>
    </div>

    <script>
    // Countdown otomatis redirect
    let countdown = 5;
    const countdownElement = document.getElementById('countdown');
    
    const timer = setInterval(() => {
        countdown--;
        countdownElement.textContent = countdown;
        
        if (countdown <= 0) {
            clearInterval(timer);
            window.location.href = '../index.php';
        }
    }, 1000);
    
    // Jika user ingin langsung pindah
    document.addEventListener('DOMContentLoaded', function() {
        const links = document.querySelectorAll('a');
        links.forEach(link => {
            link.addEventListener('click', function() {
                clearInterval(timer);
            });
        });
    });
    </script>
</body>
</html>