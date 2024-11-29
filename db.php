<?php
// db.php

$host = 'localhost'; // Alamat database server
$db = 'lms_magang'; // Ganti dengan nama database Anda
$user = 'root'; // Ganti dengan username database Anda
$pass = ''; // Ganti dengan password database Anda jika ada

try {
    // Membuat koneksi PDO
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Menangani error dengan exception
} catch (PDOException $e) {
    // Menangani error jika gagal koneksi
    die("Gagal terhubung ke database: " . $e->getMessage());
}
?>
