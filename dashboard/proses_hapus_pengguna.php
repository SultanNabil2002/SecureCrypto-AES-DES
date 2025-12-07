<?php
// File: Kriptografi-Aes/dashboard/proses_hapus_pengguna.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/config.php';

// --- VALIDASI AKSES & INPUT ---
// 1. Pastikan yang mengakses adalah admin yang sudah login
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    // Jika bukan admin, tendang keluar
    $_SESSION['pesan_error'] = "Anda tidak memiliki hak akses untuk menghapus pengguna.";
    header("Location: pengguna.php");
    exit();
}

// 2. Pastikan ID pengguna yang akan dihapus dikirim melalui URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['pesan_error'] = "Aksi gagal: ID pengguna tidak ditemukan.";
    header("Location: pengguna.php");
    exit();
}

$id_pengguna_hapus = $_GET['id'];
$admin_yang_login = $_SESSION['username'];

// --- VALIDASI TAMBAHAN: AMBIL USERNAME YANG AKAN DIHAPUS ---
// Ini untuk memastikan admin tidak bisa menghapus dirinya sendiri dengan memanipulasi URL
$stmt_get_user = mysqli_prepare($conn, "SELECT username FROM user WHERE id = ?");
mysqli_stmt_bind_param($stmt_get_user, "i", $id_pengguna_hapus);
mysqli_stmt_execute($stmt_get_user);
$result_get_user = mysqli_stmt_get_result($stmt_get_user);

if ($result_get_user && $user_to_delete = mysqli_fetch_assoc($result_get_user)) {
    // 3. Pengecekan agar admin tidak bisa menghapus dirinya sendiri
    if ($user_to_delete['username'] === $admin_yang_login) {
        $_SESSION['pesan_error'] = "Aksi gagal: Anda tidak dapat menghapus akun Anda sendiri.";
        header("Location: pengguna.php");
        exit();
    }
} else {
    $_SESSION['pesan_error'] = "Aksi gagal: Pengguna dengan ID tersebut tidak ditemukan.";
    header("Location: pengguna.php");
    exit();
}
mysqli_stmt_close($stmt_get_user);

// Langkah 1: Hapus dokumen yang terkait dengan pengguna ini
$stmt_delete_docs = mysqli_prepare($conn, "DELETE FROM dokumen_terenkripsi WHERE username = ?");
mysqli_stmt_bind_param($stmt_delete_docs, "s", $user_to_delete['username']);
// Eksekusi penghapusan dokumen, kita tidak perlu cek hasilnya, yang penting pengguna terhapus.
// Tapi dalam aplikasi nyata, ini perlu error handling.
mysqli_stmt_execute($stmt_delete_docs);
mysqli_stmt_close($stmt_delete_docs);


// Langkah 2: Hapus pengguna itu sendiri
$stmt_delete_user = mysqli_prepare($conn, "DELETE FROM user WHERE id = ?");
if ($stmt_delete_user) {
    mysqli_stmt_bind_param($stmt_delete_user, "i", $id_pengguna_hapus);
    
    if (mysqli_stmt_execute($stmt_delete_user)) {
        // Jika penghapusan berhasil
        $_SESSION['pesan_sukses'] = "Pengguna berhasil dihapus.";
    } else {
        // Jika gagal
        $_SESSION['pesan_error'] = "Gagal menghapus pengguna. Error: " . mysqli_stmt_error($stmt_delete_user);
    }
    mysqli_stmt_close($stmt_delete_user);
} else {
    $_SESSION['pesan_error'] = "Gagal mempersiapkan query penghapusan pengguna.";
}

if (isset($conn)) {
    mysqli_close($conn);
}

// Redirect kembali ke halaman pengguna
header("Location: pengguna.php");
exit();

?>