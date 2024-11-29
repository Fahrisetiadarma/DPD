<?php
session_start();

// Cek apakah pengguna sudah login, jika tidak arahkan ke halaman login
if (!isset($_SESSION['username'])) {
    header("Location: signin.php");
    exit();
}

// Ambil peran pengguna dari session
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'User';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/dashboardstyles.css">
    <style>
        /* Styling untuk bagian konten */
        .content {
            margin-top: 20px;
        }

        .project-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .project-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 15px;
            transition: all 0.3s ease-in-out;
        }

        .project-card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .project-card h3 {
            font-size: 1.2rem;
            color: #333;
            margin-bottom: 10px;
        }

        .project-card p {
            font-size: 0.9rem;
            color: #555;
            margin-bottom: 10px;
        }

        .project-card .due-date {
            font-size: 0.85rem;
            color: #FF4500;
        }

        .project-card .status {
            font-weight: bold;
            padding: 5px;
            background-color: #FF8C00; /* Tombol status orange */
            color: white;
            border-radius: 5px;
        }

        /* Styling untuk tombol aksi cepat */
        .action-buttons {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }

        .action-button {
            padding: 10px 20px;
            background-color: #FF8C00; /* Warna orange */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .action-button:hover {
            background-color: #e07b00; /* Warna orange lebih gelap saat hover */
        }

        /* Styling untuk form edit status proyek */
        .status-select {
            margin-top: 10px;
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <h2>
            <a href="dashboard.php" style="text-decoration: none; color: inherit;">Dashboard</a>
        </h2>
        <ul>
        <?php if (strtolower($role) === 'admin'): ?>
                <li><a href="user_management.php">User Management</a></li>
            <?php endif; ?>
                <li><a href="project_management.php">Project Management</a></li>
                <li><a href="presensi.php">Presensi</a></li>
                <li><a href="logbook.php">Logbook</a></li>
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

    <div class="main-content" id="main-content">
        <header>
            <h1>Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <p>Kamu masuk sebagai <?php echo htmlspecialchars($role); ?></p>
            <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
        </header>
        <div class="content">
            <h2>Ringkasan Aktivitas & Proyek</h2>
            
            <div class="project-summary">
                <!-- Proyek 1 -->
                <div class="project-card">
                    <h3>Proyek A: Pengembangan Sistem</h3>
                    <p>Deskripsi singkat mengenai proyek A, dengan fokus pada pengembangan dan pengujian sistem.</p>
                    <span class="due-date">Tanggal Tenggat: 30 November 2024</span>

                    <div class="status">
                        Status: 
                        <?php if (strtolower($role) === 'pembimbing'): ?>
                            <form method="POST" action="update_status.php">
                                <select name="status" class="status-select">
                                    <option value="Sedang Berjalan">Sedang Berjalan</option>
                                    <option value="Menunggu Review">Menunggu Review</option>
                                    <option value="Dalam Proses">Dalam Proses</option>
                                </select>
                                <button type="submit" class="action-button">Update Status</button>
                            </form>
                        <?php else: ?>
                            Sedang Berjalan
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Proyek 2 -->
                <div class="project-card">
                    <h3>Proyek B: Implementasi Fitur Baru</h3>
                    <p>Fitur baru sedang dalam tahap implementasi. Fokus pada pengujian dan feedback pengguna.</p>
                    <span class="due-date">Tanggal Tenggat: 10 Desember 2024</span>

                    <div class="status">
                        Status: 
                        <?php if (strtolower($role) === 'pembimbing'): ?>
                            <form method="POST" action="update_status.php">
                                <select name="status" class="status-select">
                                    <option value="Sedang Berjalan">Sedang Berjalan</option>
                                    <option value="Menunggu Review">Menunggu Review</option>
                                    <option value="Dalam Proses">Dalam Proses</option>
                                </select>
                                <button type="submit" class="action-button">Update Status</button>
                            </form>
                        <?php else: ?>
                            Menunggu Review
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Proyek 3 -->
                <div class="project-card">
                    <h3>Proyek C: Pengujian QA</h3>
                    <p>Proyek ini berkaitan dengan pengujian kualitas dan perbaikan bug di sistem yang telah ada.</p>
                    <span class="due-date">Tanggal Tenggat: 5 Desember 2024</span>

                    <div class="status">
                        Status: 
                        <?php if (strtolower($role) === 'pembimbing'): ?>
                            <form method="POST" action="update_status.php">
                                <select name="status" class="status-select">
                                    <option value="Sedang Berjalan">Sedang Berjalan</option>
                                    <option value="Menunggu Review">Menunggu Review</option>
                                    <option value="Dalam Proses">Dalam Proses</option>
                                </select>
                                <button type="submit" class="action-button">Update Status</button>
                            </form>
                        <?php else: ?>
                            Dalam Proses
                        <?php endif; ?>
                    </div>
                </div>
            </div>


    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            sidebar.classList.toggle('active');

            if (sidebar.classList.contains('active')) {
                mainContent.style.marginLeft = '0';
            } else {
                mainContent.style.marginLeft = '250px'; 
            }
        }
    </script>
</body>
</html>
