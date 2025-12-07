<?php
// Variabel spesifik halaman
$currentPageTitle = "Daftar Dokumen";
$pageSpecificCss = "daftar_dokumen.css"; 

include '../includes/header.php'; 
include '../includes/navigation.php'; 
require_once '../config/config.php'; 

if (!$conn) {
    echo "<div class='main'><div class='page-content-header'><h2>Error Database</h2></div><div class='document-tables-container'><div class='document-list-card'><p style='color:red; text-align:center; padding:20px;'>Error: Tidak bisa terhubung ke database. Periksa file config.php.</p></div></div></div>";
    echo "</div></body></html>"; 
    exit();
}

// --- PENGATURAN PAGINATION ---
$data_per_halaman = 5; 

// Pagination untuk Tabel Dokumen Terenkripsi
$halaman_enkripsi_saat_ini = isset($_GET['page_enk']) ? (int)$_GET['page_enk'] : 1;
if ($halaman_enkripsi_saat_ini < 1) $halaman_enkripsi_saat_ini = 1;
$offset_enkripsi = ($halaman_enkripsi_saat_ini - 1) * $data_per_halaman;

// Pagination untuk Tabel Riwayat Dokumen Terdekripsi
$halaman_dekripsi_saat_ini = isset($_GET['page_dek']) ? (int)$_GET['page_dek'] : 1;
if ($halaman_dekripsi_saat_ini < 1) $halaman_dekripsi_saat_ini = 1;
$offset_dekripsi = ($halaman_dekripsi_saat_ini - 1) * $data_per_halaman;

$username_pengguna = $_SESSION['username']; // Ambil username dari sesi

// --- HITUNG TOTAL DATA UNTUK PAGINATION ---
// Total data Terenkripsi
$sql_total_enkripsi = "SELECT COUNT(id_dokumen) as total FROM dokumen_terenkripsi WHERE username = ? AND status_proses = 'Terenkripsi'";
$stmt_total_enk = mysqli_prepare($conn, $sql_total_enkripsi);
mysqli_stmt_bind_param($stmt_total_enk, "s", $username_pengguna);
mysqli_stmt_execute($stmt_total_enk);
$result_total_enk = mysqli_stmt_get_result($stmt_total_enk);
$total_data_enkripsi = mysqli_fetch_assoc($result_total_enk)['total'] ?? 0;
mysqli_stmt_close($stmt_total_enk);
$total_halaman_enkripsi = ceil($total_data_enkripsi / $data_per_halaman);

// Total data Terdekripsi
$sql_total_dekripsi = "SELECT COUNT(id_dokumen) as total FROM dokumen_terenkripsi WHERE username = ? AND status_proses = 'Terdekripsi'";
$stmt_total_dek = mysqli_prepare($conn, $sql_total_dekripsi);
mysqli_stmt_bind_param($stmt_total_dek, "s", $username_pengguna);
mysqli_stmt_execute($stmt_total_dek);
$result_total_dek = mysqli_stmt_get_result($stmt_total_dek);
$total_data_dekripsi = mysqli_fetch_assoc($result_total_dek)['total'] ?? 0;
mysqli_stmt_close($stmt_total_dek);
$total_halaman_dekripsi = ceil($total_data_dekripsi / $data_per_halaman);

?>

        <div class="main">
            <?php include '../includes/topbar.php'; ?>

            <div class="page-content-header">
                <h2><?php echo htmlspecialchars($currentPageTitle); ?></h2>
            </div>

            <?php
            // Menampilkan pesan sukses atau error
            if (isset($_SESSION['pesan_sukses'])) {
                echo "<div class='alert alert-success' style='margin: 0 20px 20px 20px; padding: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px;'>" . htmlspecialchars($_SESSION['pesan_sukses']) . "</div>";
                unset($_SESSION['pesan_sukses']); 
            }
            if (isset($_SESSION['pesan_error'])) {
                echo "<div class='alert alert-danger' style='margin: 0 20px 20px 20px; padding: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px;'>" . htmlspecialchars($_SESSION['pesan_error']) . "</div>";
                unset($_SESSION['pesan_error']); 
            }
            ?>

            <div class="document-tables-container">
                
                <div class="document-list-card">
                    <div class="cardHeader">
                        <h3>Dokumen Terenkripsi</h3>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th data-label="No.">No.</th>
                                <th data-label="Nama File Asli">Nama File Asli</th>
                                <th data-label="File Enkripsi">File Enkripsi</th>
                                <th data-label="Algoritma">Algoritma</th>
                                <th data-label="Ukuran Asli (KB)">Ukuran Asli (KB)</th>
                                <th data-label="Ukuran Enkrip (KB)">Ukuran Enkrip (KB)</th>
                                <th data-label="Durasi Enkripsi">Durasi Enkripsi</th>
                                <th data-label="Tgl. Unggah">Tgl. Unggah</th>
                                <th data-label="Deskripsi">Deskripsi</th>
                                <th data-label="Aksi">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($conn) {
                                $query_enkripsi = "SELECT id_dokumen, nama_asli_file, nama_file_enkripsi, algoritma_enkripsi, 
                                                          ukuran_file_kb, ukuran_file_enkripsi_kb, tanggal_unggah, 
                                                          deskripsi_file, durasi_enkripsi_detik 
                                                   FROM dokumen_terenkripsi 
                                                   WHERE username = ? AND status_proses = 'Terenkripsi' 
                                                   ORDER BY tanggal_unggah DESC
                                                   LIMIT ? OFFSET ?"; // Tambahkan LIMIT dan OFFSET
                                
                                $stmt_enkripsi = mysqli_prepare($conn, $query_enkripsi);
                                
                                if ($stmt_enkripsi) {
                                    mysqli_stmt_bind_param($stmt_enkripsi, "sii", $username_pengguna, $data_per_halaman, $offset_enkripsi);
                                    mysqli_stmt_execute($stmt_enkripsi);
                                    $hasil_enkripsi = mysqli_stmt_get_result($stmt_enkripsi);
                                    // Nomor dimulai dari offset + 1
                                    $nomor = $offset_enkripsi + 1;

                                    if ($hasil_enkripsi && mysqli_num_rows($hasil_enkripsi) > 0) {
                                        while ($row = mysqli_fetch_assoc($hasil_enkripsi)) {
                                            // ... (echo <tr> dan <td> Anda yang sudah ada untuk tabel terenkripsi) ...
                                            // Contoh untuk satu baris:
                                            echo "<tr>";
                                            echo "<td data-label='No.:'>" . $nomor++ . ".</td>";
                                            echo "<td data-label='Nama File Asli:'>" . htmlspecialchars($row['nama_asli_file']) . "</td>";
                                            echo "<td data-label='File Enkripsi:'>" . htmlspecialchars($row['nama_file_enkripsi']) . "</td>";
                                            echo "<td data-label='Algoritma:'>" . htmlspecialchars($row['algoritma_enkripsi']) . "</td>";
                                            echo "<td data-label='Ukuran Asli (KB):'>" . htmlspecialchars($row['ukuran_file_kb']) . " KB</td>";
                                            echo "<td data-label='Ukuran Enkrip (KB):'>" . ($row['ukuran_file_enkripsi_kb'] ? htmlspecialchars($row['ukuran_file_enkripsi_kb']) . " KB" : '-') . "</td>";
                                            echo "<td data-label='Durasi Enkripsi:'>" . ($row['durasi_enkripsi_detik'] ? htmlspecialchars($row['durasi_enkripsi_detik']) . " detik" : '-') . "</td>";
                                            echo "<td data-label='Tgl. Unggah:'>" . htmlspecialchars(date('d-m-Y H:i', strtotime($row['tanggal_unggah']))) . "</td>";
                                            echo "<td data-label='Deskripsi:'>" . (!empty($row['deskripsi_file']) ? htmlspecialchars(substr($row['deskripsi_file'], 0, 30)) . (strlen($row['deskripsi_file']) > 30 ? '...' : '') : '-') . "</td>";
                                            
                                            echo "<td data-label='Aksi:'>";
                                            $link_dekripsi = "#"; 
                                            $id_dok_current = $row['id_dokumen'];
                                            $algoritma_file_enc = isset($row['algoritma_enkripsi']) ? strtolower(htmlspecialchars($row['algoritma_enkripsi'])) : '';

                                            if ($algoritma_file_enc === 'aes-128') {
                                                $link_dekripsi = "dekripsi_aes128.php?id=" . $id_dok_current;
                                            } elseif ($algoritma_file_enc === 'des' || $algoritma_file_enc === 'des-64') {
                                                $link_dekripsi = "dekripsi_des64.php?id=" . $id_dok_current; 
                                            }
                                            echo "<a href='" . $link_dekripsi . "' class='btn-aksi btn-dekripsi'>Dekripsi</a> ";
                                            echo "<a href='download_file.php?id=" . $id_dok_current . "&type=enkripsi' class='btn-aksi btn-download'>Download .enc</a> ";
                                            echo "<a href='proses_hapus.php?id=" . $id_dok_current . "' class='btn-aksi btn-hapus' onclick='return confirm(\"Anda yakin ingin menghapus file ini beserta riwayatnya?\");'>Hapus</a>";
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='10' style='text-align:center;'>Belum ada dokumen yang terenkripsi.</td></tr>";
                                    }
                                    mysqli_stmt_close($stmt_enkripsi);
                                } else {
                                    echo "<tr><td colspan='10' style='text-align:center;'>Gagal mengambil data terenkripsi: " . htmlspecialchars(mysqli_error($conn)) . "</td></tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                    <div class="pagination">
                        <?php if ($total_halaman_enkripsi > 1): ?>
                            <?php if ($halaman_enkripsi_saat_ini > 1): ?>
                                <a href="?page_enk=<?php echo $halaman_enkripsi_saat_ini - 1; ?>&page_dek=<?php echo $halaman_dekripsi_saat_ini; ?>">&laquo; Sebelumnya</a>
                            <?php endif; ?>
                            <?php for ($i = 1; $i <= $total_halaman_enkripsi; $i++): ?>
                                <a href="?page_enk=<?php echo $i; ?>&page_dek=<?php echo $halaman_dekripsi_saat_ini; ?>" class="<?php if ($i == $halaman_enkripsi_saat_ini) echo 'active'; ?>"><?php echo $i; ?></a>
                            <?php endfor; ?>
                            <?php if ($halaman_enkripsi_saat_ini < $total_halaman_enkripsi): ?>
                                <a href="?page_enk=<?php echo $halaman_enkripsi_saat_ini + 1; ?>&page_dek=<?php echo $halaman_dekripsi_saat_ini; ?>">Berikutnya &raquo;</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="document-list-card">
                    <div class="cardHeader">
                        <h3>Riwayat Dokumen Terdekripsi</h3>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th data-label="No.">No.</th>
                                <th data-label="Nama File Asli">Nama File Asli</th>
                                <th data-label="Nama File Hasil Dekripsi">Nama File Hasil Dekripsi</th>
                                <th data-label="Algoritma">Algoritma</th>
                                <th data-label="Ukuran Hasil Dekripsi (KB)">Ukuran Hasil Dekripsi (KB)</th>
                                <th data-label="Durasi Dekripsi">Durasi Dekripsi</th>
                                <th data-label="Aksi">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($conn) { 
                                $query_dekripsi = "SELECT id_dokumen, nama_asli_file, algoritma_enkripsi, 
                                                          nama_file_terdekripsi, ukuran_file_terdekripsi_kb, 
                                                          durasi_dekripsi_detik 
                                                   FROM dokumen_terenkripsi 
                                                   WHERE username = ? AND status_proses = 'Terdekripsi' 
                                                   ORDER BY tanggal_unggah DESC
                                                   LIMIT ? OFFSET ?"; 
                                
                                $stmt_dekripsi = mysqli_prepare($conn, $query_dekripsi);
                                
                                if ($stmt_dekripsi) {
                                    mysqli_stmt_bind_param($stmt_dekripsi, "sii", $username_pengguna, $data_per_halaman, $offset_dekripsi); 
                                    mysqli_stmt_execute($stmt_dekripsi);
                                    $hasil_dekripsi = mysqli_stmt_get_result($stmt_dekripsi);
                                    $nomor_dek = $offset_dekripsi + 1;

                                    if ($hasil_dekripsi && mysqli_num_rows($hasil_dekripsi) > 0) {
                                        while ($row_dek = mysqli_fetch_assoc($hasil_dekripsi)) {
                                            // ... (echo <tr> dan <td> Anda yang sudah ada untuk tabel terdekripsi) ...
                                            echo "<tr>";
                                            echo "<td data-label='No.:'>" . $nomor_dek++ . ".</td>";
                                            echo "<td data-label='Nama File Asli:'>" . htmlspecialchars($row_dek['nama_asli_file']) . "</td>";
                                            echo "<td data-label='Nama File Hasil Dekripsi:'>" . ($row_dek['nama_file_terdekripsi'] ? htmlspecialchars($row_dek['nama_file_terdekripsi']) : 'N/A') . "</td>";
                                            echo "<td data-label='Algoritma:'>" . htmlspecialchars($row_dek['algoritma_enkripsi']) . "</td>";
                                            echo "<td data-label='Ukuran Hasil Dekripsi (KB):'>" . ($row_dek['ukuran_file_terdekripsi_kb'] ? htmlspecialchars($row_dek['ukuran_file_terdekripsi_kb']) . " KB" : '-') . "</td>";
                                            echo "<td data-label='Durasi Dekripsi:'>" . ($row_dek['durasi_dekripsi_detik'] ? htmlspecialchars($row_dek['durasi_dekripsi_detik']) . " detik" : '-') . "</td>";
                                            echo "<td data-label='Aksi:'>
                                                    <a href='download_file.php?id=" . $row_dek['id_dokumen'] . "&type=dekripsi' class='btn-aksi btn-download'>Download Hasil Dekripsi</a>
                                                    <a href='proses_hapus.php?id=" . $row_dek['id_dokumen'] . "' class='btn-aksi btn-hapus' onclick='return confirm(\"Anda yakin ingin menghapus riwayat ini?\");'>Hapus Riwayat</a>
                                                  </td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='7' style='text-align:center;'>Belum ada dokumen yang tercatat sebagai terdekripsi.</td></tr>";
                                    }
                                    mysqli_stmt_close($stmt_dekripsi);
                                } else {
                                    echo "<tr><td colspan='7' style='text-align:center;'>Gagal mengambil data riwayat dekripsi: " . htmlspecialchars(mysqli_error($conn)) . "</td></tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                     <div class="pagination">
                        <?php if ($total_halaman_dekripsi > 1): ?>
                            <?php if ($halaman_dekripsi_saat_ini > 1): ?>
                                <a href="?page_enk=<?php echo $halaman_enkripsi_saat_ini; ?>&page_dek=<?php echo $halaman_dekripsi_saat_ini - 1; ?>">&laquo; Sebelumnya</a>
                            <?php endif; ?>
                            <?php for ($i = 1; $i <= $total_halaman_dekripsi; $i++): ?>
                                <a href="?page_enk=<?php echo $halaman_enkripsi_saat_ini; ?>&page_dek=<?php echo $i; ?>" class="<?php if ($i == $halaman_dekripsi_saat_ini) echo 'active'; ?>"><?php echo $i; ?></a>
                            <?php endfor; ?>
                            <?php if ($halaman_dekripsi_saat_ini < $total_halaman_dekripsi): ?>
                                <a href="?page_enk=<?php echo $halaman_enkripsi_saat_ini; ?>&page_dek=<?php echo $halaman_dekripsi_saat_ini + 1; ?>">Berikutnya &raquo;</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div> </div> </div> <script src="../assets/js/dashboard.js"></script> 
    
    <?php 
    if (isset($conn)) {
         mysqli_close($conn);
    }
    ?>
</body>
</html>