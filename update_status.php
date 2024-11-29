<?php
require 'db.php'; // Pastikan db.php sudah terhubung dengan benar

// Mengecek apakah ada data yang dikirim melalui URL atau form
if (isset($_GET['project_id'])) {
    $project_id = $_GET['project_id'];

    // Query untuk mengambil data project berdasarkan project_id
    $sql = "SELECT * FROM project_management WHERE ProjectID = :project_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['project_id' => $project_id]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$project) {
        echo "Project tidak ditemukan!";
        exit;
    }
}

// Jika form di-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    
    // Query untuk memperbarui status proyek
    $sql = "UPDATE project_management SET status = :status WHERE ProjectID = :project_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['status' => $status, 'project_id' => $project_id]);

    // Mengalihkan pengguna setelah update
    header("Location: dashboard.php"); // Arahkan kembali ke dashboard
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <title>Update Status - LMSMAGANG</title>
    <style>
        /* Tambahan CSS untuk judul, kontainer, dan footer */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }

        .update__section {
            padding: 80px 20px 40px;
            text-align: center;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 0 auto;
        }

        .update__section h2 {
            font-size: 2rem;
            color: #007bff;
            margin-bottom: 20px;
        }

        .update__form {
            display: flex;
            flex-direction: column;
            gap: 20px;
            max-width: 600px;
            margin: 0 auto;
        }

        .update__form select, .update__form button {
            padding: 12px;
            font-size: 1rem;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .update__form button {
            background-color: #007bff;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .update__form button:hover {
            background-color: #0056b3;
        }

        footer {
            background-color: #333;
            color: white;
            padding: 20px;
            text-align: center;
            margin-top: 40px;
        }

        @media (max-width: 768px) {
            .update__section {
                padding: 60px 15px;
            }

            .update__section h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav>
        <div class="nav__header">
            <div class="nav__logo">
                <a href="dashboard.php">LMS<span>MAGANG</span></a>
            </div>
            <div class="nav__menu__btn" id="menu-btn">
                <span><i class="ri-menu-line"></i></span>
            </div>
        </div>
        <ul class="nav__links" id="nav-links">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="update_status.php">Update Status</a></li>
            <li><a href="logout.php" class="login">Logout</a></li>
        </ul>
    </nav>

    <!-- Update Status Section -->
    <section class="update__section">
        <h2>Update Status Proyek</h2>
        <form class="update__form" method="POST" action="">
            <label for="status">Pilih Status:</label>
            <select name="status" id="status">
                <option value="Sedang Berjalan" <?= ($project['status'] == 'Sedang Berjalan') ? 'selected' : '' ?>>Sedang Berjalan</option>
                <option value="Menunggu Review" <?= ($project['status'] == 'Menunggu Review') ? 'selected' : '' ?>>Menunggu Review</option>
                <option value="Dalam Proses" <?= ($project['status'] == 'Dalam Proses') ? 'selected' : '' ?>>Dalam Proses</option>
            </select>

            <input type="hidden" name="project_id" value="<?= htmlspecialchars($project['ProjectID']) ?>">

            <button type="submit">Update Status</button>
        </form>
    </section>

    <!-- Footer -->
    <footer>
        <p>Copyright © 2024 DPD YOGYAKARTA. All Rights Reserved.</p>
    </footer>
</body>
</html>
