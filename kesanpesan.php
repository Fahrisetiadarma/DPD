<?php
require 'db.php'; // Mengimpor file koneksi database

// Query untuk mengambil data dari tabel kesan_dan_pesan
$sql = "SELECT name, message FROM kesan_dan_pesan"; 
$stmt = $pdo->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC); // Ambil semua data dalam bentuk array asosiatif
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="css/styles.css" />
    <title>Kesan dan Pesan - LMSMAGANG</title>
    <style>
      /* Tambahan CSS untuk judul, kontainer, dan footer */
      .kesan__section {
        padding: 100px 20px 40px; /* Tambahkan jarak atas lebih besar */
        text-align: center;
      }

      .kesan__section h2 {
        font-size: 2rem;
        color: #007bff;
        margin-bottom: 30px;
      }

      .container {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px; /* Jarak antar elemen kartu */
      }

      .kesan__card {
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin: 20px 0;
        max-width: 600px;
        padding: 20px;
        text-align: center;
        width: 90%;
      }

      .kesan__card h5 {
        font-size: 1.5rem;
        color: #007bff;
        margin-bottom: 15px;
        font-weight: bold;
      }

      .kesan__card p {
        font-size: 1.1rem;
        color: #555;
        line-height: 1.5;
      }

      footer {
        background-color: #333;
        color: white;
        padding: 20px 10px;
        text-align: center;
        margin-top: 40px;
      }

      @media (max-width: 768px) {
        .kesan__section {
          padding: 80px 10px 30px;
        }

        .kesan__section h2 {
          font-size: 1.8rem;
        }

        .kesan__card {
          padding: 15px;
        }

        .kesan__card h5 {
          font-size: 1.3rem;
        }

        .kesan__card p {
          font-size: 1rem;
        }
      }
    </style>
  </head>
  <body>
  <nav>
      <div class="nav__header">
        <div class="nav__logo">
          <a href="index.html">LMS<span>MAGANG</span></a>
        </div>
        <div class="nav__menu__btn" id="menu-btn">
          <span><i class="ri-menu-line"></i></span>
        </div>
      </div>
      <ul class="nav__links" id="nav-links">
        <li><a href="kesanpesan.php">Kesan dan Pesan</a></li>
        <li><a href="#">About Us</a></li>
        <li><a href="contact.html">Contact</a></li>
        <li><a href="signin.php" class="login">Login</a></li>
      </ul>
    </nav>
    
    <section class="kesan__section">
      <h2>Kesan dan Pesan</h2>
      <div class="container">
        <?php if ($results): ?>
          <?php foreach ($results as $row): ?>
            <div class="kesan__card">
              <h5><?= htmlspecialchars($row['name']) ?></h5>
              <p><?= htmlspecialchars($row['message']) ?></p>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-center">Belum ada data kesan dan pesan yang tersedia.</p>
        <?php endif; ?>
      </div>
    </section>

    <footer>
      <p>Copyright © 2024 DPD YOGYAKARTA. All Rights Reserved.</p>
    </footer>
  </body>
</html>
