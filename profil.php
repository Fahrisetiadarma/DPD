<?php
session_start();

// Cek Login
if (!isset($_SESSION['username'])) {
    header("Location: signin.php");
    exit();
}

include('db.php');
$username = $_SESSION['username'];
$role = $_SESSION['role'] ?? 'User'; // Default role jika tidak ada di session

// Ambil data pengguna dari database
$query = "SELECT * FROM users WHERE username = :username";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['error_message'] = "Pengguna tidak ditemukan.";
    header("Location: signin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_nama = trim($_POST['nama']);
    $new_username = trim($_POST['username']);
    $new_email = trim($_POST['email']);
    $new_password = trim($_POST['password']);
    $new_jurusan = trim($_POST['jurusan'] ?? ''); // Hanya akan ada jika role adalah magang
    $new_universitas = trim($_POST['universitas'] ?? ''); // Sama seperti jurusan

    // Update data pengguna di database
    if (!empty($new_nama) && !empty($new_username) && !empty($new_email)) {
        $update_query = "UPDATE users SET nama = :nama, username = :username, email = :email" .
                        (!empty($new_password) ? ", password = :password" : "") .
                        (strtolower($role) === 'magang' ? ", jurusan = :jurusan, universitas = :universitas" : "") .
                        " WHERE username = :old_username";

        $update_stmt = $pdo->prepare($update_query);
        $update_stmt->bindParam(':nama', $new_nama, PDO::PARAM_STR);
        $update_stmt->bindParam(':username', $new_username, PDO::PARAM_STR);
        $update_stmt->bindParam(':email', $new_email, PDO::PARAM_STR);
        $update_stmt->bindParam(':old_username', $username, PDO::PARAM_STR);

        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
        }

        if (strtolower($role) === 'magang') {
            $update_stmt->bindParam(':jurusan', $new_jurusan, PDO::PARAM_STR);
            $update_stmt->bindParam(':universitas', $new_universitas, PDO::PARAM_STR);
        }

        if ($update_stmt->execute()) {
            $_SESSION['success_message'] = "Data profil berhasil diperbarui.";
            $_SESSION['username'] = $new_username;
        } else {
            $_SESSION['error_message'] = "Gagal memperbarui data profil.";
        }
    } else {
        $_SESSION['error_message'] = "Semua field harus diisi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna</title>
    <link rel="stylesheet" href="css/projectmanagement.css">
    <style>
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .btn-submit {
            padding: 10px 20px;
            background-color: #FFA500;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-submit:hover {
            background-color: #FF8C00;
        }

        .error-message {
            color: red;
            font-weight: bold;
        }

        .success-message {
            color: green;
            font-weight: bold;
        }
    </style>
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
            <li><a href="profil.php">Profil</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content" id="main-content">
        <header>
            <h1>Selamat datang, <?php echo htmlspecialchars($username); ?>!</h1>
        </header>

        <div class="content">
            <h1>Profil Pengguna</h1>

            <?php if (isset($_SESSION['success_message'])): ?>
                <p class="success-message"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <p class="error-message"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></p>
            <?php endif; ?>

            <form method="POST" action="profil.php">
                <div class="form-group">
                    <label for="nama">Nama</label>
                    <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($user['nama'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="password">Password Baru</label>
                    <input type="password" id="password" name="password">
                </div>
                
                <!-- Kolom Jurusan dan Universitas untuk Magang -->
                <?php if (strtolower($role) === 'magang'): ?>
                    <div class="form-group">
                        <label for="jurusan">Jurusan</label>
                        <input type="text" id="jurusan" name="jurusan" value="<?php echo htmlspecialchars($user['jurusan'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="universitas">Universitas</label>
                        <input type="text" id="universitas" name="universitas" value="<?php echo htmlspecialchars($user['universitas'] ?? ''); ?>">
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <button type="submit" class="btn-submit">Perbarui Profil</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
