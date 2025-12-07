<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit(); // Pastikan skrip berhenti setelah redirect
}

// Judul halaman default jika tidak di-set oleh halaman yang memanggil
if (!isset($currentPageTitle)) {
    $currentPageTitle = "Dashboard Admin";
}

// Variabel untuk CSS spesifik per halaman (jika ada)
if (!isset($pageSpecificCss)) {
    $pageSpecificCss = ""; 
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($currentPageTitle); ?> - Aplikasi Kriptografi AES</title> 
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <?php if (!empty($pageSpecificCss)): ?>
        <link rel="stylesheet" href="../assets/css/<?php echo htmlspecialchars($pageSpecificCss); ?>">
    <?php endif; ?>
    
    <link rel="icon" href="../assets/img/Jasa Raharja Logo.png" type="image/png">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>

<body>
    <div class="container">