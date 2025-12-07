<?php
// 1. Definisikan variabel spesifik untuk halaman ini SEBELUM meng-include header
$currentPageTitle = "Beranda Dashboard";
// $pageSpecificCss = ""; // Tidak ada CSS khusus untuk halaman utama dashboard ini selain dashboard.css

// 2. Memanggil file header.php 
include '../includes/header.php'; 

// 3. Menyertakan file konfigurasi database untuk mendapatkan variabel koneksi $conn
require_once '../config/config.php';

// Pastikan koneksi database berhasil
if (!$conn) {
    echo "<div class='main'><div class='page-content-header'><h2>Error Database</h2></div><div class='content-card'><p style='color:red; text-align:center; padding:20px;'>Error: Tidak bisa terhubung ke database. Periksa file config.php.</p></div></div>";
    echo "</div></body></html>"; 
    exit();
}

$username_pengguna = $_SESSION['username']; // Asumsi sudah divalidasi oleh header.php

// --- PHP LOGIC UNTUK KARTU STATISTIK ---
// (Kode untuk $total_enkripsi, $total_deskripsi, $total_dokumen, $total_pengguna tetap sama seperti sebelumnya)
// Card 1: Jumlah File Terenkripsi
$sql_total_enkripsi = "SELECT COUNT(id_dokumen) as total_enkripsi FROM dokumen_terenkripsi WHERE username = ? AND (status_proses = 'Terenkripsi' OR status_proses = 'Proses Enkripsi')";
$stmt_total_enkripsi = mysqli_prepare($conn, $sql_total_enkripsi);
$total_enkripsi = 0;
if ($stmt_total_enkripsi) {
    mysqli_stmt_bind_param($stmt_total_enkripsi, "s", $username_pengguna);
    mysqli_stmt_execute($stmt_total_enkripsi);
    $result_total_enkripsi = mysqli_stmt_get_result($stmt_total_enkripsi);
    if ($result_total_enkripsi) { 
        $row_total_enkripsi = mysqli_fetch_assoc($result_total_enkripsi);
        $total_enkripsi = $row_total_enkripsi['total_enkripsi'] ?? 0;
    }
    mysqli_stmt_close($stmt_total_enkripsi);
}

// Card 2: Jumlah File Terdeskripsi
$sql_total_deskripsi = "SELECT COUNT(id_dokumen) as total_deskripsi FROM dokumen_terenkripsi WHERE username = ? AND status_proses = 'Terdekripsi'";
$stmt_total_deskripsi = mysqli_prepare($conn, $sql_total_deskripsi);
$total_deskripsi = 0;
if ($stmt_total_deskripsi) {
    mysqli_stmt_bind_param($stmt_total_deskripsi, "s", $username_pengguna);
    mysqli_stmt_execute($stmt_total_deskripsi);
    $result_total_deskripsi = mysqli_stmt_get_result($stmt_total_deskripsi);
    if ($result_total_deskripsi) {
        $row_total_deskripsi = mysqli_fetch_assoc($result_total_deskripsi);
        $total_deskripsi = $row_total_deskripsi['total_deskripsi'] ?? 0;
    }
    mysqli_stmt_close($stmt_total_deskripsi);
}

// Card 3: Jumlah Total Dokumen (milik pengguna ini)
$sql_total_dokumen = "SELECT COUNT(id_dokumen) as total_dokumen FROM dokumen_terenkripsi WHERE username = ?";
$stmt_total_dokumen = mysqli_prepare($conn, $sql_total_dokumen);
$total_dokumen = 0;
if ($stmt_total_dokumen) {
    mysqli_stmt_bind_param($stmt_total_dokumen, "s", $username_pengguna);
    mysqli_stmt_execute($stmt_total_dokumen);
    $result_total_dokumen = mysqli_stmt_get_result($stmt_total_dokumen);
    if ($result_total_dokumen) {
        $row_total_dokumen = mysqli_fetch_assoc($result_total_dokumen);
        $total_dokumen = $row_total_dokumen['total_dokumen'] ?? 0;
    }
    mysqli_stmt_close($stmt_total_dokumen);
}

// Card 4: Jumlah Pengguna
$total_pengguna = 0;
$label_pengguna = "Pengguna Aktif";
if (isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') { // Sesuaikan dengan case 'Admin' jika itu yang disimpan di sesi
    $sql_total_pengguna = "SELECT COUNT(id) as jumlah_pengguna FROM user";
    $result_total_pengguna_q = mysqli_query($conn, $sql_total_pengguna); // Query sederhana, bisa juga pakai prepare jika mau
    if ($result_total_pengguna_q) {
        $row_total_pengguna = mysqli_fetch_assoc($result_total_pengguna_q);
        $total_pengguna = $row_total_pengguna['jumlah_pengguna'] ?? 0;
        $label_pengguna = "Total Pengguna";
    }
} else {
    $total_pengguna = 1; 
}
// --- AKHIR PHP LOGIC UNTUK KARTU STATISTIK ---


// --- PENGATURAN PAGINATION UNTUK TABEL DOKUMEN TERBARU ---
$data_per_halaman_recent = 7; // Jumlah data per halaman untuk tabel ini
$halaman_recent_saat_ini = isset($_GET['page_recent']) ? (int)$_GET['page_recent'] : 1;
if ($halaman_recent_saat_ini < 1) $halaman_recent_saat_ini = 1;
$offset_recent = ($halaman_recent_saat_ini - 1) * $data_per_halaman_recent;

// Hitung total dokumen terbaru milik pengguna (tanpa filter status_proses untuk "Dokumen Terbaru")
$sql_total_recent_docs = "SELECT COUNT(id_dokumen) as total FROM dokumen_terenkripsi WHERE username = ?";
$stmt_total_recent = mysqli_prepare($conn, $sql_total_recent_docs);
$total_data_recent = 0;
if($stmt_total_recent){
    mysqli_stmt_bind_param($stmt_total_recent, "s", $username_pengguna);
    mysqli_stmt_execute($stmt_total_recent);
    $result_total_recent = mysqli_stmt_get_result($stmt_total_recent);
    $total_data_recent = mysqli_fetch_assoc($result_total_recent)['total'] ?? 0;
    mysqli_stmt_close($stmt_total_recent);
}
$total_halaman_recent = ceil($total_data_recent / $data_per_halaman_recent);
// --- AKHIR PENGATURAN PAGINATION ---


// Memanggil file navigasi
include '../includes/navigation.php'; 
?>

        <div class="main"> 
            <?php include '../includes/topbar.php'; ?>

            <div class="cardBox">
                <div class="card">
                    <div>
                        <div class="numbers"><?php echo $total_enkripsi; ?></div>
                        <div class="cardName">Enkripsi</div>
                    </div>
                    <div class="iconBx"><ion-icon name="lock-closed-outline"></ion-icon></div>
                </div>
                <div class="card">
                    <div>
                        <div class="numbers"><?php echo $total_deskripsi; ?></div>
                        <div class="cardName">Dekripsi</div>
                    </div>
                    <div class="iconBx"><ion-icon name="lock-open-outline"></ion-icon></div>
                </div>
                <div class="card">
                    <div>
                        <div class="numbers"><?php echo $total_dokumen; ?></div>
                        <div class="cardName">Total Dokumen Saya</div>
                    </div>
                    <div class="iconBx"><ion-icon name="document-text-outline"></ion-icon></div>
                </div>
                <div class="card">
                    <div>
                        <div class="numbers"><?php echo $total_pengguna; ?></div>
                        <div class="cardName"><?php echo htmlspecialchars($label_pengguna); ?></div>
                    </div>
                    <div class="iconBx"><ion-icon name="people-outline"></ion-icon></div>
                </div>
            </div>

            <div class="details">
                <div class="daftarDokumen">
                    <div class="cardHeader">
                        <h2>Dokumen Terbaru Saya</h2>
                        <a href="daftar_dokumen.php" class="btn">Lihat Semua</a> 
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th data-label="Nama File Asli">Nama File Asli</th>
                                <th data-label="Algoritma">Algoritma</th> <th data-label="Ukuran (KB)">Ukuran (KB)</th>
                                <th data-label="Tanggal Unggah">Tanggal Unggah</th>
                                <th data-label="Status">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Query untuk mengambil dokumen terbaru dengan pagination dan kolom algoritma
                            $query_recent_docs = "SELECT id_dokumen, nama_asli_file, algoritma_enkripsi, ukuran_file_kb, tanggal_unggah, status_proses 
                                                  FROM dokumen_terenkripsi 
                                                  WHERE username = ? 
                                                  ORDER BY tanggal_unggah DESC 
                                                  LIMIT ? OFFSET ?"; // LIMIT dan OFFSET ditambahkan
                            
                            $stmt_recent = mysqli_prepare($conn, $query_recent_docs);
                            if ($stmt_recent) {
                                mysqli_stmt_bind_param($stmt_recent, "sii", $username_pengguna, $data_per_halaman_recent, $offset_recent);
                                mysqli_stmt_execute($stmt_recent);
                                $hasil_recent = mysqli_stmt_get_result($stmt_recent);

                                if ($hasil_recent && mysqli_num_rows($hasil_recent) > 0) {
                                    while ($row = mysqli_fetch_assoc($hasil_recent)) {
                                        echo "<tr>";
                                        echo "<td data-label='Nama File:'>" . htmlspecialchars($row['nama_asli_file']) . "</td>";
                                        echo "<td data-label='Algoritma:'>" . htmlspecialchars($row['algoritma_enkripsi']) . "</td>"; // Tampilkan Algoritma
                                        echo "<td data-label='Ukuran:'>" . htmlspecialchars($row['ukuran_file_kb']) . " KB</td>";
                                        echo "<td data-label='Tanggal:'>" . htmlspecialchars(date('d-m-Y H:i', strtotime($row['tanggal_unggah']))) . "</td>";
                                        
                                        $statusClass = '';
                                        $statusText = htmlspecialchars($row['status_proses']);
                                        if ($row['status_proses'] == 'Terenkripsi' || $row['status_proses'] == 'Proses Enkripsi') {
                                            $statusClass = 'delivered'; 
                                        } elseif ($row['status_proses'] == 'Terdekripsi') {
                                            $statusClass = 'pending';   
                                        } else {
                                            $statusClass = 'return';    
                                        }
                                        echo "<td data-label='Status:'><span class='status " . $statusClass . "'>" . $statusText . "</span></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' style='text-align:center;'>Belum ada dokumen yang diproses.</td></tr>"; // Colspan menjadi 5
                                }
                                mysqli_stmt_close($stmt_recent);
                            } else {
                                 echo "<tr><td colspan='5' style='text-align:center;'>Gagal mengambil data dokumen terbaru: " . htmlspecialchars(mysqli_error($conn)) . "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    <div class="pagination">
                        <?php if ($total_halaman_recent > 1): ?>
                            <?php if ($halaman_recent_saat_ini > 1): ?>
                                <a href="?page_recent=<?php echo $halaman_recent_saat_ini - 1; ?>">&laquo; Sebelumnya</a>
                            <?php endif; ?>
                            <?php 
                            // Logika untuk menampilkan rentang nomor halaman yang lebih baik (misal maks 5 nomor)
                            $start_page = max(1, $halaman_recent_saat_ini - 2);
                            $end_page = min($total_halaman_recent, $halaman_recent_saat_ini + 2);
                            if ($end_page - $start_page < 4) { // Pastikan minimal 5 halaman ditampilkan jika memungkinkan
                                if ($start_page == 1) $end_page = min($total_halaman_recent, $start_page + 4);
                                else $start_page = max(1, $end_page - 4);
                            }
                            if ($start_page > 1) echo "<a href='?page_recent=1'>1</a><span>...</span>";
                            for ($i = $start_page; $i <= $end_page; $i++): ?>
                                <a href="?page_recent=<?php echo $i; ?>" class="<?php if ($i == $halaman_recent_saat_ini) echo 'active'; ?>"><?php echo $i; ?></a>
                            <?php endfor; 
                            if ($end_page < $total_halaman_recent) echo "<span>...</span><a href='?page_recent=".$total_halaman_recent."'>".$total_halaman_recent."</a>";
                            ?>
                            <?php if ($halaman_recent_saat_ini < $total_halaman_recent): ?>
                                <a href="?page_recent=<?php echo $halaman_recent_saat_ini + 1; ?>">Berikutnya &raquo;</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="recentCustomers">
                    <div class="cardHeader">
                        <h2>Informasi Pengguna</h2> 
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Terdaftar Sejak</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $limit_users_display = (isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') ? 5 : 1;
                        $sql_display_users = "";

                        if (isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') {
                            $sql_display_users = "SELECT username, role, created_at FROM user ORDER BY created_at DESC LIMIT ?";
                            $stmt_display_users = mysqli_prepare($conn, $sql_display_users);
                            mysqli_stmt_bind_param($stmt_display_users, "i", $limit_users_display);
                        } else {
                            $sql_display_users = "SELECT username, role, created_at FROM user WHERE username = ? LIMIT ?";
                            $stmt_display_users = mysqli_prepare($conn, $sql_display_users);
                            mysqli_stmt_bind_param($stmt_display_users, "si", $username_pengguna, $limit_users_display);
                        }
                        
                        if ($stmt_display_users) {
                            mysqli_stmt_execute($stmt_display_users);
                            $hasil_display_users = mysqli_stmt_get_result($stmt_display_users);

                            if ($hasil_display_users && mysqli_num_rows($hasil_display_users) > 0) {
                                while ($user_row_display = mysqli_fetch_assoc($hasil_display_users)) {
                                    echo "<tr>";
                                    echo "<td data-label='Username:'>" . htmlspecialchars($user_row_display['username']) . "</td>";
                                    echo "<td data-label='Role:'>" . htmlspecialchars(ucfirst($user_row_display['role'])) . "</td>";
                                    echo "<td data-label='Terdaftar:'>" . htmlspecialchars(date('d-m-Y', strtotime($user_row_display['created_at']))) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3' style='text-align:center;'>Tidak ada data pengguna untuk ditampilkan.</td></tr>";
                            }
                            mysqli_stmt_close($stmt_display_users);
                        } else {
                            echo "<tr><td colspan='3' style='text-align:center;'>Gagal mengambil data pengguna: " . htmlspecialchars(mysqli_error($conn)) . "</td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div> 
        </div> 
    </div> 

    <script src="../assets/js/dashboard.js"></script> 
    
    <?php 
    if (isset($conn)) {
         mysqli_close($conn);
    }
    ?>
</body>
</html>