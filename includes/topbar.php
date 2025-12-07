<?php
// File: Kriptografi-Aes/includes/topbar.php
// session_start(); // Tidak perlu, sudah di header.php

// Mengambil username dari session untuk ditampilkan
$username_display = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Pengguna';
?>
<div class="topbar">
    <div class="toggle">
        <ion-icon name="menu-outline"></ion-icon>
    </div>

    <div class="user">
        <span>Halo, <?php echo $username_display; ?>!</span>
    </div>
</div>