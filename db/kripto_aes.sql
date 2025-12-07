-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 13 Jul 2025 pada 15.20
-- Versi server: 10.4.27-MariaDB
-- Versi PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kripto_aes`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `dokumen_terenkripsi`
--

CREATE TABLE `dokumen_terenkripsi` (
  `id_dokumen` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `nama_asli_file` varchar(255) NOT NULL,
  `nama_file_enkripsi` varchar(255) NOT NULL,
  `path_file_enkripsi` varchar(255) NOT NULL,
  `ukuran_file_kb` decimal(10,2) NOT NULL,
  `ukuran_file_enkripsi_kb` decimal(10,2) DEFAULT NULL,
  `kunci_enkripsi` varchar(16) NOT NULL,
  `algoritma_enkripsi` varchar(50) DEFAULT NULL,
  `tanggal_unggah` datetime NOT NULL,
  `status_proses` varchar(20) NOT NULL,
  `deskripsi_file` text DEFAULT NULL,
  `durasi_enkripsi_detik` decimal(10,2) DEFAULT NULL,
  `durasi_dekripsi_detik` decimal(10,2) DEFAULT NULL,
  `nama_file_terdekripsi` varchar(255) DEFAULT NULL,
  `ukuran_file_terdekripsi_kb` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `dokumen_terenkripsi`
--

INSERT INTO `dokumen_terenkripsi` (`id_dokumen`, `username`, `nama_asli_file`, `nama_file_enkripsi`, `path_file_enkripsi`, `ukuran_file_kb`, `ukuran_file_enkripsi_kb`, `kunci_enkripsi`, `algoritma_enkripsi`, `tanggal_unggah`, `status_proses`, `deskripsi_file`, `durasi_enkripsi_detik`, `durasi_dekripsi_detik`, `nama_file_terdekripsi`, `ukuran_file_terdekripsi_kb`) VALUES
(30, 'sultan', 'Logo_JRP_Insurance.png', '1752323164_6872545c12239_AES_Logo_JRP_Insurance.png.enc', '../hasil/Terenkripsi/1752323164_6872545c12239_AES_Logo_JRP_Insurance.png.enc', '180.31', '180.33', 'cee1a3783cef4677', 'AES-128', '2025-07-12 19:26:04', 'Terenkripsi', 'ini gambar logo perusahaan', '1.09', NULL, NULL, NULL),
(31, 'sultan', 'Logo_JRP_Insurance.png', '1752323211_6872548b7dfe5_DES_Logo_JRP_Insurance.png.enc', '../hasil/Terenkripsi/1752323211_6872548b7dfe5_DES_Logo_JRP_Insurance.png.enc', '180.31', '180.32', 'cee1a378', 'DES-64', '2025-07-12 19:26:51', 'Terenkripsi', 'gambar logo PT', '3.40', NULL, NULL, NULL),
(32, 'sultan', 'profil-asuransi-pt-jasa-raharja-putera.docx', '1752323285_687254d50ddfa_AES_profil-asuransi-pt-jasa-raharja-putera.docx.enc', '../hasil/Terenkripsi/1752323285_687254d50ddfa_AES_profil-asuransi-pt-jasa-raharja-putera.docx.enc', '55.55', '55.56', 'cee1a3783cef4677', 'AES-128', '2025-07-12 19:28:05', 'Terenkripsi', 'file profile perusahaan', '0.34', NULL, NULL, NULL),
(33, 'sultan', 'profil-asuransi-pt-jasa-raharja-putera.docx', '1752323316_687254f4b7942_DES_profil-asuransi-pt-jasa-raharja-putera.docx.enc', '../hasil/Terenkripsi/1752323316_687254f4b7942_DES_profil-asuransi-pt-jasa-raharja-putera.docx.enc', '55.55', '55.55', 'cee1a378', 'DES-64', '2025-07-12 19:28:36', 'Terenkripsi', 'profil PT Jasa Raharja Putera', '1.08', NULL, NULL, NULL),
(34, 'sultan', '(Short Version) Annual Report 2024 PT Jasa Raharja Putera.pdf', '1752323400_6872554897d7e_AES__Short_Version__Annual_Report_2024_PT_Jasa_Raharja_Putera.pdf.enc', '../hasil/Terenkripsi/1752323400_6872554897d7e_AES__Short_Version__Annual_Report_2024_PT_Jasa_Raharja_Putera.pdf.enc', '3337.09', '3337.11', 'cee1a3783cef4677', 'AES-128', '2025-07-12 19:30:00', 'Terenkripsi', 'file laporan tahunan', '20.01', NULL, NULL, NULL),
(36, 'sultan', '(Short Version) Annual Report 2024 PT Jasa Raharja Putera.pdf', '1752323603_6872561321522_AES__Short_Version__Annual_Report_2024_PT_Jasa_Raharja_Putera.pdf.enc', '../hasil/Terenkripsi/1752323603_6872561321522_AES__Short_Version__Annual_Report_2024_PT_Jasa_Raharja_Putera.pdf.enc', '3337.09', '3337.11', 'cee1a3783cef4677', 'AES-128', '2025-07-12 19:33:23', 'Terenkripsi', 'file annual report', '20.27', NULL, NULL, NULL),
(37, 'sultan', '(Short Version) Annual Report 2024 PT Jasa Raharja Putera.pdf', '1752323831_687256f7ef58d_AES__Short_Version__Annual_Report_2024_PT_Jasa_Raharja_Putera.pdf.enc', '../hasil/Terenkripsi/1752323831_687256f7ef58d_AES__Short_Version__Annual_Report_2024_PT_Jasa_Raharja_Putera.pdf.enc', '3337.09', '3337.11', 'cee1a3783cef4677', 'AES-128', '2025-07-12 19:37:11', 'Terdekripsi', '', '20.23', '41.98', '1752324891_68725b1ba46e8_AES_(Short Version) Annual Report 2024 PT Jasa Raharja Putera.pdf', '3337.09'),
(38, 'sultan', '(Short Version) Annual Report 2024 PT Jasa Raharja Putera.pdf', '1752323945_68725769a7d31_DES__Short_Version__Annual_Report_2024_PT_Jasa_Raharja_Putera.pdf.enc', '../hasil/Terenkripsi/1752323945_68725769a7d31_DES__Short_Version__Annual_Report_2024_PT_Jasa_Raharja_Putera.pdf.enc', '3337.09', '3337.10', 'cee1a378', 'DES-64', '2025-07-12 19:39:05', 'Terenkripsi', 'file annual report perusahaan', '63.18', NULL, NULL, NULL),
(39, 'sultan', '(Short Version) Annual Report 2024 PT Jasa Raharja Putera.pdf', '1752324155_6872583b75ef5_DES__Short_Version__Annual_Report_2024_PT_Jasa_Raharja_Putera.pdf.enc', '../hasil/Terenkripsi/1752324155_6872583b75ef5_DES__Short_Version__Annual_Report_2024_PT_Jasa_Raharja_Putera.pdf.enc', '3337.09', '3337.10', 'cee1a378', 'DES-64', '2025-07-12 19:42:35', 'Terdekripsi', '', '67.46', '63.05', '1752324674_68725a427e98a_DES_(Short Version) Annual Report 2024 PT Jasa Raharja Putera.pdf', '3337.09'),
(40, 'sultan', 'Data_Klaim_Jasa_Raharja_Putera.xlsx', '1752324478_6872597e08957_AES_Data_Klaim_Jasa_Raharja_Putera.xlsx.enc', '../hasil/Terenkripsi/1752324478_6872597e08957_AES_Data_Klaim_Jasa_Raharja_Putera.xlsx.enc', '13.95', '13.97', 'cee1a3783cef4677', 'AES-128', '2025-07-12 19:47:58', 'Terenkripsi', 'data klaim customer', '0.09', NULL, NULL, NULL),
(41, 'sultan', 'Data_Klaim_Jasa_Raharja_Putera.xlsx', '1752324632_68725a181ee5a_DES_Data_Klaim_Jasa_Raharja_Putera.xlsx.enc', '../hasil/Terenkripsi/1752324632_68725a181ee5a_DES_Data_Klaim_Jasa_Raharja_Putera.xlsx.enc', '13.95', '13.96', 'cee1a378', 'DES-64', '2025-07-12 19:50:32', 'Terenkripsi', 'File klaim asuransi', '0.26', NULL, NULL, NULL),
(42, 'sultan', 'Data_Klaim_Jasa_Raharja_Putera.xlsx', '1752325060_68725bc461af5_AES_Data_Klaim_Jasa_Raharja_Putera.xlsx.enc', '../hasil/Terenkripsi/1752325060_68725bc461af5_AES_Data_Klaim_Jasa_Raharja_Putera.xlsx.enc', '13.95', '13.97', '202cb962ac59075b', 'AES-128', '2025-07-12 19:57:40', 'Terdekripsi', '', '0.09', '0.18', '1752325073_68725bd118645_AES_Data_Klaim_Jasa_Raharja_Putera.xlsx', '13.95'),
(43, 'sultan', 'Data_Klaim_Jasa_Raharja_Putera.xlsx', '1752325133_68725c0d2c151_DES_Data_Klaim_Jasa_Raharja_Putera.xlsx.enc', '../hasil/Terenkripsi/1752325133_68725c0d2c151_DES_Data_Klaim_Jasa_Raharja_Putera.xlsx.enc', '13.95', '13.96', '202cb962', 'DES-64', '2025-07-12 19:58:53', 'Terdekripsi', '', '0.27', '0.26', '1752325164_68725c2c5a1d9_DES_Data_Klaim_Jasa_Raharja_Putera.xlsx', '13.95'),
(44, 'sultan', 'profil-asuransi-pt-jasa-raharja-putera.docx', '1752325409_68725d21b2678_AES_profil-asuransi-pt-jasa-raharja-putera.docx.enc', '../hasil/Terenkripsi/1752325409_68725d21b2678_AES_profil-asuransi-pt-jasa-raharja-putera.docx.enc', '55.55', '55.56', 'cee1a3783cef4677', 'AES-128', '2025-07-12 20:03:29', 'Terdekripsi', '', '0.34', '0.76', '1752325452_68725d4cb9b8b_AES_profil-asuransi-pt-jasa-raharja-putera.docx', '55.55'),
(45, 'sultan', 'profil-asuransi-pt-jasa-raharja-putera.docx', '1752325629_68725dfde3924_DES_profil-asuransi-pt-jasa-raharja-putera.docx.enc', '../hasil/Terenkripsi/1752325629_68725dfde3924_DES_profil-asuransi-pt-jasa-raharja-putera.docx.enc', '55.55', '55.55', '202cb962', 'DES-64', '2025-07-12 20:07:09', 'Terdekripsi', '', '1.05', '1.06', '1752325737_68725e690644c_DES_profil-asuransi-pt-jasa-raharja-putera.docx', '55.55'),
(46, 'sultan', 'Logo_JRP_Insurance.png', '1752325813_68725eb5548a9_DES_Logo_JRP_Insurance.png.enc', '../hasil/Terenkripsi/1752325813_68725eb5548a9_DES_Logo_JRP_Insurance.png.enc', '180.31', '180.32', 'c4ca4238', 'DES-64', '2025-07-12 20:10:13', 'Terdekripsi', '', '3.37', '4.05', '1752326047_68725f9f3c24f_DES_Logo_JRP_Insurance.png', '180.31'),
(50, 'sultan', 'Logo_JRP_Insurance.png', '1752327702_6872661631d24_AES_Logo_JRP_Insurance.png.enc', '../hasil/Terenkripsi/1752327702_6872661631d24_AES_Logo_JRP_Insurance.png.enc', '180.31', '180.33', '202cb962ac59075b', 'AES-128', '2025-07-12 20:41:42', 'Terenkripsi', '', '1.14', NULL, NULL, NULL),
(53, 'sultan', 'MARS budi Luhur.mp4', '1752410270_6873a89e7b353_AES_MARS_budi_Luhur.mp4.enc', '../hasil/Terenkripsi/1752410270_6873a89e7b353_AES_MARS_budi_Luhur.mp4.enc', '7444.17', '7444.19', 'cee1a3783cef4677', 'AES-128', '2025-07-13 19:37:50', 'Terenkripsi', '', '47.72', NULL, NULL, NULL),
(54, 'sultan', 'MARS budi Luhur.mp4', '1752410446_6873a94e27281_DES_MARS_budi_Luhur.mp4.enc', '../hasil/Terenkripsi/1752410446_6873a94e27281_DES_MARS_budi_Luhur.mp4.enc', '7444.17', '7444.19', 'cee1a378', 'DES-64', '2025-07-13 19:40:46', 'Terenkripsi', '', '150.34', NULL, NULL, NULL),
(55, 'sultan', 'MARS Universitas Budi Luhur.mp3', '1752410654_6873aa1e4c20e_AES_MARS_Universitas_Budi_Luhur.mp3.enc', '../hasil/Terenkripsi/1752410654_6873aa1e4c20e_AES_MARS_Universitas_Budi_Luhur.mp3.enc', '1546.92', '1546.94', 'cee1a3783cef4677', 'AES-128', '2025-07-13 19:44:14', 'Terenkripsi', '', '10.49', NULL, NULL, NULL),
(56, 'sultan', 'MARS Universitas Budi Luhur.mp3', '1752410683_6873aa3ba8d76_DES_MARS_Universitas_Budi_Luhur.mp3.enc', '../hasil/Terenkripsi/1752410683_6873aa3ba8d76_DES_MARS_Universitas_Budi_Luhur.mp3.enc', '1546.92', '1546.93', 'cee1a378', 'DES-64', '2025-07-13 19:44:43', 'Terenkripsi', '', '38.78', NULL, NULL, NULL),
(57, 'sultan', 'MARS Universitas Budi Luhur.mp3', '1752411076_6873abc4e4a04_AES_MARS_Universitas_Budi_Luhur.mp3.enc', '../hasil/Terenkripsi/1752411076_6873abc4e4a04_AES_MARS_Universitas_Budi_Luhur.mp3.enc', '1546.92', '1546.94', '202cb962ac59075b', 'AES-128', '2025-07-13 19:51:16', 'Terdekripsi', '', '9.77', '20.72', '1752411209_6873ac4920373_AES_MARS Universitas Budi Luhur.mp3', '1546.92'),
(58, 'sultan', 'MARS Universitas Budi Luhur.mp3', '1752411099_6873abdb68213_DES_MARS_Universitas_Budi_Luhur.mp3.enc', '../hasil/Terenkripsi/1752411099_6873abdb68213_DES_MARS_Universitas_Budi_Luhur.mp3.enc', '1546.92', '1546.93', '202cb962', 'DES-64', '2025-07-13 19:51:39', 'Terdekripsi', '', '30.73', '30.83', '1752411149_6873ac0dac1c1_DES_MARS Universitas Budi Luhur.mp3', '1546.92'),
(59, 'sultan', 'MARS budi Luhur.mp4', '1752411325_6873acbd3438a_AES_MARS_budi_Luhur.mp4.enc', '../hasil/Terenkripsi/1752411325_6873acbd3438a_AES_MARS_budi_Luhur.mp4.enc', '7444.17', '7444.19', '202cb962ac59075b', 'AES-128', '2025-07-13 19:55:25', 'Terdekripsi', '', '47.29', '97.73', '1752411587_6873adc3183a5_AES_MARS budi Luhur.mp4', '7444.17'),
(60, 'sultan', 'MARS budi Luhur.mp4', '1752411395_6873ad03db305_DES_MARS_budi_Luhur.mp4.enc', '../hasil/Terenkripsi/1752411395_6873ad03db305_DES_MARS_budi_Luhur.mp4.enc', '7444.17', '7444.19', '202cb962', 'DES-64', '2025-07-13 19:56:35', 'Terdekripsi', '', '147.97', '146.96', '1752411871_6873aedfd6b80_DES_MARS budi Luhur.mp4', '7444.17'),
(61, 'sultan', 'indonesia.gif', '1752412096_6873afc076647_AES_indonesia.gif.enc', '../hasil/Terenkripsi/1752412096_6873afc076647_AES_indonesia.gif.enc', '3374.18', '3374.19', 'cee1a3783cef4677', 'AES-128', '2025-07-13 20:08:16', 'Terenkripsi', '', '20.72', NULL, NULL, NULL),
(62, 'sultan', 'indonesia.gif', '1752412151_6873aff72c4da_DES_indonesia.gif.enc', '../hasil/Terenkripsi/1752412151_6873aff72c4da_DES_indonesia.gif.enc', '3374.18', '3374.19', 'cee1a378', 'DES-64', '2025-07-13 20:09:11', 'Terenkripsi', '', '64.63', NULL, NULL, NULL),
(63, 'sultan', 'indonesia.gif', '1752412311_6873b09781311_AES_indonesia.gif.enc', '../hasil/Terenkripsi/1752412311_6873b09781311_AES_indonesia.gif.enc', '3374.18', '3374.19', '202cb962ac59075b', 'AES-128', '2025-07-13 20:11:51', 'Terdekripsi', '', '20.46', '43.47', '1752412570_6873b19a5a5c4_AES_indonesia.gif', '3374.18'),
(64, 'sultan', 'indonesia.gif', '1752412368_6873b0d065bfc_DES_indonesia.gif.enc', '../hasil/Terenkripsi/1752412368_6873b0d065bfc_DES_indonesia.gif.enc', '3374.18', '3374.19', '202cb962', 'DES-64', '2025-07-13 20:12:48', 'Terdekripsi', '', '64.08', '63.96', '1752412467_6873b1336f75c_DES_indonesia.gif', '3374.18');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id` int(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','User') NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'sultan', '$2y$10$MTknnhokR15w0/fBNPWJm.ItdE4A9oKxjsaIlnYGhnuX6AqF.2DOG', 'Admin', '2025-05-26 14:19:36'),
(5, 'nabil', '$2y$10$bu1SaiQ6DGRkOs4bNcoaKuUjGwEdw2Mhyrswszw36mKcOwz8WO3te', 'User', '2025-05-26 14:28:41'),
(6, 'sultannabil', '$2y$10$Ddzvlr3yjQU2019dl2agreHPJ7pTlQI.lDwsprWTub9JQJeAioCZ6', 'Admin', '2025-05-28 10:16:58');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `dokumen_terenkripsi`
--
ALTER TABLE `dokumen_terenkripsi`
  ADD PRIMARY KEY (`id_dokumen`),
  ADD KEY `username` (`username`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_username_unique` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `dokumen_terenkripsi`
--
ALTER TABLE `dokumen_terenkripsi`
  MODIFY `id_dokumen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `dokumen_terenkripsi`
--
ALTER TABLE `dokumen_terenkripsi`
  ADD CONSTRAINT `dokumen_terenkripsi_ibfk_1` FOREIGN KEY (`username`) REFERENCES `user` (`username`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
