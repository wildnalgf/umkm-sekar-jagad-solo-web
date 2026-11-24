<?php
declare(strict_types=1);

// --- Fungsi Helper Tampilan (tetap dari P9) ---
function e(string $teks): string
{
    return htmlspecialchars($teks, ENT_QUOTES, "UTF-8");
}

function formatRupiah(int $angka): string
{
    return "Rp" . number_format($angka, 0, ",", ".");
}

function statusStok(int $stok): string
{
    if ($stok === 0) return "Habis";
    if ($stok < 10) return "Menipis";
    return "Tersedia";
}

function kelasBadge(string $status): string
{
    return match ($status) {
        "Habis"   => "badge habis",
        "Menipis" => "badge menipis",
        default   => "badge tersedia",
    };
}

// --- Fungsi Baca Database (BARU P10) ---
function ambilSemuaKategori(PDO $pdo): array
{
    $sql = "SELECT id, nama FROM kategori ORDER BY nama";
    return $pdo->query($sql)->fetchAll();
}

function ambilProduk(PDO $pdo, string $kataKunci = "", string $kategori = "Semua"): array
{
    $sql = "SELECT p.kode, p.nama, p.harga, p.stok, k.nama AS kategori
            FROM produk AS p
            JOIN kategori AS k ON k.id = p.id_kategori";
    $kondisi = [];
    $parameter = [];

    if ($kataKunci !== "") {
        $kondisi[] = "p.nama LIKE :cari";
        $parameter["cari"] = "%" . $kataKunci . "%";
    }
    if ($kategori !== "Semua") {
        $kondisi[] = "k.nama = :kategori";
        $parameter["kategori"] = $kategori;
    }

    if (count($kondisi) > 0) {
        $sql .= " WHERE " . implode(" AND ", $kondisi);
    }
    $sql .= " ORDER BY p.nama";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($parameter);
    return $stmt->fetchAll();
}

function ambilProdukByKode(PDO $pdo, string $kode): ?array
{
    $sql = "SELECT p.id, p.kode, p.nama, p.harga, p.stok, p.deskripsi,
                   k.nama AS kategori
            FROM produk AS p
            JOIN kategori AS k ON k.id = p.id_kategori
            WHERE p.kode = :kode";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["kode" => $kode]);
    $produk = $stmt->fetch();
    return $produk === false ? null : $produk;
}

// --- Validasi Pesanan (diperbarui di HO-6) ---
function validasiPesanan(array $input, ?array $produk): array
{
    $kesalahan = [];

    if (trim($input["nama"] ?? "") === "") {
        $kesalahan["nama"] = "Nama pemesan wajib diisi.";
    }

    $wa = trim($input["whatsapp"] ?? "");
    if ($wa === "" || !preg_match('/^[0-9]{10,15}$/', $wa)) {
        $kesalahan["whatsapp"] = "Nomor WhatsApp harus 10-15 digit angka.";
    }

    if ($produk === null) {
        $kesalahan["kode_produk"] = "Pilih produk dari daftar.";
    } elseif ($produk["stok"] === 0) {
        $kesalahan["kode_produk"] = "Maaf, stok produk ini habis.";
    }

    $jumlah = filter_var($input["jumlah"] ?? "", FILTER_VALIDATE_INT, [
        "options" => ["min_range" => 1, "max_range" => 20],
    ]);
    if ($jumlah === false) {
        $kesalahan["jumlah"] = "Jumlah harus angka 1-20.";
    } elseif ($produk !== null && $produk["stok"] > 0 && $jumlah > $produk["stok"]) {
        $kesalahan["jumlah"] = "Jumlah melebihi stok (tersisa {$produk['stok']}).";
    }

    if (mb_strlen($input["catatan"] ?? "") > 200) {
        $kesalahan["catatan"] = "Catatan maksimal 200 karakter.";
    }

    return $kesalahan;
}