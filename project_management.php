<?php
include 'db.php'; // Pastikan ini berisi konfigurasi koneksi PDO yang benar
session_start();

// Redirect jika tidak ada sesi aktif
if (!isset($_SESSION['username'])) {
    header("Location: signin.php");
    exit();
}

// Variabel sesi
$role = $_SESSION['role'] ?? 'User';
$user_id = $_SESSION['user']['UserID'] ?? null;

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
            echo "Semua field harus diisi!";
        }
    } elseif (isset($_POST['delete_project'])) {
        $project_id = intval($_POST['project_id']);

        // Hapus proyek hanya jika milik user yang login
        $stmt = $pdo->prepare(
            "DELETE FROM project_management WHERE ProjectID = :project_id AND UserID = :user_id"
        );
        $stmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
    }
}

// Ambil proyek milik user yang login hanya jika role bukan 'Magang'
$projects = [];
if (strtolower($role) !== 'magang') {
    $stmt = $pdo->prepare("SELECT * FROM project_management WHERE UserID = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
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
        <?php if (strtolower($role) === 'magang'): ?>
            <!-- Form untuk role 'Magang' -->
            <h1>Tambah Proyek Baru</h1>
            <form method="post">
                <input type="text" name="title" placeholder="Judul Proyek" required>
                <textarea name="description" placeholder="Deskripsi Proyek" required></textarea>
                <input type="date" name="deadline" placeholder="Tanggal Deadline" required>
                <button type="submit" name="add_project">Tambah Proyek</button>
            </form>
        <?php else: ?>
            <!-- Daftar proyek untuk role 'Admin' dan 'Pembimbing' -->
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
        <?php endif; ?>
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
