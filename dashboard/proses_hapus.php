<?php
// Selalu mulai sesi di awal
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/config.php';
require_once '../includes/functions.php'; // Memanggil functions.php

// Cek login
if (!isset($_SESSION['username'])) {
    // Untuk skrip proses, kita bisa langsung hentikan atau redirect tanpa alert JS
    header("Location: ../index.php"); 
    exit();
}

// Cek koneksi database
if (!$conn) {
    // Catat error atau redirect dengan pesan error
    $_SESSION['pesan_error'] = "Koneksi database gagal saat mencoba menghapus.";
    header("Location: daftar_dokumen.php");
    exit();
}

$id_dokumen_hapus = null;

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_dokumen_hapus = $_GET['id'];
}

if ($id_dokumen_hapus) {
    // Panggil fungsi hapus dari functions.php
    // Kirim $conn sebagai parameter
    if (handleFileDelete($conn, $id_dokumen_hapus, $_SESSION['username'])) {
        $_SESSION['pesan_sukses'] = "Dokumen berhasil dihapus.";
    } else {
        $_SESSION['pesan_error'] = "Gagal menghapus dokumen. File mungkin tidak ditemukan atau Anda tidak memiliki izin.";
    }
} else {
    $_SESSION['pesan_error'] = "ID Dokumen tidak valid untuk dihapus.";
}

mysqli_close($conn);
header("Location: daftar_dokumen.php"); // Redirect kembali ke daftar dokumen
exit();
?>