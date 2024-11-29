<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: signin.php");
    exit();
}

$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'User';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ Section</title>
    <link rel="stylesheet" href="css/faq.css"> <!-- Link to the CSS file -->
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
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <p>You are logged in as <?php echo htmlspecialchars($role); ?></p>
            <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
        </header>
        <div class="content">
    <h1>Frequently Asked Questions (FAQ)</h1>
    <div class="faq-item">
        <div class="faq-question" onclick="toggleAnswer(1)">
            <h2>Apa itu DPD RI DI Yogyakarta?</h2>
        </div>
        <div class="faq-answer" id="answer-1">
            <p>DPD RI DI Yogyakarta adalah perwakilan daerah dari Dewan Perwakilan Daerah Republik Indonesia yang berfungsi untuk mewakili aspirasi masyarakat daerah dalam penyusunan undang-undang di tingkat nasional.</p>
        </div>
    </div>
    <div class="faq-item">
        <div class="faq-question" onclick="toggleAnswer(2)">
            <h2>Bagaimana cara bergabung dengan DPD RI DI Yogyakarta?</h2>
        </div>
        <div class="faq-answer" id="answer-2">
            <p>Untuk bergabung sebagai anggota DPD RI, calon anggota harus mengikuti proses pemilihan umum yang dilakukan setiap lima tahun sekali. DPD RI beranggotakan wakil-wakil dari tiap provinsi yang dipilih langsung oleh rakyat.</p>
        </div>
    </div>
    <div class="faq-item">
        <div class="faq-question" onclick="toggleAnswer(3)">
            <h2>Apa tugas utama DPD RI DI Yogyakarta?</h2>
        </div>
        <div class="faq-answer" id="answer-3">
            <p>Tugas utama DPD RI adalah mengawasi, memberikan masukan, dan menyetujui rancangan undang-undang yang berkaitan dengan daerah, serta memastikan kepentingan daerah terakomodasi dalam kebijakan nasional.</p>
        </div>
    </div>
    <div class="faq-item">
        <div class="faq-question" onclick="toggleAnswer(4)">
            <h2>Bagaimana DPD RI DI Yogyakarta berperan dalam pengambilan keputusan nasional?</h2>
        </div>
        <div class="faq-answer" id="answer-4">
            <p>DPD RI DI Yogyakarta berperan dengan memberikan pertimbangan atau pendapat mengenai setiap rancangan undang-undang yang berhubungan dengan daerah, serta dapat mengajukan rancangan undang-undang kepada DPR atau pemerintah.</p>
        </div>
    </div>
    <div class="faq-item">
        <div class="faq-question" onclick="toggleAnswer(5)">
            <h2>Apakah DPD RI DI Yogyakarta memiliki program khusus untuk masyarakat?</h2>
        </div>
        <div class="faq-answer" id="answer-5">
            <p>Ya, DPD RI DI Yogyakarta memiliki berbagai program yang berfokus pada pemberdayaan masyarakat, peningkatan kualitas pendidikan, kesehatan, serta pengembangan ekonomi daerah untuk kesejahteraan masyarakat.</p>
        </div>
    </div>
    <div class="faq-item">
        <div class="faq-question" onclick="toggleAnswer(6)">
            <h2>Bagaimana cara mengajukan pertanyaan atau masukan kepada DPD RI DI Yogyakarta?</h2>
        </div>
        <div class="faq-answer" id="answer-6">
            <p>Anda dapat mengajukan pertanyaan atau masukan melalui saluran komunikasi yang tersedia di website resmi DPD RI DIY Yogyakarta, atau melalui media sosial dan alamat email resmi yang sudah disediakan.</p>
        </div>
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

        // JavaScript to toggle the visibility of the FAQ answers
        function toggleAnswer(id) {
            var answer = document.getElementById('answer-' + id);
            if (answer.style.display === "block") {
                answer.style.display = "none";
            } else {
                answer.style.display = "block";
            }
        }
    </script>
</body>
</html>
