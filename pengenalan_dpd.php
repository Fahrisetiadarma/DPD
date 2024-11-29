<?php
include 'db.php'; // Pastikan file ini berisi koneksi PDO
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: signin.php");
    exit();
}

$role = $_SESSION['role'] ?? 'User';

// Fungsi untuk mengubah URL YouTube menjadi format embed
function convertToEmbed($url) {
    // Cek apakah URL mengandung youtube.com atau youtu.be
    if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
        return 'https://www.youtube.com/embed/' . $matches[1];
    } elseif (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
        return 'https://www.youtube.com/embed/' . $matches[1];
    }
    return $url; // Jika bukan URL YouTube, kembalikan URL yang sama
}

// Handle submit form untuk menambahkan video
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_video'])) {
        $title = $_POST['title'];
        $youtube_link = $_POST['youtube_link'];

        // Ubah URL YouTube menjadi format embed
        $embed_link = convertToEmbed($youtube_link);

        $stmt = $pdo->prepare("INSERT INTO video_gallery (title, youtube_link) VALUES (:title, :youtube_link)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':youtube_link', $embed_link);
        $stmt->execute();
    } elseif (isset($_POST['edit_video'])) {
        $video_id = $_POST['video_id'];
        $new_title = $_POST['new_title'];
        $new_youtube_link = $_POST['new_youtube_link'];

        // Ubah URL YouTube menjadi format embed
        $embed_link = convertToEmbed($new_youtube_link);

        $stmt = $pdo->prepare("UPDATE video_gallery SET title = :title, youtube_link = :youtube_link WHERE id = :id");
        $stmt->bindParam(':title', $new_title);
        $stmt->bindParam(':youtube_link', $embed_link);
        $stmt->bindParam(':id', $video_id, PDO::PARAM_INT);
        $stmt->execute();
    } elseif (isset($_POST['delete_video'])) {
        $video_id = $_POST['video_id'];

        $stmt = $pdo->prepare("DELETE FROM video_gallery WHERE id = :id");
        $stmt->bindParam(':id', $video_id, PDO::PARAM_INT);
        $stmt->execute();
    }
}

// Ambil data video dari database
$stmt = $pdo->query("SELECT * FROM video_gallery ORDER BY id DESC");
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Video Pengenalan DPD RI</title>
    <link rel="stylesheet" href="css/projectmanagement.css">
    <style>
        /* Styling untuk tombol */
        .btn-orange, .btn-red {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-align: center;
        }

        .btn-orange {
            background-color: #ff6600;
            color: white;
        }

        .btn-orange:hover {
            background-color: #e65c00;
        }

        .btn-red {
            background-color: #e60000;
            color: white;
        }

        .btn-red:hover {
            background-color: #cc0000;
        }

        /* Styling untuk kontainer video item */
        .video-item {
            margin-bottom: 20px;
            padding: 20px;
            background-color: #f4f4f4;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .video-item h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .video-gallery {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        iframe {
            width: 100%;
            height: 315px;
            border-radius: 10px;
        }

        /* Flexbox untuk tombol edit dan hapus agar sejajar */
        .video-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-start;
            margin-top: 10px;
        }

        /* Form kontrol input */
        form input[type="text"] {
            padding: 10px;
            margin: 5px;
            width: 200px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 10px;
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
        </header>
        <div class="content">
            <h1>Galeri Video Pengenalan DPD RI</h1>

            <?php if ($role === 'Admin' || $role === 'Pembimbing'): ?>
                <form method="post">
                    <input type="text" name="title" placeholder="Judul Video" required>
                    <input type="text" name="youtube_link" placeholder="Link Youtube Embed" required>
                    <button type="submit" name="add_video" class="btn-orange">Tambah Video</button>
                </form>
            <?php endif; ?>

            <h2>Daftar Video Pengenalan DPD RI</h2>
            <div class="video-gallery">
                <?php foreach ($videos as $video): ?>
                    <div class="video-item">
                        <h3><?php echo htmlspecialchars($video['title']); ?></h3>
                        <iframe width="560" height="315" src="<?php echo htmlspecialchars($video['youtube_link']); ?>" 
                                title="<?php echo htmlspecialchars($video['title']); ?>" frameborder="0" allowfullscreen>
                        </iframe>

                        <?php if ($role === 'Admin' || $role === 'Pembimbing'): ?>
                            <div class="video-actions">
                                <!-- Form Edit Video -->
                                <form method="post">
                                    <input type="hidden" name="video_id" value="<?php echo $video['id']; ?>">
                                    <input type="text" name="new_title" value="<?php echo htmlspecialchars($video['title']); ?>" required>
                                    <input type="text" name="new_youtube_link" value="<?php echo htmlspecialchars($video['youtube_link']); ?>" required>
                                    <button type="submit" name="edit_video" class="btn-orange">Edit Video</button>
                                </form>
                                 
                                <!-- Form Hapus Video -->
                                <form method="post" style="display:inline-block;">
                                    <input type="hidden" name="video_id" value="<?php echo $video['id']; ?>">
                                    <button type="submit" name="delete_video" class="btn-red">Hapus Video</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>
