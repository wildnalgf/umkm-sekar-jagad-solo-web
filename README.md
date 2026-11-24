# UMKM Sekar Jagad Solo

## Deskripsi Proyek
**UMKM Sekar Jagad Solo** adalah aplikasi web katalog produk dan sistem pemesanan daring berbasis PHP 8+, PDO, dan MySQL. Aplikasi ini dirancang untuk membantu pelaku UMKM memasarkan produk unggulan lokal—seperti kain batik tulis, kuliner tradisional, dan kopi—secara digital sekaligus mencatat pesanan serta memperbarui stok barang secara otomatis.

## Teknologi yang Digunakan
- **Backend:** PHP 8+ (PDO dengan *Prepared Statements* & transaksi database)
- **Database:** MySQL / MariaDB
- **Frontend:** HTML5 & Vanilla CSS (Grid & Flexbox)
- **Server Lokal:** Laragon / XAMPP

## Fitur Utama
- **Katalog Produk Dinamis:** Menampilkan daftar produk lengkap dengan pencarian kata kunci dan filter berdasarkan kategori.
- **Indikator Stok Otomatis:** Label status ketersediaan barang berubah otomatis sesuai sisa stok (*Tersedia*, *Menipis*, dan *Habis*).
- **Halaman Detail Produk:** Menampilkan spesifikasi, deskripsi produk, harga, serta sisa stok sebelum pemesanan.
- **Form Pemesanan Terintegrasi:** Validasi input pelanggan dan penyimpanan transaksi ke database yang otomatis mengurangi jumlah stok produk.
- **Riwayat Daftar Pesanan:** Menampilkan rekap seluruh pesanan masuk secara *real-time* dari database.

## Struktur Database (`toko_umkm`)
- `kategori` — Menyimpan data pengelompokan kategori produk.
- `produk` — Menyimpan detail produk, harga, stok, dan relasi ke tabel kategori.
- `pesanan` — Menyimpan data transaksi pemesanan pelanggan, total harga, dan catatan.

## Cara Menjalankan di Lokal
1. *Clone* atau unduh repositori ini, lalu letakkan di folder server lokal (`C:\laragon\www\` atau `htdocs`).
2. Buka **phpMyAdmin**, buat database baru bernama `toko_umkm`, lalu *import* file `toko_umkm.sql`.
3. Sesuaikan pengaturan koneksi database di file `koneksi.php` jika diperlukan.
4. Buka di browser melalui alamat `http://localhost/toko-umkm%202/`.