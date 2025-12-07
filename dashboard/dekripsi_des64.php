<?php
// Variabel spesifik halaman
$currentPageTitle = "Dekripsi File DES";
// Gunakan CSS yang sama dengan form dekripsi AES agar tampilan konsisten
$pageSpecificCss = "dekripsi-style.css"; 
$hideSearchInTopbar = true; // Sembunyikan search bar di topbar

// Memuat header.php (yang di dalamnya sudah ada session_start() dan cek login)
include '../includes/header.php'; 
// Memuat file konfigurasi database untuk mendapatkan variabel koneksi $conn
require_once '../config/config.php'; 
// Memuat file navigasi
include '../includes/navigation.php'; 

// Inisialisasi variabel
$nama_file_info = "";
$id_dokumen_valid = null;
$pesan_error_halaman = "";
$algoritma_file = "DES-64"; // Default untuk halaman ini

// Cek koneksi database
if (!$conn) {
    $pesan_error_halaman = "Koneksi ke database gagal. Periksa file konfigurasi.";
} else {
    // Cek apakah halaman diakses dengan ID file dari daftar dokumen
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $id_dokumen = filter_var($_GET['id'], FILTER_VALIDATE_INT);
        
        if ($id_dokumen === false) {
            $pesan_error_halaman = "ID file yang diberikan tidak valid.";
        } else {
            $username_pengguna = $_SESSION['username']; 
            // Query untuk mendapatkan detail file yang akan didekripsi
            $sql_file = "SELECT id_dokumen, nama_asli_file, algoritma_enkripsi
                         FROM dokumen_terenkripsi 
                         WHERE id_dokumen = ? AND username = ? AND status_proses = 'Terenkripsi'";
            
            $stmt_file = mysqli_prepare($conn, $sql_file);
            if ($stmt_file) {
                mysqli_stmt_bind_param($stmt_file, "is", $id_dokumen, $username_pengguna);
                mysqli_stmt_execute($stmt_file);
                $result_file = mysqli_stmt_get_result($stmt_file);

                if ($result_file && mysqli_num_rows($result_file) == 1) {
                    $file_data = mysqli_fetch_assoc($result_file);
                    
                    // Verifikasi apakah algoritma adalah DES
                    if (strtoupper($file_data['algoritma_enkripsi']) === 'DES' || strtoupper($file_data['algoritma_enkripsi']) === 'DES-64') {
                        $nama_file_info = htmlspecialchars($file_data['nama_asli_file']);
                        $id_dokumen_valid = $file_data['id_dokumen'];
                        $algoritma_file = htmlspecialchars($file_data['algoritma_enkripsi']);
                    } else {
                        $pesan_error_halaman = "File ini tidak dienkripsi menggunakan DES. Algoritma terdeteksi: " . htmlspecialchars($file_data['algoritma_enkripsi']) . ". Silakan gunakan form dekripsi yang sesuai.";
                    }
                } else {
                    $pesan_error_halaman = "File tidak ditemukan, bukan milik Anda, atau statusnya bukan 'Terenkripsi'.";
                }
                mysqli_stmt_close($stmt_file);
            } else {
                $pesan_error_halaman = "Gagal menyiapkan query untuk mengambil data file: " . htmlspecialchars(mysqli_error($conn));
            }
        }
    }
    // Jika tidak ada ID di URL, halaman akan langsung menampilkan form upload, $pesan_error_halaman akan kosong.
}
?>

        <div class="main">
            <?php include '../includes/topbar.php'; ?>

            <div class="page-content-header">
                 <h2><?php echo htmlspecialchars($currentPageTitle); ?></h2>
            </div>

            <div class="form-container">
                <div class="form-card">
                    <div class="cardHeader">
                        <h3>Dekripsi File dengan <?php echo $algoritma_file; ?></h3>
                    </div>

                    <?php if (!empty($pesan_error_halaman)): // Jika ada error saat memuat data dengan ID ?>
                        <div class="alert alert-danger" role="alert" style="margin-bottom: 20px;">
                            <?php echo $pesan_error_halaman; ?> <a href="daftar_dokumen.php" style="font-weight: bold; text-decoration: underline;">Kembali ke Daftar Dokumen</a>.
                        </div>
                    <?php else: // Jika tidak ada error, tampilkan form dekripsi ?>
                        
                        <?php if ($id_dokumen_valid !== null): // Info jika mendekripsi dari daftar dokumen ?>
                            <p class="file-info">Anda akan mendekripsi file: <strong><?php echo $nama_file_info; ?></strong></p>
                        <?php else: ?>
                             <p class="file-info">Silakan unggah file `.enc` yang dienkripsi dengan DES untuk didekripsi.</p>
                        <?php endif; ?>
                        
                        <form action="proses_dekripsi_des64.php" method="POST" enctype="multipart/form-data" id="desDecryptionForm">
                            
                            <?php if ($id_dokumen_valid !== null): // Jika ada ID, kirim sebagai hidden input ?>
                                <input type="hidden" name="fileid" value="<?php echo $id_dokumen_valid; ?>">
                            <?php else: // Jika tidak ada ID, tampilkan form upload file ?>
                                <div class="form-group">
                                    <label for="fileToDecrypt">1. Pilih File `.enc`</label>
                                    <input type="file" name="file" id="fileToDecrypt" class="form-control-file" required>
                                </div>
                                <div class="form-group">
                                    <label for="originalFileName">2. Masukkan Nama File Asli (termasuk ekstensi)</label>
                                    <input type="text" name="nama_asli_manual" id="originalFileName" class="form-control" placeholder="Contoh: laporan_keuangan.xlsx" required>
                                    <small class="form-text text-muted">Ini akan menjadi nama dan tipe file setelah berhasil didekripsi.</small>
                                </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label for="decryptionPassword">
                                    <?php echo ($id_dokumen_valid === null) ? '3. ' : ''; ?>Password Dekripsi
                                </label>
                                <input type="password" name="pwdfile" id="decryptionPassword" class="form-control" placeholder="Masukkan Password yang digunakan saat enkripsi" required>
                                <small class="form-text text-muted">Password ini akan digunakan untuk menghasilkan kunci dekripsi DES 64-bit.</small>
                            </div>
                            
                            <div class="form-group button-group">
                                <button type="submit" name="decrypt_des_now" class="btn btn-success">Dekripsi File Sekarang</button>
                                <a href="daftar_dokumen.php" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            
        </div> </div> <script src="../assets/js/dashboard.js"></script> 
    
    <?php 
    if (isset($conn)) {
         mysqli_close($conn);
    }
    ?>
</body>
</html>