<?php
require 'db.php';
session_start();

// Cek apakah sesi sudah ada, jika iya maka arahkan ke dashboard
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['Password']) ? trim($_POST['Password']) : '';

    if (empty($username) || empty($password)) {
        $error = "Username dan password harus diisi.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['Password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $user['role'];
            echo "<script>alert('Login berhasil!'); window.location.href='dashboard.php';</script>";
            exit();
        } else {
            $error = "Username atau password salah.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/signinstyles.css">
    <style>
        .password-container {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
        }
        .toggle-password i {
            font-size: 1.2rem;
            color: #333;
        }
        .back-button {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            background-color: #FFA500; /* Warna dominan oranye */
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .back-button i {
            margin-right: 8px;
            font-size: 18px;
        }
        .back-button:hover {
            background-color: #FF8C00; /* Oranye lebih gelap saat hover */
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet" />
</head>
<body>
    <div class="form-container">
        <h1 class="form-title">LOGIN</h1>
        <form class="sign-up-form" method="POST" action="">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Masukkan username Anda" required>

            <label for="Password">Password</label>
            <div class="password-container">
                <input type="password" id="Password" name="Password" placeholder="Masukkan Password Anda" required>
                <span class="toggle-password" onclick="togglePasswordVisibility()">
                    <i class="ri-eye-off-line" id="toggle-icon"></i>
                </span>
            </div>
            
            <button type="submit" class="register-button">Masuk</button>
        </form>

        <?php if (!empty($error)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <p class="sign-in-text">
            Belum memiliki akun? <a href="signup.php" class="sign-in-link">Daftar di sini</a>
        </p>

        <!-- Tombol kembali ke index.html -->
        <a href="index.html" class="back-button">
            <i class="ri-arrow-left-line"></i> Kembali ke Halaman Utama
        </a>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('Password');
            const toggleIcon = document.getElementById('toggle-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('ri-eye-off-line');
                toggleIcon.classList.add('ri-eye-line');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('ri-eye-line');
                toggleIcon.classList.add('ri-eye-off-line');
            }
        }
    </script>
</body>
</html>
