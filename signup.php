<?php
require 'db.php';
$registrationSuccess = false; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $username = trim($_POST['username']);
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $Password = password_hash(trim($_POST['Password']), PASSWORD_DEFAULT);

   
    if (empty($username) || empty($nama) || empty($email) || empty($Password)) {
        echo "<script>alert('All fields are required.');</script>";
    } else {
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            echo "<script>alert('Username or email already exists. Please choose another.');</script>";
        } else {
            
            $stmt = $pdo->prepare("INSERT INTO users ( username, nama, email, Password) VALUES ( ?, ?, ?, ?)");
            if ($stmt->execute([$username, $nama, $email, $Password])) {
                $registrationSuccess = true; 
            } else {
                echo "<script>alert('Registration failed. Please try again.');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Form</title>
    <link rel="stylesheet" href="css/signupstyles.css">
    <script>
        
        window.onload = function() {
            <?php if ($registrationSuccess): ?>
                alert('Registration successful. You can now log in.');
            <?php endif; ?>
        }
    </script>
</head>
<body>
    <div class="form-container">
        <h1 class="form-title">SIGN UP</h1>
        <form class="sign-up-form" method="POST" action="">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter your username" required>
            
            <label for="nama">Name</label>
            <input type="text" id="nama" name="nama" placeholder="Enter your name" required>
            
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
            
            <label for="Password">Password</label>
            <input type="Password" id="Password" name="Password" placeholder="Enter your Password" required>
            
            <button type="submit" class="register-button">Daftar</button>
        </form>
        <p class="sign-in-text">
            Sudah memiliki akun? <a href="signin.php" class="sign-in-link">Masuk di sini</a>
        </p>
    </div>
</body>
</html>