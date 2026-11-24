<?php
require_once 'koneksi.php';

$kode = trim($_GET['kode'] ?? '');

try {
    $stmt = $pdo->prepare("
        SELECT p.*, k.nama AS kategori
        FROM produk p
        LEFT JOIN kategori k ON p.id_kategori = k.id
        WHERE p.kode = ?
    ");
    $stmt->execute([$kode]);
    $produk = $stmt->fetch();
} catch (PDOException $e) {
    $stmt = $pdo->prepare("SELECT * FROM produk WHERE kode = ?");
    $stmt->execute([$kode]);
    $produk = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $produk ? htmlspecialchars($produk['nama']) : 'Produk Tidak Ditemukan' ?> - UMKM Sekar Jagad Solo</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="kepala">
  <h1>UMKM Sekar Jagad Solo</h1>
  <nav class="menu">
    <a href="index.php" class="aktif">Katalog</a>
    <a href="pesan.php">Pesan</a>
    <a href="daftar_pesanan.php">Daftar Pesanan</a>
  </nav>
</header>

<main class="wadah sempit">
  <?php if (!$produk): ?>
    <div class="kosong">
      <p>Produk tidak ditemukan.</p>
      <br>
      <a class="tombol" href="index.php">Kembali ke Katalog</a>
    </div>
  <?php else: ?>
    <?php
      $stok = (int) $produk['stok'];
      if ($stok <= 0) {
          $kelas_badge = 'habis';
          $teks_badge  = 'Habis';
      } elseif ($stok <= 5) {
          $kelas_badge = 'menipis';
          $teks_badge  = 'Menipis';
      } else {
          $kelas_badge = 'tersedia';
          $teks_badge  = 'Tersedia';
      }
    ?>
    <article class="kartu detail">
      <img class="foto-detail"
           src="img/<?= htmlspecialchars($produk['kode']) ?>.jpg"
           alt="<?= htmlspecialchars($produk['nama']) ?>"
           onerror="this.style.display='none'">

      <span class="badge <?= $kelas_badge ?>"><?= $teks_badge ?></span>
      <h2><?= htmlspecialchars($produk['nama']) ?></h2>
      <p class="kategori"><?= htmlspecialchars($produk['kategori'] ?? 'Umum') ?> &middot; <?= htmlspecialchars($produk['kode']) ?></p>

      <?php if (!empty($produk['deskripsi'])): ?>
        <p><?= nl2br(htmlspecialchars($produk['deskripsi'])) ?></p>
      <?php endif; ?>

      <p><strong>Stok tersedia:</strong> <?= $stok ?> unit</p>
      <p class="harga">Rp<?= number_format($produk['harga'], 0, ',', '.') ?></p>

      <div>
        <?php if ($stok > 0): ?>
          <a class="tombol" href="pesan.php?produk=<?= urlencode($produk['kode']) ?>">Pesan Sekarang</a>
        <?php endif; ?>
        <a href="index.php">&larr; Kembali ke Katalog</a>
      </div>
    </article>
  <?php endif; ?>
</main>

<footer class="kaki">
  &copy; 2026 Pemrograman Web · Informatika TSU
</footer>

</body>
</html>