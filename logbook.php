<?php
include 'db.php'; // Pastikan file ini mengatur koneksi PDO
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user']['UserID'] ?? null;

// Debug: cek apakah user_id tersedia, jika tidak fallback
if (!$user_id && isset($_SESSION['username'])) {
    $stmt = $pdo->prepare("SELECT UserID FROM users WHERE username = :username");
    $stmt->bindParam(':username', $_SESSION['username'], PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user']['UserID'] = $user['UserID'];
        $user_id = $user['UserID'];
    } else {
        die("Error: UserID tidak ditemukan di database. Pastikan Anda sudah login.");
    }
}

// Handle tambah/edit/hapus laporan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if (isset($_POST['add_report'])) {
            $link = $_POST['link'];
            $date = $_POST['date'];

            if (empty($link) || empty($date)) {
                die("Error: Data link atau tanggal tidak boleh kosong.");
            }

            $stmt = $pdo->prepare("INSERT INTO logbook (UserID, link, tanggal) VALUES (:user_id, :link, :tanggal)");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':link', $link);
            $stmt->bindParam(':tanggal', $date);
            $stmt->execute();
        } elseif (isset($_POST['edit_report'])) {
            $id = $_POST['report_id'];
            $link = $_POST['link'];
            $date = $_POST['date'];

            if (empty($id) || empty($link) || empty($date)) {
                die("Error: Data ID, link, atau tanggal tidak boleh kosong.");
            }

            $stmt = $pdo->prepare("UPDATE logbook SET link = :link, tanggal = :tanggal WHERE LogbookID = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':link', $link);
            $stmt->bindParam(':tanggal', $date);
            $stmt->execute();
        } elseif (isset($_POST['delete_report'])) {
            $id = $_POST['report_id'];

            if (empty($id)) {
                die("Error: ID logbook tidak ditemukan.");
            }

            $stmt = $pdo->prepare("DELETE FROM logbook WHERE LogbookID = :LogbookID");
            $stmt->bindParam(':LogbookID', $id, PDO::PARAM_INT);
            $stmt->execute();
        }
    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }
}

// Ambil data laporan
$reports = [];
$stmt = $pdo->prepare("SELECT logbook.*, users.username FROM logbook 
                       JOIN users ON logbook.UserID = users.UserID 
                       ORDER BY logbook.LogbookID DESC");
$stmt->execute();
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logbook</title>
    <link rel="stylesheet" href="css/projectmanagement.css">
</head>
<body>
<div class="sidebar" id="sidebar">
    <h2>
        <a href="dashboard.php" style="text-decoration: none; color: inherit;">Dashboard</a>
    </h2>
    <ul>
        <li><a href="user_management.php">User Management</a></li>
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
        <h1>Halo, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
    </header>
    <div class="content">
        <!-- Form untuk semua user -->
        <h1>Tambah Logbook</h1>
        <form method="post">
            <label for="link">Link Logbook:</label>
            <input type="url" name="link" placeholder="Masukkan link logbook" required>
            <label for="date">Tanggal:</label>
            <input type="date" name="date" required>
            <button type="submit" name="add_report">Tambah logbook</button>
        </form>

        <!-- Tabel untuk semua user -->
        <h2>Daftar Logbook</h2>
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Link</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report): ?>
                    <tr>
                        <td><?= htmlspecialchars($report['username']); ?></td>
                        <td><a href="<?= htmlspecialchars($report['link']); ?>" target="_blank">Lihat</a></td>
                        <td><?= htmlspecialchars($report['tanggal']); ?></td>
                        <td>
                            <form method="post" style="display:inline-block;">
                                <input type="hidden" name="report_id" value="<?= $report['LogbookID']; ?>">
                                <input type="url" name="link" value="<?= htmlspecialchars($report['link']); ?>" required>
                                <input type="date" name="date" value="<?= htmlspecialchars($report['tanggal']); ?>" required>
                                <button type="submit" name="edit_report">Edit</button>
                            </form>
                            <form method="post" style="display:inline-block;">
                                <input type="hidden" name="report_id" value="<?= $report['LogbookID']; ?>">
                                <button type="submit" name="delete_report">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
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
</script>
</body>
</html>
