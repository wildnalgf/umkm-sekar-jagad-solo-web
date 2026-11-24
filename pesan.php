<?php
declare(strict_types=1);

require __DIR__ . "/data.php";
require __DIR__ . "/koneksi.php";
require __DIR__ . "/functions.php";

$daftarProduk = ambilProduk($pdo);

$produkDipilih = (string) filter_input(INPUT_GET, "produk");
$input = [
    "nama"        => "",
    "whatsapp"    => "",
    "kode_produk" => $produkDipilih,
    "jumlah"      => "1",
    "catatan"     => "",
];
$kesalahan = [];
$pesananBerhasil = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input["nama"]        = trim((string) ($_POST["nama"] ?? ""));
    $input["whatsapp"]    = trim((string) ($_POST["whatsapp"] ?? ""));
    $input["kode_produk"] = trim((string) ($_POST["kode_produk"] ?? ""));
    $input["jumlah"]      = trim((string) ($_POST["jumlah"] ?? ""));
    $input["catatan"]     = trim((string) ($_POST["catatan"] ?? ""));

    $produk = ambilProdukByKode($pdo, $input["kode_produk"]);
    $kesalahan = validasiPesanan($input, $produk);

    if (count($kesalahan) === 0 && $produk !== null) {
        $jumlah = (int) $input["jumlah"];
        $total  = (int) $produk["harga"] * $jumlah;

        try {
            $pdo->beginTransaction();

            // Menyesuaikan nama kolom tabel pesanan secara otomatis
            $kolomTabel = $pdo->query("SHOW COLUMNS FROM pesanan")->fetchAll(PDO::FETCH_COLUMN);
            if (in_array("id", $kolomTabel, true)) {
                $nomorUrut = (int) $pdo->query("SELECT COALESCE(MAX(id), 0) FROM pesanan")->fetchColumn() + 1;
            } else {
                $nomorUrut = (int) $pdo->query("SELECT COUNT(*) FROM pesanan")->fetchColumn() + 1;
            }
            $kodePesanan = sprintf("PSN-%04d", $nomorUrut);

            $dataInsert = [];
            foreach ($kolomTabel as $kol) {
                switch ($kol) {
                    case "kode":
                    case "kode_pesanan":
                        $dataInsert[$kol] = $kodePesanan;
                        break;
                    case "nama":
                    case "nama_pemesan":
                        $dataInsert[$kol] = $input["nama"];
                        break;
                    case "whatsapp":
                    case "no_wa":
                    case "no_hp":
                    case "telepon":
                        $dataInsert[$kol] = $input["whatsapp"];
                        break;
                    case "id_produk":
                    case "produk_id":
                        $dataInsert[$kol] = $produk["id"];
                        break;
                    case "kode_produk":
                    case "produk_kode":
                        $dataInsert[$kol] = $produk["kode"];
                        break;
                    case "nama_produk":
                        $dataInsert[$kol] = $produk["nama"];
                        break;
                    case "jumlah":
                    case "qty":
                        $dataInsert[$kol] = $jumlah;
                        break;
                    case "harga":
                    case "harga_satuan":
                        $dataInsert[$kol] = (int) $produk["harga"];
                        break;
                    case "total":
                    case "total_harga":
                        $dataInsert[$kol] = $total;
                        break;
                    case "catatan":
                        $dataInsert[$kol] = $input["catatan"];
                        break;
                }
            }

            $namaKolom   = array_keys($dataInsert);
            $placeholder = array_fill(0, count($namaKolom), "?");
            $sqlInsert   = "INSERT INTO pesanan (" . implode(", ", $namaKolom) . ") VALUES (" . implode(", ", $placeholder) . ")";
            $stmtInsert  = $pdo->prepare($sqlInsert);
            $stmtInsert->execute(array_values($dataInsert));

            // Otomatis kurangi stok produk di database
            $stmtStok = $pdo->prepare("UPDATE produk SET stok = stok - ? WHERE kode = ? AND stok >= ?");
            $stmtStok->execute([$jumlah, $produk["kode"], $jumlah]);

            $pdo->commit();

            // Perbarui daftar produk agar stok di pilihan dropdown langsung berkurang
            $daftarProduk = ambilProduk($pdo);

            $pesananBerhasil = [
                "kode"        => $kodePesanan,
                "nama"        => $input["nama"],
                "whatsapp"    => $input["whatsapp"],
                "nama_produk" => $produk["nama"],
                "jumlah"      => $jumlah,
                "total"       => $total,
            ];

            $input = [
                "nama"        => "",
                "whatsapp"    => "",
                "kode_produk" => "",
                "jumlah"      => "1",
                "catatan"     => "",
            ];
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $kesalahan["kode_produk"] = "Gagal menyimpan pesanan: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pesan Produk - <?= e($namaToko) ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header class="kepala">
    <h1><?= e($namaToko) ?></h1>
    <nav class="menu">
      <a href="index.php">Katalog</a>
      <a href="pesan.php" class="aktif">Pesan</a>
      <a href="daftar-pesanan.php">Daftar Pesanan</a>
    </nav>
  </header>

  <main class="wadah sempit">
    <h2>Form Pemesanan</h2>

    <?php if ($pesananBerhasil !== null): ?>
      <div class="kartu">
        <span class="badge tersedia">Pesanan Dicatat</span>
        <p><strong>Kode Pesanan:</strong> <?= e($pesananBerhasil["kode"]) ?></p>
        <p><strong>Pemesan:</strong> <?= e($pesananBerhasil["nama"]) ?> (<?= e($pesananBerhasil["whatsapp"]) ?>)</p>
        <p><strong>Produk:</strong> <?= e($pesananBerhasil["nama_produk"]) ?> &times; <?= $pesananBerhasil["jumlah"] ?></p>
        <p><strong>Total:</strong> <?= formatRupiah($pesananBerhasil["total"]) ?></p>
      </div>
    <?php endif; ?>

    <form action="pesan.php" method="post" class="kartu">
      <p>
        <label for="nama">Nama Pemesan</label><br>
        <input type="text" id="nama" name="nama" value="<?= e($input["nama"]) ?>" required>
        <?php if (isset($kesalahan["nama"])): ?>
          <small style="color:red"><?= e($kesalahan["nama"]) ?></small>
        <?php endif; ?>
      </p>

      <p>
        <label for="whatsapp">Nomor WhatsApp</label><br>
        <input type="tel" id="whatsapp" name="whatsapp" placeholder="081234567890" value="<?= e($input["whatsapp"]) ?>" required>
        <?php if (isset($kesalahan["whatsapp"])): ?>
          <small style="color:red"><?= e($kesalahan["whatsapp"]) ?></small>
        <?php endif; ?>
      </p>

      <p>
        <label for="kode_produk">Pilih Produk</label><br>
        <select id="kode_produk" name="kode_produk" required>
          <option value="">-- Pilih Produk --</option>
          <?php foreach ($daftarProduk as $item): ?>
            <option value="<?= e($item["kode"]) ?>" <?= $item["kode"] === $input["kode_produk"] ? "selected" : "" ?>>
              <?= e($item["nama"]) ?> - <?= formatRupiah($item["harga"]) ?> (Stok: <?= $item["stok"] ?>)
            </option>
          <?php endforeach; ?>
        </select>
        <?php if (isset($kesalahan["kode_produk"])): ?>
          <small style="color:red"><?= e($kesalahan["kode_produk"]) ?></small>
        <?php endif; ?>
      </p>

      <p>
        <label for="jumlah">Jumlah</label><br>
        <input type="number" id="jumlah" name="jumlah" min="1" max="20" value="<?= e($input["jumlah"]) ?>" required>
        <?php if (isset($kesalahan["jumlah"])): ?>
          <small style="color:red"><?= e($kesalahan["jumlah"]) ?></small>
        <?php endif; ?>
      </p>

      <p>
        <label for="catatan">Catatan (opsional)</label><br>
        <textarea id="catatan" name="catatan" rows="3" maxlength="200"><?= e($input["catatan"]) ?></textarea>
        <?php if (isset($kesalahan["catatan"])): ?>
          <small style="color:red"><?= e($kesalahan["catatan"]) ?></small>
        <?php endif; ?>
      </p>

      <button type="submit" class="tombol">Kirim Pesanan</button>
    </form>
  </main>

  <footer class="kaki">
    <p>&copy; <?= date("Y") ?> Pemrograman Web &middot; Informatika TSU</p>
  </footer>
</body>
</html>