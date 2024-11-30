<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: signin.php");
    exit();
}

$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'User';

// Sertakan koneksi database
require_once 'db.php';

// Menangani form input FAQ
if (isset($_POST['add_faq'])) {
    $question = $_POST['question'];
    $answer = $_POST['answer'];

    try {
        // Menyimpan data ke dalam database menggunakan PDO
        $stmt = $pdo->prepare("INSERT INTO faq (question, answer) VALUES (:question, :answer)");
        $stmt->bindParam(':question', $question);
        $stmt->bindParam(':answer', $answer);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Menangani pengeditan FAQ
if (isset($_POST['edit_faq'])) {
    $faq_id = $_POST['faq_id'];
    $question = $_POST['question'];
    $answer = $_POST['answer'];

    try {
        // Update data FAQ di database
        $stmt = $pdo->prepare("UPDATE faq SET question = :question, answer = :answer WHERE id = :id");
        $stmt->bindParam(':id', $faq_id);
        $stmt->bindParam(':question', $question);
        $stmt->bindParam(':answer', $answer);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Menangani penghapusan FAQ
if (isset($_GET['delete_faq'])) {
    $faq_id = $_GET['delete_faq'];

    try {
        // Menghapus FAQ dari database
        $stmt = $pdo->prepare("DELETE FROM faq WHERE id = :id");
        $stmt->bindParam(':id', $faq_id);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Menampilkan FAQ dari database
try {
    $stmt = $pdo->query("SELECT * FROM faq");
    $faqItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ Section</title>
    <link rel="stylesheet" href="css/faq.css">
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
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <p>You are logged in as <?php echo htmlspecialchars($role); ?></p>
            <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
        </header>

        <div class="content">
            <h1>Frequently Asked Questions (FAQ)</h1>

            <!-- FAQ Items -->
            <?php foreach ($faqItems as $row): ?>
            <div class="faq-item">
                <div class="faq-question" onclick="toggleAnswer(<?php echo $row['id']; ?>)">
                    <h2><?php echo htmlspecialchars($row['question']); ?></h2>
                </div>
                <div class="faq-answer" id="answer-<?php echo $row['id']; ?>">
                    <p><?php echo htmlspecialchars($row['answer']); ?></p>
                </div>

                <!-- Tombol Edit dan Hapus untuk Admin/Pembimbing -->
                <?php if ($role === 'Admin' || $role === 'Pembimbing'): ?>
                <div class="faq-actions">
                    <button onclick="editFAQ(<?php echo $row['id']; ?>)">Edit</button>
                    <a href="?delete_faq=<?php echo $row['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus FAQ ini?')">Delete</a>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>

            <!-- Form untuk Admin dan Pembimbing untuk menambah FAQ -->
            <?php if ($role === 'Admin' || $role === 'Pembimbing'): ?>
            <h2>Tambah FAQ</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="question">Pertanyaan:</label>
                    <textarea name="question" id="question" required></textarea>
                </div>
                <div class="form-group">
                    <label for="answer">Jawaban:</label>
                    <textarea name="answer" id="answer" required></textarea>
                </div>
                <button type="submit" name="add_faq">Tambah FAQ</button>
            </form>
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

        function toggleAnswer(id) {
            var answer = document.getElementById('answer-' + id);
            if (answer.style.display === "block") {
                answer.style.display = "none";
            } else {
                answer.style.display = "block";
            }
        }

        function editFAQ(id) {
            var question = prompt("Edit pertanyaan FAQ:");
            var answer = prompt("Edit jawaban FAQ:");

            if (question !== null && answer !== null) {
                // Kirim data edit melalui POST
                var form = document.createElement("form");
                form.method = "POST";
                form.action = "";

                var faq_id = document.createElement("input");
                faq_id.type = "hidden";
                faq_id.name = "faq_id";
                faq_id.value = id;

                var faq_question = document.createElement("input");
                faq_question.type = "hidden";
                faq_question.name = "question";
                faq_question.value = question;

                var faq_answer = document.createElement("input");
                faq_answer.type = "hidden";
                faq_answer.name = "answer";
                faq_answer.value = answer;

                form.appendChild(faq_id);
                form.appendChild(faq_question);
                form.appendChild(faq_answer);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
