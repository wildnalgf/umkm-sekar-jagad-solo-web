<?php
require_once 'koneksi.php';

$kata_kunci = trim($_GET['q'] ?? '');
$kategori   = trim($_GET['kategori'] ?? '');

// Ambil daftar kategori dari tabel kategori
$map_kategori = [];
try {
    $stmt_kat = $pdo->query("SELECT * FROM kategori");
    foreach ($stmt_kat->fetchAll(PDO::FETCH_ASSOC) as $k) {
        $id_k   = (string) ($k['id'] ?? $k['id_kategori'] ?? '');
        $nama_k = $k['nama'] ?? $k['nama_kategori'] ?? '';
        if ($id_k !== '') {
            $map_kategori[$id_k] = $nama_k;
        }
    }
} catch (PDOException $e) {
}

// Deteksi nama kolom relasi kategori di tabel produk
$kolom_kat = 'kategori_id';
$cols = $pdo->query("SHOW COLUMNS FROM produk")->fetchAll(PDO::FETCH_COLUMN);
if (in_array('id_kategori', $cols)) {
    $kolom_kat = 'id_kategori';
} elseif (in_array('kategori', $cols)) {
    $kolom_kat = 'kategori';
}

$sql = "SELECT * FROM produk WHERE 1=1";
$params = [];

if ($kata_kunci !== '') {
    $sql .= " AND (nama LIKE ? OR kode LIKE ?)";
    $params[] = "%$kata_kunci%";
    $params[] = "%$kata_kunci%";
}

if ($kategori !== '') {
    $sql .= " AND $kolom_kat = ?";
    $params[] = $kategori;
}

$sql .= " ORDER BY nama ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$daftar_produk = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UMKM Sekar Jagad Solo</title>
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

<main class="wadah">
  <form class="cari" method="get" action="index.php">
    <input type="text" name="q" placeholder="Cari produk..." value="<?= htmlspecialchars($kata_kunci) ?>">
    <select name="kategori">
      <option value="">Semua</option>
      <?php foreach ($map_kategori as $id_k => $nama_k): ?>
        <option value="<?= htmlspecialchars($id_k) ?>" <?= $kategori === (string) $id_k ? 'selected' : '' ?>>
          <?= htmlspecialchars($nama_k) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <button type="submit">Cari</button>
  </form>

  <p class="info"><?= count($daftar_produk) ?> produk ditemukan.</p>

  <section class="grid">
    <?php if (empty($daftar_produk)): ?>
      <p class="kosong">Produk tidak ditemukan.</p>
    <?php else: ?>
      <?php foreach ($daftar_produk as $p): ?>
        <?php
          $stok = (int) $p['stok'];
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

          $val_kat = (string) ($p[$kolom_kat] ?? '');
          $nama_kategori = $map_kategori[$val_kat] ?? ($p['kategori'] ?? 'Umum');
        ?>
        <article class="kartu">
          <img class="foto-produk"
               src="img/<?= htmlspecialchars($p['kode']) ?>.jpg"
               alt="<?= htmlspecialchars($p['nama']) ?>"
               onerror="this.style.display='none'">

          <span class="badge <?= $kelas_badge ?>"><?= $teks_badge ?></span>

          <h2>
            <a href="produk.php?kode=<?= urlencode($p['kode']) ?>">
              <?= htmlspecialchars($p['nama']) ?>
            </a>
          </h2>

          <p class="kategori"><?= htmlspecialchars($nama_kategori) ?> · <?= htmlspecialchars($p['kode']) ?></p>
          <p class="harga">Rp<?= number_format($p['harga'], 0, ',', '.') ?></p>

          <?php if ($stok > 0): ?>
            <a class="tombol" href="pesan.php?produk=<?= urlencode($p['kode']) ?>">Pesan</a>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </section>
</main>

<footer class="kaki">
  &copy; 2026 Pemrograman Web · Informatika TSU
</footer>

</body>
</html>