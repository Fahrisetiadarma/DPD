<?php
// Database Connection
include 'db.php';
session_start();

// Cek Login
if (!isset($_SESSION['username'])) {
    header("Location: signin.php");
    exit();
}

$role = $_SESSION['role'] ?? 'User';
$username = $_SESSION['username'];

// Ambil UserID berdasarkan username yang ada dalam session
$stmt = $pdo->prepare("SELECT UserID FROM users WHERE username = :username");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $userID = $user['UserID'];

    // Ambil nama user yang sedang login berdasarkan UserID
    $stmt = $pdo->prepare("SELECT Nama FROM users WHERE UserID = :UserID");
    $stmt->execute(['UserID' => $userID]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $loggedInName = $user['Nama'] ?? 'Tidak Diketahui';
} else {
    $loggedInName = 'Tidak Diketahui';
}

// Variabel untuk pesan
$error_message = '';
$success_message = '';

// Handle POST Request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_entry']) && $role === 'Magang') { // Batasi penambahan data untuk role Magang
        $hari_tanggal = htmlspecialchars(trim($_POST['hari_tanggal'])); // Input format YYYY-MM-DD
        $jam = htmlspecialchars(trim($_POST['jam']));
        $judul = htmlspecialchars(trim($_POST['judul']));
        $link_ppt = filter_var($_POST['link_ppt'], FILTER_VALIDATE_URL);
        $link_video = filter_var($_POST['link_video'], FILTER_VALIDATE_URL); // Tambahkan validasi untuk link_video
        $nama = htmlspecialchars(trim($_POST['nama'])); // Ambil nama yang diinput

        if (!$link_ppt || !$link_video) {
            $error_message = 'Link PPT atau Link Video tidak valid!';
        } else {
            // Insert data ke tabel knowledge_sharing
            $stmt = $pdo->prepare("INSERT INTO knowledge_sharing (hari_tanggal, jam, nama, judul, link_ppt, link_video) 
                                   VALUES (:hari_tanggal, :jam, :nama, :judul, :link_ppt, :link_video)");
            if ($stmt->execute([
                'hari_tanggal' => $hari_tanggal,
                'jam' => $jam,
                'nama' => $nama, // Simpan nama yang diinput
                'judul' => $judul,
                'link_ppt' => $link_ppt,
                'link_video' => $link_video // Simpan link video
            ])) {
                $success_message = 'Data berhasil ditambahkan.';
            } else {
                $error_message = 'Terjadi kesalahan saat menyimpan data.';
            }
        }
    } elseif (isset($_POST['delete_entry']) && ($role === 'Admin' || $role === 'Pembimbing')) { // Hanya Admin/Pembimbing yang bisa menghapus
        $id = filter_var($_POST['entry_id'], FILTER_VALIDATE_INT);
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM knowledge_sharing WHERE id = :id");
            if ($stmt->execute(['id' => $id])) {
                $success_message = 'Data berhasil dihapus.';
            } else {
                $error_message = 'Terjadi kesalahan saat menghapus data.';
            }
        }
    }
}

// Fetch Entries
$stmt = $pdo->query("SELECT * FROM knowledge_sharing ORDER BY SK_ID DESC");
$entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Knowledge Sharing</title>
    <link rel="stylesheet" href="css/projectmanagement.css">
    <style>
        /* Form Styling */
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

        .form-group input {
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
        }

        .btn-submit {
            padding: 0.5rem 1rem;
            background-color: #FFA500;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-submit:hover {
            background-color: #FF8C00;
        }

        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <!-- Sidebar -->
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

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <header>
            <h1>Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <p>Kamu masuk sebagai <?php echo htmlspecialchars($role); ?></p>
            <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
        </header>

        <div class="content">
            <h1>Knowledge Sharing</h1>

            <!-- Pesan Error atau Sukses -->
            <?php if ($error_message): ?>
                <p class="error"><?= htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            <?php if ($success_message): ?>
                <p class="success"><?= htmlspecialchars($success_message); ?></p>
            <?php endif; ?>

            <!-- Form Tambah Data (Hanya untuk Magang) -->
            <?php if ($role === 'Magang'): ?>
                <form method="post" class="form-input">
                    <div class="form-group">
                        <label for="nama">Nama:</label>
                        <input type="text" name="nama" id="nama" value="<?= htmlspecialchars($loggedInName); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="hari_tanggal">Hari dan Tanggal:</label>
                        <input type="date" name="hari_tanggal" id="hari_tanggal" required>
                    </div>
                    <div class="form-group">
                        <label for="jam">Jam:</label>
                        <input type="text" name="jam" id="jam" placeholder="Jam (contoh: 10:00 AM)" required>
                    </div>
                    <div class="form-group">
                        <label for="judul">Judul:</label>
                        <input type="text" name="judul" id="judul" placeholder="Judul" required>
                    </div>
                    <div class="form-group">
                        <label for="link_ppt">Link PPT:</label>
                        <input type="url" name="link_ppt" id="link_ppt" placeholder="Link PPT" required>
                    </div>
                    <div class="form-group">
                        <label for="link_video">Link Video:</label>
                        <input type="url" name="link_video" id="link_video" placeholder="Link Video" required>
                    </div>
                    <button type="submit" name="add_entry" class="btn-submit">Tambah</button>
                </form>
            <?php endif; ?>

            <!-- Daftar Knowledge Sharing -->
            <h2>Daftar Knowledge Sharing</h2>
            <table border="1" cellpadding="10">
                <thead>
                    <tr>
                        <th>Hari dan Tanggal</th>
                        <th>Jam</th>
                        <th>Nama</th>
                        <th>Judul</th>
                        <th>Link PPT</th>
                        <th>Link Video</th> <!-- Tambahkan kolom untuk link video -->
                        <?php if ($role === 'Admin' || $role === 'Pembimbing'): ?>
                            <th>Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($entries): ?>
                        <?php foreach ($entries as $entry): ?>
                            <tr>
                                <td><?= htmlspecialchars($entry['hari_tanggal']); ?></td>
                                <td><?= htmlspecialchars($entry['jam']); ?></td>
                                <td><?= htmlspecialchars($entry['nama']); ?></td>
                                <td><?= htmlspecialchars($entry['judul']); ?></td>
                                <td><a href="<?= htmlspecialchars($entry['link_ppt']); ?>" target="_blank">Lihat PPT</a></td>
                                <td>
                                    <?php if (!empty($entry['link_video']) && filter_var($entry['link_video'], FILTER_VALIDATE_URL)): ?>
                                        <a href="<?= htmlspecialchars($entry['link_video']); ?>" target="_blank">Lihat Video</a>
                                    <?php else: ?>
                                        Tidak tersedia
                                    <?php endif; ?>
                                </td>
                                <?php if ($role === 'Admin' || $role === 'Pembimbing'): ?>
                                    <td>
                                        <form method="post">
                                            <input type="hidden" name="entry_id" value="<?= htmlspecialchars($entry['SK_ID']); ?>">
                                            <button type="submit" name="delete_entry" class="btn-submit">Hapus</button>
                                        </form>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?php echo ($role === 'Admin' || $role === 'Pembimbing') ? '7' : '6'; ?>">Tidak ada data untuk ditampilkan</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>
    </div>
</body>
</html>
