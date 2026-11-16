# MiniMenu Resto

Aplikasi pemesanan restoran berbasis PHP dan MySQL dengan keranjang belanja, tagihan, panel admin, dan kitchen display.

> **Jenis proyek:** Praktikum / Tugas Kuliah

## Ringkasan

MiniMenu Resto adalah aplikasi order restoran sederhana yang saya bangun sebagai tugas praktikum. Pengunjung dapat memilih menu, memasukkan pesanan ke keranjang, dan menerima halaman tagihan tanpa perlu membuat akun. Dapur dikelola lewat panel admin terpisah yang memantau status tiap pesanan dan mengelola daftar menu beserta fotonya. Basis data menggunakan MySQL dengan akses PDO, dan state keranjang disimpan di session PHP.

## Fitur

- Halaman beranda menampilkan menu dari database pada kartu menu unggulan (`index.php`).
- Daftar menu dengan filter kategori Makanan, Minuman, dan Dessert (`menu.php`).
- Keranjang belanja berbasis session dengan tambah item, ubah jumlah, dan hapus item (`config/functions.php`, `cart.php`).
- Endpoint JSON untuk membuat pesanan dan mengambil jumlah item dalam keranjang (`order.php`, `get_cart_count.php`).
- Halaman tagihan yang menampilkan nomor pesanan, rincian item, subtotal, dan total pembayaran (`bill.php`).
- Panel admin dengan login terpisah, ringkasan pesanan harian, dan daftar pesanan terbaru (`admin/index.php`, `admin/dashboard.php`).
- Pengelolaan menu termasuk tambah, ubah, hapus, unggah foto, dan penandaan ketersediaan item (`admin/menu_management.php`).
- Kitchen Display untuk memantau status tiap item pesanan dari pending, preparing, ready, hingga completed (`admin/kitchen.php`).

## Teknologi

![PHP](https://img.shields.io/badge/PHP-777BB4%20warna?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-4479A1%20warna?style=for-the-badge&logo=mysql)
![HTML](https://img.shields.io/badge/HTML-E34F26%20warna?style=for-the-badge&logo=html5)
![CSS](https://img.shields.io/badge/CSS-1572B6%20warna?style=for-the-badge&logo=css3)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E%20warna?style=for-the-badge&logo=javascript)
![Session](https://img.shields.io/badge/Session-4F5D95%20warna?style=for-the-badge)

## Cara Menjalankan

Aplikasi ini berjalan di lingkungan XAMPP dengan PHP dan MySQL lokal.

1. Salin/duplikasi folder ke `C:\xampp\htdocs\MiniMenu-Resto\`
2. Jalankan Apache + MySQL dari XAMPP Control Panel
3. Buka http://localhost/phpmyadmin, buat database dengan nama `restoran_rapi`, lalu buat database secara manual sesuai tabel yang dipakai di kode. Repo ini tidak menyertakan berkas `.sql` yang dapat diimport.
4. Sesuaikan kredensial di `config/database.php` (host, user, password, nama database)
5. Buka http://localhost/MiniMenu-Resto/ di browser

Tabel yang dipakai di kode: `admin`, `menu`, `orders`, `order_items`.

## Struktur Proyek

```
MiniMenu-Resto/
├── admin/
│   ├── dashboard.php
│   ├── index.php
│   ├── kitchen.php
│   ├── logout.php
│   └── menu_management.php
├── assets/
│   ├── css/style.css
│   └── js/script.js
├── config/
│   ├── database.php
│   └── functions.php
├── uploads/
│   └── menu/
├── bill.php
├── cart.php
├── get_cart_count.php
├── index.php
├── menu.php
└── order.php
```

## Catatan

- Kredensial database ditulis langsung di `config/database.php` dengan user `root` dan password kosong (default XAMPP). Untuk deployment nyata, kredensial sebaiknya dipindahkan ke environment variable atau file konfigurasi di luar repo.
- Folder `uploads/menu/` berisi foto menu contoh yang ikut ter-track di git. Bila tidak ingin dipublikasikan, hapus dari riwayat git sebelum repositori dikirim.
- Filter kategori di `menu.php` masih memakai interpolasi string langsung ke query SQL. Untuk penggunaan produksi, sebaiknya diubah ke prepared statement.
- Halaman admin berada di path terpisah (`/admin/`) dan memakai session `admin_id` yang berbeda dari session keranjang pelanggan.

## Lisensi

MIT License — lihat [LICENSE](LICENSE)
