<?php
require_once 'db.php'; // Pastikan untuk menyertakan koneksi database
require('lib/fpdf.php'); // Sertakan library FPDF

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: signin.php");
    exit();
}

$role = $_SESSION['role'] ?? 'User   ';
$userId = $_SESSION['user']['User   ID'] ?? null;

// Ambil data presensi
$stmt = $pdo->prepare("SELECT presensi.*, users.Nama 
                       FROM presensi 
                       JOIN users ON presensi.UserID = users.UserID 
                       " . ($role === 'User   ' ? "WHERE presensi.UserID = :user_id" : "") . "
                       ORDER BY presensi.id DESC");

if ($role === 'User   ') {
    $stmt->execute(['user_id' => $userId]);
} else {
    $stmt->execute();
}

$attendances = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Membuat instance dari FPDF dengan orientasi landscape
$pdf = new FPDF('L'); // 'L' untuk landscape
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Rekap Presensi', 0, 1, 'C'); // Judul rata tengah

// Header tabel
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(80, 10, 'Nama', 1, 0, 'C'); // Rata tengah
$pdf->Cell(60, 10, 'Tanggal', 1, 0, 'C'); // Rata tengah
$pdf->Cell(75, 10, 'Lokasi', 1, 0, 'C'); // Rata tengah
$pdf->Cell(60, 10, 'Jenis Presensi', 1, 1, 'C'); // Rata tengah

// Data presensi
$pdf->SetFont('Arial', '', 12);
foreach ($attendances as $attendance) {
    $pdf->Cell(80, 10, htmlspecialchars($attendance['Nama']), 1, 0, 'C'); // Rata tengah
    $pdf->Cell(60, 10, htmlspecialchars($attendance['tanggal']), 1, 0, 'C'); // Rata tengah
    $pdf->Cell(75, 10, htmlspecialchars($attendance['lokasi']), 1, 0, 'C'); // Rata tengah
    $pdf->Cell(60, 10, htmlspecialchars($attendance['jenis_presensi']), 1, 1, 'C'); // Rata tengah
}

// Output PDF
$pdf->Output('D', 'rekap_presensi.pdf'); // D untuk download