<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: signin.php");
    exit();
}

require_once 'db.php';

// Cek Role dan Username
$role = $_SESSION['role'] ?? 'User';
$username = $_SESSION['username'];

// Ambil nama user yang sedang login
$stmt = $pdo->prepare("SELECT Nama, UserID FROM users WHERE username = :username");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$loggedInName = $user['Nama'] ?? 'Tidak Diketahui';
$userId = $user['UserID'] ?? null; // Fetch UserID directly from the database

// Redirect if UserID is not found
if (!$userId) {
    header("Location: signin.php");
    exit();
}

// Update session with UserID if not already set
if (!isset($_SESSION['user']['UserID'])) {
    $_SESSION['user']['UserID'] = $userId;
}

// Ambil data presensi dengan join ke tabel users untuk mengambil Nama
$attendances = [];
if (strtolower($role) === 'admin' || strtolower($role) === 'pembimbing') {
    $stmt = $pdo->query("SELECT presensi.*, users.Nama FROM presensi JOIN users ON presensi.UserID = users.UserID ORDER BY presensi.id DESC");
    $attendances = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Tangani form presensi
$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && strtolower($role) === 'magang') {
    $date = htmlspecialchars(trim($_POST['date']));
    $jenis_presensi = htmlspecialchars(trim($_POST['jenis_presensi']));
    $location = htmlspecialchars(trim($_POST['location']));

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && strtolower($role) === 'magang') {
        $date = htmlspecialchars(trim($_POST['date']));
        $jenis_presensi = htmlspecialchars(trim($_POST['jenis_presensi']));
        $location = htmlspecialchars(trim($_POST['location']));
    
        if (!$location) {
            $error_message = 'Lokasi tidak valid!';
        } else {
            // Periksa apakah presensi untuk tanggal dan jenis sudah ada
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM presensi WHERE UserID = :user_id AND tanggal = :tanggal AND jenis_presensi = :jenis_presensi");
            $stmt->execute([
                'user_id' => $userId,
                'tanggal' => $date,
                'jenis_presensi' => $jenis_presensi,
            ]);
            $count = $stmt->fetchColumn();
    
            if ($count > 0) {
                $error_message = "Presensi untuk $jenis_presensi sudah tercatat pada tanggal ini.";
            } else {
                // Simpan presensi jika belum ada
                $stmt = $pdo->prepare("INSERT INTO presensi (UserID, tanggal, lokasi, jenis_presensi) VALUES (:user_id, :tanggal, :lokasi, :jenis_presensi)");
                if ($stmt->execute([
                    'user_id' => $userId,
                    'tanggal' => $date,
                    'lokasi' => $location,
                    'jenis_presensi' => $jenis_presensi,
                ])) {
                    $success_message = 'Presensi berhasil disimpan.';
                } else {
                    $error_message = 'Terjadi kesalahan saat menyimpan presensi.';
                }
            }
        }
    }
}

// Ambil data filter tanggal dari query string
$filter_date = $_GET['filter_date'] ?? '';

// Ambil data presensi dengan filter tanggal untuk Admin dan Pembimbing
$attendances = [];
if (strtolower($role) === 'admin' || strtolower($role) === 'pembimbing') {
    if ($filter_date) {
        // Query dengan filter tanggal
        $stmt = $pdo->prepare("SELECT presensi.*, users.Nama 
                               FROM presensi 
                               JOIN users ON presensi.UserID = users.UserID 
                               WHERE presensi.tanggal = :filter_date 
                               ORDER BY presensi.id DESC");
        $stmt->execute(['filter_date' => $filter_date]);
    } else {
        // Query tanpa filter (default)
        $stmt = $pdo->query("SELECT presensi.*, users.Nama 
                             FROM presensi 
                             JOIN users ON presensi.UserID = users.UserID 
                             ORDER BY presensi.id DESC");
    }
    $attendances = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi</title>
    <link rel="stylesheet" href="css/projectmanagement.css">
    <style>
        .form-input {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            max-width: 500px;
            margin: 0 auto;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .form-group input,
        .form-group select {
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
        }

        .btn-submit {
            padding: 0.5rem 1rem;
            background-color: #ff9800;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-submit:hover {
            background-color: #e68900;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }

        table th, table td {
            padding: 0.75rem;
            text-align: left;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #ff9800;
            color: white;
        }
    </style>
</head>
<body>
    
    <!-- Sidebar' -->

    <div class="sidebar" id="sidebar">
        <h2><a href="dashboard.php" style="text-decoration: none; color: inherit;">Dashboard</a></h2>
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
            <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
            <p>You are logged in as <?php echo htmlspecialchars($role); ?></p>
            <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
        </header>

        <div class="content">

                <!-- Form untuk 'Magang' -->
                <h1>Tambah Presensi</h1>
                <?php if ($error_message): ?>
                    <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
                <?php endif; ?>
                <?php if ($success_message): ?>
                    <p class="success"><?php echo htmlspecialchars($success_message); ?></p>
                <?php endif; ?>
                <form method="post" class="form-input">
                    <div class="form-group">
                        <label for="date">Tanggal:</label>
                        <input type="date" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" readonly required>
                    </div>
                    <div class="form-group">
                        <label for="jenis_presensi">Jenis Presensi:</label>
                        <select id="jenis_presensi" name="jenis_presensi" required>
                            <option value="Kedatangan">Kedatangan</option>
                            <option value="Pulang">Pulang</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="location">Lokasi:</label>
                        <input type="text" id="location" name="location" readonly required>
                        <button type="button" onclick="getLocation()">📍 Ambil Lokasi</button>
                    </div>
                    <button type="submit" class="btn-submit">Tambah Presensi</button>
                </form>

            <!-- Filter Presensi untuk Admin dan Pembimbing' -->

            <h2>Filter Presensi</h2>
                <form method="get" style="margin-bottom: 1rem;">
                    <label for="filter_date">Tanggal:</label>
                    <input type="date" id="filter_date" name="filter_date" value="<?php echo htmlspecialchars($filter_date); ?>">
                    <button type="submit" class="btn-submit">Filter</button>
                </form>

               <!-- Tabel untuk 'Admin' dan 'Pembimbing' -->
            <h2>Daftar Presensi</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Tanggal</th>
                        <th>Lokasi</th>
                        <th>Jenis Presensi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($attendances): ?>
                        <?php foreach ($attendances as $attendance): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($attendance['Nama']); ?></td>
                                <td><?php echo htmlspecialchars($attendance['tanggal']); ?></td>
                                <td><?php echo htmlspecialchars($attendance['lokasi']); ?></td>
                                <td><?php echo htmlspecialchars($attendance['jenis_presensi']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">Belum ada data presensi.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
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

        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;
                    document.getElementById('location').value = `${latitude},${longitude}`;
                });
            } else {
                alert("Geolocation tidak didukung oleh browser Anda.");
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const dateInput = document.getElementById('date');
            const today = new Date().toISOString().split('T')[0];
            dateInput.value = today; // Set tanggal default ke hari ini
        });

    </script>
</body>
</html>
