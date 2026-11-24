<?php
declare(strict_types=1);

const DB_HOST = "localhost";
const DB_NAME = "toko_umkm";
const DB_USER = "root";
const DB_PASS = "1234";

function buatKoneksi(): PDO
{
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $opsi = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    return new PDO($dsn, DB_USER, DB_PASS, $opsi);
}

try {
    $pdo = buatKoneksi();
} catch (PDOException $e) {
    error_log("Koneksi database gagal: " . $e->getMessage());
    http_response_code(500);
    exit("Maaf, database belum dapat diakses. Pastikan MySQL di Laragon sudah berjalan.");
}