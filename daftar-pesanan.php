<?php
require_once 'koneksi.php';

// Ambil data produk untuk mencocokkan nama & harga
$produkMap = [];
foreach ($pdo->query("SELECT * FROM produk") as $pr) {
    if (!empty($pr['kode'])) $produkMap[$pr['kode']] = $pr;
    if (!empty($pr['id']))   $produkMap[$pr['id']]   = $pr;
}

// Ambil seluruh riwayat pesanan
$pesanan = $pdo->query("SELECT * FROM pesanan ORDER BY 1 DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Pesanan - UMKM Sekar Jagad Solo</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .kartu-tabel {
      background: #ffffff;
      border: 1px solid #e6e1d6;
      border-radius: 14px;
      padding: 24px;
      overflow-x: auto;
      margin-top: 16px;
    }
    .tabel-pesanan {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.95rem;
    }
    .tabel-pesanan th,
    .tabel-pesanan td {
      padding: 12px 14px;
      text-align: left;
      border-bottom: 1px solid #ece7dc;
    }
    .tabel-pesanan th {
      background-color: #f7f4ed;
      color: #2b3a2c;
      font-weight: 600;
    }
    .tabel-pesanan tr:last-child td {
      border-bottom: none;
    }
  </style>
</head>
<body>

<header class="kepala">
  <h1>UMKM Sekar Jagad Solo</h1>
  <nav class="menu">
    <a href="index.php">Katalog</a>
    <a href="pesan.php">Pesan</a>
    <a href="daftar-pesanan.php" class="aktif">Daftar Pesanan</a>
  </nav>
</header>

<main class="wadah">
  <h2>Daftar Pesanan Masuk</h2>

  <?php if (empty($pesanan)): ?>
    <div class="kosong">
      <p>Belum ada pesanan yang masuk.</p>
      <br>
      <a class="tombol" href="pesan.php">Buat Pesanan</a>
    </div>
  <?php else: ?>
    <div class="kartu-tabel">
      <table class="tabel-pesanan">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama Pemesan</th>
            <th>No. WhatsApp</th>
            <th>Produk</th>
            <th>Jumlah</th>
            <th>Total Harga</th>
            <th>Catatan</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pesanan as $i => $row): ?>
            <?php
              $namaPemesan = $row['nama_pemesan'] ?? $row['nama'] ?? '-';
              $noWa        = $row['no_wa'] ?? $row['whatsapp'] ?? $row['telepon'] ?? $row['no_hp'] ?? '-';
              $jumlah      = (int) ($row['jumlah'] ?? $row['qty'] ?? 1);
              $catatan     = trim($row['catatan'] ?? '');

              $kunciProduk = $row['kode_produk'] ?? $row['produk_kode'] ?? $row['id_produk'] ?? $row['produk_id'] ?? '';
              $infoProduk  = $produkMap[$kunciProduk] ?? null;

              $namaProduk  = $row['nama_produk'] ?? ($infoProduk['nama'] ?? $kunciProduk ?: '-');
              $hargaSatuan = (int) ($infoProduk['harga'] ?? 0);
              $totalHarga  = (int) ($row['total_harga'] ?? $row['total'] ?? ($hargaSatuan * $jumlah));
            ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td><strong><?= htmlspecialchars($namaPemesan) ?></strong></td>
              <td><?= htmlspecialchars($noWa) ?></td>
              <td><?= htmlspecialchars($namaProduk) ?></td>
              <td><?= $jumlah ?> unit</td>
              <td><?= $totalHarga > 0 ? 'Rp' . number_format($totalHarga, 0, ',', '.') : '-' ?></td>
              <td><?= $catatan !== '' ? htmlspecialchars($catatan) : '-' ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</main>

<footer class="kaki">
  &copy; 2026 Pemrograman Web · Informatika TSU
</footer>

</body>
</html>