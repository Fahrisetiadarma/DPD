<?php
session_start();

// Cek Login
if (!isset($_SESSION['username'])) {
    header("Location: signin.php");
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panduan Magang</title>
    <link rel="stylesheet" href="css/projectmanagement.css">
    <style>
        /* Styling untuk halaman panduan */
        .content {
            margin: 20px;
        }

        .section {
            margin-bottom: 30px;
        }

        .section h2 {
            color: #FFA500; /* Ubah warna hijau menjadi oranye */
            margin-bottom: 10px;
        }

        .section ul {
            list-style-type: none;
            padding: 0;
        }

        /* Styling untuk daftar download */
        .download-item {
            background-color: #f9f9f9;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .download-item a {
            color: #FFA500; /* Oranye */
            font-size: 1.1em;
            font-weight: bold;
            text-decoration: none;
            margin-bottom: 8px;
        }

        .download-item p {
            margin: 0;
            color: #555;
            font-size: 0.9em;
        }

        .download-item:hover {
            background-color: #FFF4E0; /* Oranye terang */
            border-color: #FFA500;
            transform: scale(1.02);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .download-item a:hover {
            text-decoration: underline;
        }

        /* Styling tombol kembali */
        .btn-back {
            padding: 10px 20px;
            background-color: #FFA500; /* Ubah warna hijau menjadi oranye */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        .btn-back:hover {
            background-color: #FF8C00; /* Lebih gelap dari oranye */
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h2><a href="dashboard.php" style="text-decoration: none; color: inherit;">Dashboard</a></h2>
        <ul>
            <li><a href="user_management.php">User Management</a></li>
            <li><a href="project_management.php">Project Management</a></li>
            <li><a href="presensi.php">Presensi</a></li>
            <li><a href="logbook">Logbook</a></li>
            <li><a href="laporan_akhir.php">Laporan Akhir</a></li>
            <li><a href="knowledge_sharing.php">Knowledge Sharing</a></li>
            <li><a href="pengenalan_dpd.php">Pengenalan DPD RI</a></li>
            <li><a href="kesan.php">Kesan dan Pesan</a></li>
            <li><a href="faq.php">FAQ</a></li>
            <li><a href="panduan.php">Panduan</a></li>
            <li><a href="profil.php">Profil</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <header>
            <h1>Selamat datang, <?php echo htmlspecialchars($username); ?>!</h1>
        </header>

        <div class="content">
            <h1>Panduan Magang</h1>

            <div class="section">
                <h2>Dokumen-Dokumen Magang</h2>
                <ul>
                    <li class="download-item">
                        <a href="https://drive.google.com/uc?export=download&id=1MJO7O7-1p-MCXt7qRmme5TFMjF0W-sAa" target="_blank">Template Spreadsheet</a>
                        <p>Unduh template spreadsheet untuk keperluan magang Anda.</p>
                    </li>
                    <li class="download-item">
                        <a href="https://drive.google.com/uc?export=download&id=1k1dY0-Ox5nQMPhDTVEKU8l2_0snV-MYD" target="_blank">Panduan Magang</a>
                        <p>Unduh panduan magang dalam format dokumen.</p>
                    </li>
                    <li class="download-item">
                        <a href="dokumen/panduan_laporan.pdf" download>Panduan Penyusunan Laporan Akhir</a>
                        <p>Unduh panduan untuk menyusun laporan akhir yang sesuai dengan standar yang ditetapkan.</p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
