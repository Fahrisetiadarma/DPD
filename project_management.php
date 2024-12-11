<?php
include 'db.php'; // Pastikan ini berisi konfigurasi koneksi PDO yang benar
session_start();

// Redirect jika tidak ada sesi aktif
if (!isset($_SESSION['username'])) {
    header("Location: signin.php");
    exit();
}

// Ambil informasi sesi
$role = $_SESSION['role'] ?? 'User';
$username = $_SESSION['username'];

// Cari UserID berdasarkan username
$stmt = $pdo->prepare("SELECT UserID FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user) {
    die("Error: User ID tidak ditemukan. Pastikan username valid.");
}

$user_id = $user['UserID'];

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_project'])) {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $deadline = trim($_POST['deadline']);

        // Validasi input
        if (!empty($title) && !empty($description) && !empty($deadline)) {
            $stmt = $pdo->prepare(
                "INSERT INTO project_management (JudulProject, Description, UserID, Deadline) 
                 VALUES (:title, :description, :user_id, :deadline)"
            );
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':deadline', $deadline);
            $stmt->execute();
        } else {
            echo "<p style='color:red;'>Semua field harus diisi!</p>";
        }
    } elseif (isset($_POST['delete_project'])) {
        $project_id = intval($_POST['project_id']);

        // Hapus proyek hanya jika milik user yang login atau jika user adalah admin/pembimbing
        if (strtolower($role) === 'admin' || strtolower($role) === 'pembimbing') {
            // Admin dan Pembimbing dapat menghapus proyek tanpa batasan
            $stmt = $pdo->prepare(
                "DELETE FROM project_management WHERE ProjectID = :project_id"
            );
            $stmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
        } else {
            // Magang hanya dapat menghapus proyek mereka sendiri
            $stmt = $pdo->prepare(
                "DELETE FROM project_management WHERE ProjectID = :project_id AND UserID = :user_id"
            );
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
        }
        $stmt->execute();
    }
}

// Ambil proyek berdasarkan peran
$projects = [];
if (strtolower($role) === 'admin' || strtolower($role) === 'pembimbing') {
    // Admin dan Pembimbing dapat melihat semua proyek
    $stmt = $pdo->prepare("SELECT pm.*, u.username FROM project_management pm JOIN users u ON pm.UserID = u.UserID");
    $stmt->execute();
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif (strtolower($role) === 'magang') {
    // Magang hanya melihat proyek yang mereka input
    $stmt = $pdo->prepare("SELECT pm.*, u.username FROM project_management pm JOIN users u ON pm.UserID = u.UserID WHERE pm.UserID = ?");
    $stmt->execute([$user_id]);
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Untuk peran lain, dapatkan proyek mereka sendiri
    $stmt = $pdo->prepare("SELECT pm.*, u.username FROM project_management pm JOIN users u ON pm.UserID = u.UserID WHERE pm.UserID = ?");
    $stmt->execute([$user_id]);
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Proyek</title>
    <link rel="stylesheet" href="css/projectmanagement.css">
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
        <!-- Form untuk tambah proyek -->
        <h1>Tambah Proyek Baru</h1>
        <form method="post">
            <input type="text" name="title" placeholder="Judul Proyek" required>
            <textarea name="description" placeholder="Deskripsi Proyek" required></textarea>
            <input type="date" name="deadline" placeholder="Tanggal Deadline" required>
            <button type="submit" name="add_project">Tambah Proyek</button>
        </form>

        <!-- Tampilkan daftar proyek -->
        <h1>Daftar Proyek</h1>
        <table>
            <thead>
                <tr>
                    <th>Judul Proyek</th>
                    <th>Deskripsi</th>
                    <th>Deadline</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $project): ?>
                    <tr>
                        <td><?= htmlspecialchars($project['JudulProject']); ?></td>
                        <td><?= htmlspecialchars($project['Description']); ?></td>
                        <td><?= htmlspecialchars($project['Deadline']); ?></td>
                        <td>
                            <form method="post" style="display:inline-block;">
                                <input type="hidden" name="project_id" value="<?= $project['ProjectID']; ?>">
                                <button type="submit" name="delete_project">Hapus</button>
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