<?php
// Selalu mulai sesi di awal
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/config.php';
require_once '../includes/functions.php'; // Memanggil functions.php

// Cek login
if (!isset($_SESSION['username'])) {
    die("Akses ditolak. Silakan login.");
}

// Cek koneksi database
if (!$conn) {
    die("Koneksi database gagal.");
}

$id_dokumen_download = null;
$tipe_download = null;

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_dokumen_download = $_GET['id'];
}

if (isset($_GET['type']) && ($_GET['type'] === 'enkripsi' || $_GET['type'] === 'dekripsi')) {
    $tipe_download = $_GET['type'];
}

if ($id_dokumen_download && $tipe_download) {
    // Panggil fungsi download dari functions.php
    // Kirim $conn sebagai parameter
    if (!handleFileDownload($conn, $id_dokumen_download, $_SESSION['username'], $tipe_download)) {
        // Jika handleFileDownload mengembalikan false, berarti ada error
        // Pesan error sudah dicatat di error_log oleh fungsi handleFileDownload
        // Anda bisa redirect atau tampilkan pesan error umum di sini
        echo "<script>
                alert('Gagal memulai download. File mungkin tidak ditemukan atau Anda tidak memiliki izin.');
                window.history.back(); // Kembali ke halaman sebelumnya
              </script>";
    }
    // Jika download berhasil, skrip di handleFileDownload sudah exit.
} else {
    echo "<script>
            alert('Parameter download tidak valid.');
            window.location.href = 'daftar_dokumen.php'; // Arahkan ke daftar dokumen
          </script>";
}

mysqli_close($conn);
?>