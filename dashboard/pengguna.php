<?php
$currentPageTitle = "Manajemen Pengguna";
$pageSpecificCss = "pengguna-style.css";

include '../includes/header.php'; 

include '../includes/navigation.php'; 

require_once '../config/config.php'; 

// 4. Pengecekan koneksi database setelah config.php di-include
if (!$conn) {
    // Tampilkan error di dalam layout .main jika koneksi gagal
    echo "<div class='main'>"; // Buka .main agar topbar dan konten error masuk layout
    include '../includes/topbar.php'; // Tetap tampilkan topbar
    echo "<div class='page-content-header'><h2>Error Database</h2></div>";
    echo "<div class='content-card' style='padding: 20px; text-align: center;'>"; // Menggunakan .content-card untuk styling
    echo "<p style='color:red;'>Error: Tidak bisa terhubung ke database. Periksa file config.php.</p>";
    echo "</div>"; // Penutup .content-card
    echo "</div>"; // Penutup .main
    
    // Menutup tag-tag HTML yang dibuka oleh header.php
    echo "</div> ";
    echo "<script src='../assets/js/dashboard.js'></script>"; // Jika ada script umum dashboard
    echo "</body></html>";
    exit(); // Hentikan skrip
}

// --- Pengecekan Role Pengguna ---
// Dilakukan SETELAH header dan navigasi di-include agar layout dasar tetap ada
// Pastikan $_SESSION['role'] DI-SET dengan BENAR saat LOGIN menjadi 'Admin' (sesuai data di DB).
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') { 
    // Jika bukan admin, tampilkan pesan akses ditolak di dalam .main
    echo "<div class='main'>"; 
    include '../includes/topbar.php'; // Sertakan topbar
    echo "<div class='page-content-header'><h2>Akses Ditolak</h2></div>";
    echo "<div class='content-card' style='padding: 20px; text-align: center;'>";
    echo "<p style='color:red;'>Maaf, Anda tidak memiliki hak akses untuk melihat halaman ini.<br>";
    // Untuk Debug (bisa di-uncomment jika masih masalah):
    // echo "Nilai \$_SESSION['role'] saat ini: ";
    // var_dump(isset($_SESSION['role']) ? $_SESSION['role'] : "Tidak di-set");
    echo "</p>";
    echo "<a href='index.php' class='btn btn-secondary' style='text-decoration:none; padding: 8px 15px; display:inline-block; margin-top:10px;'>Kembali ke Beranda</a>";
    echo "</div>"; // Penutup .content-card
    echo "</div>"; // Penutup .main
    
    // Menutup tag-tag HTML yang dibuka oleh header.php
    echo "</div> ";
    echo "<script src='../assets/js/dashboard.js'></script>";
    echo "</body></html>";
    exit(); // Hentikan eksekusi skrip
}

// Jika kode sampai di sini, berarti pengguna adalah admin dan koneksi DB berhasil.
?>
        <div class="main">
            <?php include '../includes/topbar.php'; ?>

            <div class="page-content-header">
                <h2><?php echo htmlspecialchars($currentPageTitle); ?></h2>
            </div>

            <?php
            if (isset($_SESSION['pesan_sukses'])) {
                echo "<div class='alert alert-success' style='...'>".htmlspecialchars($_SESSION['pesan_sukses'])."</div>";
                unset($_SESSION['pesan_sukses']); 
            }
            if (isset($_SESSION['pesan_error'])) {
                echo "<div class='alert alert-danger' style='...'>".htmlspecialchars($_SESSION['pesan_error'])."</div>";
                unset($_SESSION['pesan_error']); 
            }
            ?>

            <div class="content-container">
                <div class="content-card">
                    <div class="cardHeader">
                        <h3>Daftar Semua Pengguna Terdaftar</h3>
                        </div>
                    <table>
                        <thead>
                            <tr>
                                <th data-label="No.">No.</th>
                                <th data-label="ID User">ID User</th>
                                <th data-label="Username">Username</th>
                                <th data-label="Role">Role</th>
                                <th data-label="Tanggal Dibuat">Tanggal Dibuat</th>
                                <th data-label="Aksi">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($conn) { // Pastikan $conn masih valid
                                $query_pengguna = "SELECT id, username, role, created_at FROM user ORDER BY created_at DESC";
                                $stmt_pengguna = mysqli_prepare($conn, $query_pengguna);
                                
                                if ($stmt_pengguna) {
                                    mysqli_stmt_execute($stmt_pengguna);
                                    $hasil_pengguna = mysqli_stmt_get_result($stmt_pengguna);
                                    $nomor = 1;

                                    if ($hasil_pengguna && mysqli_num_rows($hasil_pengguna) > 0) {
                                        while ($row_user = mysqli_fetch_assoc($hasil_pengguna)) {
                                            echo "<tr>";
                                            echo "<td data-label='No.:'>" . $nomor++ . ".</td>";
                                            echo "<td data-label='ID User:'>" . htmlspecialchars($row_user['id']) . "</td>";
                                            echo "<td data-label='Username:'>" . htmlspecialchars($row_user['username']) . "</td>";
                                            echo "<td data-label='Role:'>" . htmlspecialchars(ucfirst($row_user['role'])) . "</td>";
                                            echo "<td data-label='Tanggal Dibuat:'>" . htmlspecialchars(date('d-m-Y H:i', strtotime($row_user['created_at']))) . "</td>";
                                            
                                            echo "<td data-label='Aksi:'>";
                                            // Tombol Edit dihilangkan
                                            if (isset($_SESSION['username']) && $_SESSION['username'] !== $row_user['username']) {
                                                // echo "<a href='form_edit_pengguna.php?id=" . $row_user['id'] . "' class='btn-aksi btn-edit'>Edit</a> "; // TOMBOL EDIT DIHAPUS
                                                echo "<a href='proses_hapus_pengguna.php?id=" . $row_user['id'] . "' class='btn-aksi btn-hapus' onclick='return confirm(\"Anda yakin ingin menghapus pengguna ini: " . htmlspecialchars($row_user['username']) . "?\");'>Hapus</a>";
                                            } else {
                                                echo "<span style='color: #777;'><i>(Anda)</i></span>";
                                            }
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' style='text-align:center;'>Belum ada pengguna lain yang terdaftar.</td></tr>";
                                    }
                                    mysqli_stmt_close($stmt_pengguna);
                                } else {
                                    echo "<tr><td colspan='6' style='text-align:center;'>Gagal mengambil data pengguna: " . htmlspecialchars(mysqli_error($conn)) . "</td></tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div> </div> </div> </div> <script src="../assets/js/dashboard.js"></script> 
    
    <?php 
    if (isset($conn)) {
         mysqli_close($conn);
    }
    ?>
</body>
</html>