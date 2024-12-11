<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: signin.php");
    exit();
}

require_once 'db.php';

$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'User';
$username = $_SESSION['username']; // Get the username from the session

// Fetch impressions from the database only for Admin and Pembimbing
$impressions = [];
if ($role == 'Admin' || $role == 'Pembimbing') {
    try {
        $stmt = $pdo->query("SELECT * FROM kesan_dan_pesan ORDER BY id DESC");
        $impressions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error fetching data: " . $e->getMessage());
    }
}

// Fetch only impressions by the logged-in user for Magang
if ($role == 'Magang') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM kesan_dan_pesan WHERE username = ? ORDER BY id DESC");
        $stmt->execute([$username]);
        $impressions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error fetching data: " . $e->getMessage());
    }
}

// Check if editing
$editImpression = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM kesan_dan_pesan WHERE id = ?");
        $stmt->execute([$editId]);
        $editImpression = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$editImpression) {
            die("Data not found for editing.");
        }
        // Check if Magang is trying to edit someone else's impression
        if ($role == 'Magang' && $editImpression['username'] !== $username) {
            die("You can only edit your own impressions.");
        }
    } catch (PDOException $e) {
        die("Error fetching data for edit: " . $e->getMessage());
    }
}

// Handle delete action
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    try {
        // Only Admin and Pembimbing can delete
        if ($role == 'Admin' || $role == 'Pembimbing') {
            $stmt = $pdo->prepare("DELETE FROM kesan_dan_pesan WHERE id = ?");
            $stmt->execute([$deleteId]);
            header("Location: kesan.php");
            exit();
        } else {
            die("You do not have permission to delete this impression.");
        }
    } catch (PDOException $e) {
        die("Error deleting data: " . $e->getMessage());
    }
}

// Handle approval action
if (isset($_GET['approve'])) {
    $approveId = (int)$_GET['approve'];
    try {
        $stmt = $pdo->prepare("UPDATE kesan_dan_pesan SET status = 'approved' WHERE id = ?");
        $stmt->execute([$approveId]);
        header("Location: kesan.php");
        exit();
    } catch (PDOException $e) {
        die("Error updating status: " . $e->getMessage());
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $jurusan = htmlspecialchars($_POST['jurusan']);
    $universitas = htmlspecialchars($_POST['universitas']);
    $message = htmlspecialchars($_POST['message']);

    if (isset($_POST['id']) && $_POST['id'] !== '') {
        // Edit existing impression
        $id = (int)$_POST['id'];
        try {
            $stmt = $pdo->prepare("UPDATE kesan_dan_pesan SET name = ?, jurusan = ?, universitas = ?, message = ? WHERE id = ?");
            $stmt->execute([$name, $jurusan, $universitas, $message, $id]);
        } catch (PDOException $e) {
            die("Error updating data: " . $e->getMessage());
        }
    } else {
        // Add new impression
        try {
            $stmt = $pdo->prepare("INSERT INTO kesan_dan_pesan (name, jurusan, universitas, message, username) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $jurusan, $universitas, $message, $username]); // Store the username for Magang
        } catch (PDOException $e) {
            die("Error inserting data: " . $e->getMessage());
        }
    }

    header("Location: kesan.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kesan dan Pesan</title>
    <link rel="stylesheet" href="css/kesan.css">
    <style>
        /* Form Styles */
        .form-container {
            background: linear-gradient(135deg, #FFEDD5, #FDBA74);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            margin: 30px auto;
            color: #1e293b;
        }

        .form-container h2 {
            text-align: center;
            color: #EA580C;
            font-size: 1.8rem;
            margin-bottom: 20px;
        }

        .form-container label {
            font-weight: bold;
            font-size: 1rem;
            margin-bottom: 10px;
            display: block;
            color: #9A3412;
        }

        .form-container input,
        .form-container textarea {
            width: 95%;
            padding: 12px 15px;
            border: 1px solid #FCD34D;
            border-radius: 8px;
            font-size: 1rem;
            color: #1e293b;
            background-color: #FFF7ED;
            margin-bottom: 15px;
            transition: box-shadow 0.3s, transform 0.2s;
        }

        .form-container input:focus,
        .form-container textarea:focus {
            box-shadow: 0 0 8px rgba(234, 88, 12, 0.6);
            transform: scale(1.02);
            outline: none;
        }

        .form-container button {
            width: 100%;
            padding: 15px;
            font-size: 1.2rem;
            font-weight: bold;
            background: #FB923C;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }

        .form-container button:hover {
            background: #F97316;
            transform: scale(1.05);
        }

        /* Impression List Styles */
        .impression-card {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            background: #FFF7ED;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .impression-card h2, .impression-card h3 {
            color: #EA580C;
            margin-bottom: 10px;
        }

        .impression-card p {
            margin-bottom: 10px;
            color: #374151;
        }

        .approve-btn {
            display: inline-block;
            padding: 8px 12px;
            background: #38A169; /* Green */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
            transition: background 0.3s;
        }

        .approve-btn:hover {
            background: #2F855A; /* Darker green */
        }
    </style>
</head>
<body>
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
            <h1>Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <p>Kamu masuk sebagai <?php echo htmlspecialchars($role); ?></p>
            <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
        </header>
        <div class="content">
            <h1>Kesan dan Pesan</h1>

            <!-- Form Input (only for Magang) -->
            <?php if ($role == 'Magang'): ?>
                <div class="form-container">
                <form method="POST" action="">
                    <?php if ($editImpression): ?>
                        <input type="hidden" name="id" value="<?php echo $editImpression['id']; ?>">
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="name">Nama</label>
                        <input type="text" id="name" name="name" placeholder="Masukkan nama Anda" 
                            value="<?php echo $editImpression ? htmlspecialchars($editImpression['name']) : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="jurusan">Jurusan</label>
                        <input type="text" id="jurusan" name="jurusan" placeholder="Masukkan jurusan Anda"
                            value="<?php echo $editImpression ? htmlspecialchars($editImpression['jurusan']) : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="universitas">Universitas</label>
                        <input type="text" id="universitas" name="universitas" placeholder="Masukkan universitas Anda"
                            value="<?php echo $editImpression ? htmlspecialchars($editImpression['universitas']) : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Kesan dan Pesan</label>
                        <textarea id="message" name="message" placeholder="Masukkan kesan dan pesan" rows="4" required><?php echo $editImpression ? htmlspecialchars($editImpression['message']) : ''; ?></textarea>
                    </div>
                    <button type="submit">Simpan</button>
                </form>
            </div>
            <?php endif; ?>

            <!-- Daftar Kesan dan Pesan -->
                    <?php if ($role == 'Admin' || $role == 'Pembimbing'): ?>
                        <div>
                            <h2>Daftar Kesan dan Pesan</h2>
                            <?php if (count($impressions) > 0): ?>
                                <?php foreach ($impressions as $impression): ?>
                                    <div class="impression-card">
                                        <h2><?php echo htmlspecialchars($impression['name']); ?></h2>
                                        <p>Jurusan: <?php echo htmlspecialchars($impression['jurusan']); ?></p>
                                        <p>Universitas: <?php echo htmlspecialchars($impression['universitas']); ?></p>
                                        <p><?php echo nl2br(htmlspecialchars($impression['message'])); ?></p>
                                        <p>Status: 
                                            <strong style="color: 
                                                <?php echo $impression['status'] === 'approved' ? 'green' : 'orange'; ?>">
                                                <?php echo ucfirst($impression['status']); ?>
                                            </strong>
                                        </p>
                                        <?php if ($impression['status'] !== 'approved'): ?>
                                            <a href="kesan.php?approve=<?php echo $impression['id']; ?>" class="approve-btn">Approve</a>
                                        <?php endif; ?>
                                        <!-- Delete button for Admin and Pembimbing -->
                                        <a href="kesan.php?delete=<?php echo $impression['id']; ?>" 
                                        class="approve-btn" style="background-color: #E53E3E; margin-top: 10px; text-decoration: none;">
                                            Delete
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>No impressions yet.</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
    <script src="js/sidebar.js"></script>
</body>
</html>
