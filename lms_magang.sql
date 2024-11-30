-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 30, 2024 at 05:07 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lms_magang`
--

-- --------------------------------------------------------

--
-- Table structure for table `dpd_videos`
--

CREATE TABLE `dpd_videos` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `youtube_link` text NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dpd_videos`
--

INSERT INTO `dpd_videos` (`id`, `title`, `youtube_link`, `uploaded_at`) VALUES
(3, 'Hari jadi DPD RI DIY', 'https://www.youtube.com/embed/n9CG8-gVo_I', '2024-11-25 03:50:05');

-- --------------------------------------------------------

--
-- Table structure for table `kesan_dan_pesan`
--

CREATE TABLE `kesan_dan_pesan` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `message` text DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `jurusan` varchar(255) NOT NULL,
  `universitas` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kesan_dan_pesan`
--

INSERT INTO `kesan_dan_pesan` (`id`, `name`, `message`, `status`, `jurusan`, `universitas`) VALUES
(1, 'Gustiiin', 'semoga kedepannya lebih baik lagi', 'approved', '', ''),
(2, 'Fahri Setia Darma', 'joss', 'approved', 'Informatika', 'Universitas Ahmad Dahlan');

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_sharing`
--

CREATE TABLE `knowledge_sharing` (
  `SK_ID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `link_ppt` text DEFAULT NULL,
  `hari_tanggal` date DEFAULT NULL,
  `jam` varchar(250) NOT NULL,
  `nama` varchar(250) NOT NULL,
  `judul` varchar(250) NOT NULL,
  `link_video` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `knowledge_sharing`
--

INSERT INTO `knowledge_sharing` (`SK_ID`, `UserID`, `link_ppt`, `hari_tanggal`, `jam`, `nama`, `judul`, `link_video`) VALUES
(1, NULL, 'https://drive.google.com/drive/u/0/home', '2024-11-16', '10:17', 'fahri', 'sadsadadasd', '');

-- --------------------------------------------------------

--
-- Table structure for table `laporan_akhir`
--

CREATE TABLE `laporan_akhir` (
  `id` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `link` text NOT NULL,
  `tanggal` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laporan_akhir`
--

INSERT INTO `laporan_akhir` (`id`, `UserID`, `link`, `tanggal`) VALUES
(1, 8, 'https://docs.google.com/spreadsheets/d/1UfXUib43RTbI96GyZbjcUINO_4eNFJni/edit?usp=drive_link&ouid=118252220238230890427&rtpof=true&sd=true', '2024-11-28');

-- --------------------------------------------------------

--
-- Table structure for table `logbook`
--

CREATE TABLE `logbook` (
  `LogbookID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `link` varchar(250) NOT NULL,
  `tanggal` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logbook`
--

INSERT INTO `logbook` (`LogbookID`, `UserID`, `link`, `tanggal`) VALUES
(4, 12, 'https://www.spotify.com/id-id/download/windows/', '2024-11-26');

-- --------------------------------------------------------

--
-- Table structure for table `presensi`
--

CREATE TABLE `presensi` (
  `id` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `jenis_presensi` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `presensi`
--

INSERT INTO `presensi` (`id`, `UserID`, `tanggal`, `lokasi`, `jenis_presensi`) VALUES
(3, 12, '2024-11-28', '-7.5628544,110.8475904', 'Kedatangan'),
(4, 13, '2024-11-29', '-7.5430007,110.6475889', 'Kedatangan');

-- --------------------------------------------------------

--
-- Table structure for table `project_management`
--

CREATE TABLE `project_management` (
  `ProjectID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `JudulProject` varchar(255) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `Deadline` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_management`
--

INSERT INTO `project_management` (`ProjectID`, `UserID`, `JudulProject`, `Description`, `Deadline`) VALUES
(1, NULL, 'sasaa', 'asasasa', '2024-11-27'),
(2, NULL, 'sasaa', 'asasasa', '2024-11-27'),
(3, NULL, 'sasaa', 'asasasa', '2024-11-27'),
(4, NULL, 'uhhaushua', 'jsajoijaodjioas', '2024-12-02'),
(5, NULL, 'eaddad', 'adwadwadwadwa', '2024-11-26'),
(6, 13, 'Pembuatan Website', 'Pembuatan Website Lms', '2024-11-29'),
(10, 13, 'ASasAS', 'saSsA', '2024-11-30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `Username` varchar(255) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `Nama` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `jurusan` varchar(250) NOT NULL,
  `universitas` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `role`, `Username`, `Password`, `Nama`, `Email`, `jurusan`, `universitas`) VALUES
(8, 'Admin', 'gustin', '$2y$10$TpU8ZE0Kc2vKZlR9HxztueVdEQ1iaRLK8TMJyKpr79fgCPFJHFkyK', 'agustin', 'gustin@gmail.com', '', ''),
(9, 'Admin', 'fikrianatri', '$2y$10$1/6cchp/kmQN2wZ0R/WlFuSEs7Vt9HZCuecgimOnWppArxe8iegEa', 'Fikriana Tri Agustina', 'fikriana2100018170@webmail.uad.ac.id', '', ''),
(12, 'Magang', 'paiman', '$2y$10$eNtfhlgzknIb3r6/GspgZOwSSaVD2Pzc7IYdUgwSPiO9JuO4Yio5C', 'ikhsan', 'Ikhsan2100018023@webmail.uad.ac.id', 'informatika', 'universitas ahmad dahlan'),
(13, 'Magang', 'fhrdrma', '$2y$10$t0517JH0jty9O2uLFIbn0OoFozBIDXLB1MksZ/oEPdBXfPDvWxRQy', 'FAHRI SETIA DARMA', 'fahrisetdarma@gmail.com', 'Informatika', 'Universitas Ahmad Dahlan');

-- --------------------------------------------------------

--
-- Table structure for table `user_management`
--

CREATE TABLE `user_management` (
  `AdminID` int(11) DEFAULT NULL,
  `UserID` int(11) DEFAULT NULL,
  `Permissions` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `video_gallery`
--

CREATE TABLE `video_gallery` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `youtube_link` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `video_gallery`
--

INSERT INTO `video_gallery` (`id`, `title`, `youtube_link`) VALUES
(4, 'HARI JADI DPD', 'https://www.youtube.com/embed/2fzvPsHQSUw');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dpd_videos`
--
ALTER TABLE `dpd_videos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kesan_dan_pesan`
--
ALTER TABLE `kesan_dan_pesan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `knowledge_sharing`
--
ALTER TABLE `knowledge_sharing`
  ADD PRIMARY KEY (`SK_ID`),
  ADD UNIQUE KEY `UserID_2` (`UserID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `laporan_akhir`
--
ALTER TABLE `laporan_akhir`
  ADD PRIMARY KEY (`id`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `logbook`
--
ALTER TABLE `logbook`
  ADD PRIMARY KEY (`LogbookID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `presensi`
--
ALTER TABLE `presensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `project_management`
--
ALTER TABLE `project_management`
  ADD PRIMARY KEY (`ProjectID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`);

--
-- Indexes for table `user_management`
--
ALTER TABLE `user_management`
  ADD KEY `AdminID` (`AdminID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `video_gallery`
--
ALTER TABLE `video_gallery`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dpd_videos`
--
ALTER TABLE `dpd_videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kesan_dan_pesan`
--
ALTER TABLE `kesan_dan_pesan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `knowledge_sharing`
--
ALTER TABLE `knowledge_sharing`
  MODIFY `SK_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `laporan_akhir`
--
ALTER TABLE `laporan_akhir`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `logbook`
--
ALTER TABLE `logbook`
  MODIFY `LogbookID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `presensi`
--
ALTER TABLE `presensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `project_management`
--
ALTER TABLE `project_management`
  MODIFY `ProjectID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `video_gallery`
--
ALTER TABLE `video_gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `knowledge_sharing`
--
ALTER TABLE `knowledge_sharing`
  ADD CONSTRAINT `knowledge_sharing_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `laporan_akhir`
--
ALTER TABLE `laporan_akhir`
  ADD CONSTRAINT `laporan_akhir_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `logbook`
--
ALTER TABLE `logbook`
  ADD CONSTRAINT `logbook_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `presensi`
--
ALTER TABLE `presensi`
  ADD CONSTRAINT `presensi_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `project_management`
--
ALTER TABLE `project_management`
  ADD CONSTRAINT `project_management_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `user_management`
--
ALTER TABLE `user_management`
  ADD CONSTRAINT `user_management_ibfk_1` FOREIGN KEY (`AdminID`) REFERENCES `users` (`UserID`),
  ADD CONSTRAINT `user_management_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
