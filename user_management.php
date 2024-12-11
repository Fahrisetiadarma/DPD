<?php
include 'db.php';
session_start();

// Cek apakah pengguna sudah login, jika tidak arahkan ke halaman login
if (!isset($_SESSION['username'])) {
    header("Location: signin.php");
    exit();
}

// Ambil peran pengguna dari session
$role = $_SESSION['role'] ?? 'User';

// Batasi akses untuk role selain 'Admin'
if (strtolower($role) !== 'admin') {
    // Arahkan kembali ke dashboard atau tampilkan pesan kesalahan
    echo "<script>alert('Anda tidak memiliki izin untuk mengakses halaman ini.'); window.location.href='dashboard.php';</script>";
    exit();
}

$updateSuccess = false;

// Proses penghapusan atau pembaruan role
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_user'])) {
        $id = (int)$_POST['user_id'];
        
        try {
            // Mulai transaksi untuk memastikan konsistensi data
            $pdo->beginTransaction();

            // Hapus data terkait di tabel presensi terlebih dahulu
            $stmt = $pdo->prepare("DELETE FROM presensi WHERE UserID = :id");
            $stmt->execute(['id' => $id]);

            // Hapus data terkait di tabel logbook (jika ada)
            $stmt = $pdo->prepare("DELETE FROM logbook WHERE UserID = :id");
            $stmt->execute(['id' => $id]);

            // Hapus data pengguna di tabel users
            $stmt = $pdo->prepare("DELETE FROM users WHERE UserID = :id");
            $stmt->execute(['id' => $id]);

            // Commit transaksi jika tidak ada error
            $pdo->commit();

            echo "<script>alert('Pengguna dan data terkait berhasil dihapus.'); window.location.href='user_management.php';</script>";
        } catch (PDOException $e) {
            // Rollback transaksi jika terjadi kesalahan
            $pdo->rollBack();
            echo "<script>alert('Error: " . $e->getMessage() . "'); window.location.href='user_management.php';</script>";
        }
    } elseif (isset($_POST['update_role'])) {
        $id = (int)$_POST['user_id'];
        $newRole = $_POST['role'];
        $stmt = $pdo->prepare("UPDATE users SET role = :role WHERE UserID = :id");
        $stmt->execute(['role' => $newRole, 'id' => $id]);

        if ($stmt->rowCount() > 0) {
            $updateSuccess = true;
        }
    }
}

// Mengambil data pengguna dari database
$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="css/usermanagemet.css">
    <script>
        function showUpdateAlert() {
            alert('Role berhasil diperbarui!');
        }
    </script>
</head>
<body>
    <?php if ($updateSuccess): ?>
        <script>showUpdateAlert();</script>
    <?php endif; ?>

    <div class="sidebar" id="sidebar">
        <h2>
            <a href="dashboard.php" style="text-decoration: none; color: inherit;">Dashboard</a>
        </h2>
        <ul>
            <li><a href="user_management.php" class="active">User Management</a></li>
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
            <h1>Manajemen User</h1>
            <table border="1" cellpadding="10">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['Nama']); ?></td>
                            <td><?= htmlspecialchars($user['Email']); ?></td>
                            <td><?= htmlspecialchars($user['role']); ?></td>
                            <td>
                                <form method="post" style="display:inline-block;">
                                    <input type="hidden" name="user_id" value="<?= $user['UserID']; ?>">
                                    <select name="role">
                                        <option value="Admin" <?= $user['role'] === 'Admin' ? 'selected' : ''; ?>>Admin</option>
                                        <option value="Pembimbing" <?= $user['role'] === 'Pembimbing' ? 'selected' : ''; ?>>Pembimbing</option>
                                        <option value="Magang" <?= $user['role'] === 'Magang' ? 'selected' : ''; ?>>Magang</option>
                                    </select>
                                    <button type="submit" name="update_role">Update Role</button>
                                </form>
                                <form method="post" style="display:inline-block;">
                                    <input type="hidden" name="user_id" value="<?= $user['UserID']; ?>">
                                    <button type="submit" name="delete_user">Hapus</button>
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
