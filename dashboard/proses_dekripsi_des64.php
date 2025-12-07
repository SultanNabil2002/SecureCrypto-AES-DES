<?php
// Memulai atau melanjutkan sesi PHP.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Error reporting untuk development.
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include file konfigurasi database dan kelas DES.
require_once '../config/config.php';
require_once '../includes/DES.php'; // Menggunakan DES.php

// Pengecekan koneksi database
if (!$conn) {
    echo "<script>alert('DEKRIPSI DES GAGAL! Tidak bisa terhubung ke database.'); window.location.href='daftar_dokumen.php';</script>";
    exit();
}

// Pengecekan apakah pengguna sudah login.
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Akses ditolak! Anda harus login terlebih dahulu.'); window.location.href='../index.php';</script>";
    exit();
}

// Memeriksa apakah form telah disubmit dengan benar.
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['decrypt_des_now'])) {

    // Validasi input password di awal
    $password_input_user = isset($_POST['pwdfile']) ? $_POST['pwdfile'] : '';
    if (empty($password_input_user)) {
        echo "<script>alert('DEKRIPSI DES GAGAL!\\nPassword tidak boleh kosong.'); window.history.back();</script>";
        exit();
    }
    // Hasilkan kunci 8 byte (64-bit) dari password untuk DES.
    $kunci_des_dari_user = substr(md5($password_input_user), 0, 8);
    
    // Inisialisasi variabel
    $id_dokumen = null;
    $nama_file_asli_output = '';
    $path_file_terenkripsi_absolut = '';
    $kunci_tersimpan_db = '';
    $is_upload_mode = false;
    $nama_file_upload = '';

    // Cek apakah ini mode upload file atau mode dari daftar dokumen
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        // --- MODE UPLOAD FILE ---
        $is_upload_mode = true;

        $nama_file_upload = $_FILES['file']['name'];
        if (strtolower(pathinfo($nama_file_upload, PATHINFO_EXTENSION)) !== 'enc') {
            echo "<script>alert('DEKRIPSI GAGAL!\\nFile yang diunggah harus berekstensi .enc'); window.history.back();</script>";
            exit();
        }

        $nama_asli_manual = isset($_POST['nama_asli_manual']) ? trim($_POST['nama_asli_manual']) : '';
        if (empty($nama_asli_manual)) {
             echo "<script>alert('DEKRIPSI GAGAL!\\nNama File Asli wajib diisi saat mengunggah file.'); window.history.back();</script>";
             exit();
        }

        $path_file_terenkripsi_absolut = $_FILES['file']['tmp_name'];
        $nama_file_asli_output = $nama_asli_manual;
        // Saat upload, kunci tidak bisa divalidasi dengan DB, jadi kita asumsikan kunci input benar
        $kunci_tersimpan_db = $kunci_des_dari_user;

    } elseif (isset($_POST['fileid']) && !empty($_POST['fileid'])) {
        // --- MODE DARI DAFTAR DOKUMEN ---
        $is_upload_mode = false;
        $id_dokumen = mysqli_real_escape_string($conn, $_POST['fileid']);
        
        $sql_file = "SELECT nama_asli_file, path_file_enkripsi, kunci_enkripsi, algoritma_enkripsi FROM dokumen_terenkripsi WHERE id_dokumen = ? AND username = ?";
        $stmt_file = mysqli_prepare($conn, $sql_file);
        
        if ($stmt_file) {
            mysqli_stmt_bind_param($stmt_file, "is", $id_dokumen, $_SESSION['username']);
            mysqli_stmt_execute($stmt_file);
            $result = mysqli_stmt_get_result($stmt_file);

            if ($result && $file_data = mysqli_fetch_assoc($result)) {
                // Verifikasi bahwa file ini memang dienkripsi dengan DES
                if (strtoupper($file_data['algoritma_enkripsi']) !== 'DES' && strtoupper($file_data['algoritma_enkripsi']) !== 'DES-64') {
                    echo "<script>alert('DEKRIPSI GAGAL!\\nFile ini tidak dienkripsi dengan DES.'); window.history.back();</script>";
                    exit();
                }
                $nama_file_asli_output = $file_data['nama_asli_file'];
                $kunci_tersimpan_db = $file_data['kunci_enkripsi'];
                $path_file_terenkripsi_absolut = realpath(__DIR__ . DIRECTORY_SEPARATOR . $file_data['path_file_enkripsi']);
            } else {
                 echo "<script>alert('DEKRIPSI DES GAGAL!\\nDetail file tidak ditemukan atau bukan milik Anda.'); window.location.href='daftar_dokumen.php';</script>";
                 exit();
            }
            mysqli_stmt_close($stmt_file);
        }
    } else {
        echo "<script>alert('DEKRIPSI DES GAGAL!\\nTidak ada file yang dipilih atau ID dokumen tidak valid.'); window.location.href='dekripsi_des64.php';</script>";
        exit();
    }
    
    // Validasi akhir password untuk mode dari daftar
    if (!$is_upload_mode && ($kunci_des_dari_user !== $kunci_tersimpan_db)) {
        echo "<script>alert('DEKRIPSI DES GAGAL!\\nPassword yang Anda masukkan salah.'); window.history.back();</script>";
        exit();
    }
    
    // ... (Lanjutan di Part 2: Persiapan File Output dan Proses Dekripsi Inti)
// Melanjutkan dari Part 1 (setelah validasi password dan algoritma)
// ...
    
    // Persiapan file output
    $folder_hasil_dekripsi = __DIR__ . "/../hasil/Terdekripsi/";
    if (!is_dir($folder_hasil_dekripsi)) {
        // Coba buat folder jika belum ada
        if (!mkdir($folder_hasil_dekripsi, 0775, true)) {
            echo "<script>alert('DEKRIPSI DES GAGAL!\\nTidak bisa membuat folder tujuan.'); window.history.back();</script>";
            exit();
        }
    }
    // Buat nama file unik untuk hasil dekripsi
    $path_hasil_dekripsi_absolut = $folder_hasil_dekripsi . time() . "_" . uniqid() . "_DES_" . $nama_file_asli_output;

    // Pastikan file sumber terenkripsi benar-benar ada dan bisa dibaca
    if ($path_file_terenkripsi_absolut && file_exists($path_file_terenkripsi_absolut) && is_readable($path_file_terenkripsi_absolut)) {
        
        $file_sumber = fopen($path_file_terenkripsi_absolut, 'rb');
        $file_tujuan = fopen($path_hasil_dekripsi_absolut, 'wb');

        if ($file_sumber && $file_tujuan) {
            // Pengaturan server untuk file besar
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', '-1');
            
            // Buat objek dari kelas DES dengan kunci yang sudah divalidasi
            $des_cipher = new DES($kunci_tersimpan_db);
            $waktu_mulai_dekripsi = microtime(true);
            
            $decrypted_padded_content = '';
            $berhasil_sepenuhnya = true;
            $block_size = 8; // Ukuran blok untuk DES adalah 8 byte

            // Baca seluruh file terenkripsi untuk didekripsi
            // Menggunakan str_split setelah membaca semua konten lebih mudah untuk didekripsi per blok
            $ciphertext = file_get_contents($path_file_terenkripsi_absolut);
            fclose($file_sumber);

            // Pastikan ukuran ciphertext adalah kelipatan dari ukuran blok
            if (strlen($ciphertext) % $block_size !== 0) {
                $berhasil_sepenuhnya = false;
                error_log("DES Decrypt: Ukuran file terenkripsi bukan kelipatan 8.");
            } else {
                // Dekripsi blok per blok
                $ciphertext_chunks = str_split($ciphertext, $block_size);
                foreach ($ciphertext_chunks as $chunk) {
                    $decrypted_padded_content .= $des_cipher->decryptBlock($chunk);
                }
            }
            
            // --- VALIDASI MAGIC NUMBER & UNPADDING ---
            $magic_number = "DES_v1_";
            $final_plaintext = null;

            // Pengecekan magic number sekarang menjadi validasi utama password/algoritma salah
            if ($berhasil_sepenuhnya && strpos($decrypted_padded_content, $magic_number) === 0) {
                // Magic number ditemukan, hapus dari konten
                $decrypted_content_no_magic = substr($decrypted_padded_content, strlen($magic_number));
                
                // Lakukan Unpadding PKCS#7
                $len = strlen($decrypted_content_no_magic);
                if ($len > 0) {
                    $pad = ord($decrypted_content_no_magic[$len - 1]);
                    // Pastikan nilai padding valid
                    if ($pad > 0 && $pad <= $block_size && $len >= $pad) {
                        $final_plaintext = substr($decrypted_content_no_magic, 0, $len - $pad);
                    } else { 
                        $berhasil_sepenuhnya = false; 
                        error_log("DES Decrypt: Nilai padding tidak valid."); 
                    }
                } else { // Kasus ini terjadi jika file asli (sebelum enkripsi) benar-benar kosong
                    $final_plaintext = "";
                }
            } else {
                // Gagal karena magic number tidak cocok (kemungkinan password salah atau file bukan format DES dari sistem ini)
                $berhasil_sepenuhnya = false;
            }
            
            // Tulis plaintext final ke file tujuan
            if ($berhasil_sepenuhnya && $final_plaintext !== null) {
                if (fwrite($file_tujuan, $final_plaintext) === false) { 
                    $berhasil_sepenuhnya = false; 
                }
            } else { 
                $berhasil_sepenuhnya = false; 
            }
            fclose($file_tujuan);

            // ... (Lanjutan di Part 3: Evaluasi Akhir dan Respon ke Pengguna) ...
// Melanjutkan dari Part 2 (setelah blok validasi magic number & unpadding)
// ...
            // --- EVALUASI AKHIR DAN RESPON ---
            if ($berhasil_sepenuhnya) {
                // Proses dekripsi dan penulisan file berhasil, lanjutkan ke update DB dan alert

                $waktu_selesai_dekripsi = microtime(true);
                $durasi_detik_bulat_dek = round($waktu_selesai_dekripsi - $waktu_mulai_dekripsi, 2);
                
                $nama_file_hasil_dekripsi_final = basename($path_hasil_dekripsi_absolut);
                $ukuran_file_hasil_dekripsi_kb_val = round(filesize($path_hasil_dekripsi_absolut) / 1024, 2);
                $ukuran_file_enkripsi_kb = round(filesize($path_file_terenkripsi_absolut) / 1024, 2);

                if ($is_upload_mode) {
                    // --- BLOK INI DIPERBARUI SELURUHNYA ---
                    $sql_insert_history = "INSERT INTO dokumen_terenkripsi (username, nama_asli_file, nama_file_enkripsi, path_file_enkripsi, algoritma_enkripsi, kunci_enkripsi, tanggal_unggah, status_proses, durasi_dekripsi_detik, nama_file_terdekripsi, ukuran_file_terdekripsi_kb, ukuran_file_kb, ukuran_file_enkripsi_kb) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?)";
                    $stmt_insert_history = mysqli_prepare($conn, $sql_insert_history);
                    
                    // Tambahkan pengecekan ini untuk memastikan prepare berhasil
                    if ($stmt_insert_history) {
                        // Simpan nilai string ke dalam variabel sebelum di-bind
                        $status_proses = "Terdekripsi";
                        $algoritma_file_db_val = "DES-64";
                        $nama_file_upload_asli = $_FILES['file']['name'];
                        $path_enkripsi_upload = "diunggah: " . $nama_file_upload_asli;
                        $ukuran_asli_info = $ukuran_file_enkripsi_kb; 
                        
                        // Gunakan variabel-variabel tersebut di dalam bind_param
                        mysqli_stmt_bind_param($stmt_insert_history, "sssssssdsddd", 
                            $_SESSION['username'], 
                            $nama_file_asli_output, 
                            $nama_file_upload_asli, 
                            $path_enkripsi_upload, 
                            $algoritma_file_db_val, // Menggunakan variabel
                            $kunci_tersimpan_db, 
                            $status_proses,         // Menggunakan variabel
                            $durasi_detik_bulat_dek, 
                            $nama_file_hasil_dekripsi_final, 
                            $ukuran_file_hasil_dekripsi_kb_val, 
                            $ukuran_asli_info, 
                            $ukuran_file_enkripsi_kb
                        );
                        
                        mysqli_stmt_execute($stmt_insert_history);
                        $id_dokumen = mysqli_insert_id($conn);
                        mysqli_stmt_close($stmt_insert_history);
                    } else {
                        // Tambahkan error handling jika prepare gagal
                        error_log("Gagal prepare statement INSERT history dekripsi DES: " . mysqli_error($conn));
                        // Anda bisa juga set $berhasil_sepenuhnya = false di sini jika mau
                    }

                } else {
                    // ... (blok UPDATE Anda yang sudah diperbaiki sebelumnya tetap di sini) ...
                    $sql_update_dekripsi = "UPDATE dokumen_terenkripsi SET durasi_dekripsi_detik = ?, status_proses = ?, nama_file_terdekripsi = ?, ukuran_file_terdekripsi_kb = ? WHERE id_dokumen = ?";
                    $stmt_update_dekripsi = mysqli_prepare($conn, $sql_update_dekripsi);
                    if ($stmt_update_dekripsi) {
                        $status_setelah_dekripsi = "Terdekripsi";
                        mysqli_stmt_bind_param($stmt_update_dekripsi, "dssdi", $durasi_detik_bulat_dek, $status_setelah_dekripsi, $nama_file_hasil_dekripsi_final, $ukuran_file_hasil_dekripsi_kb_val, $id_dokumen);
                        mysqli_stmt_execute($stmt_update_dekripsi);
                        mysqli_stmt_close($stmt_update_dekripsi);
                    }
                }
                
                // Set session untuk link download di halaman selanjutnya
                $session_id_key = $id_dokumen; 
                $_SESSION['download_file_path_' . $session_id_key] = "hasil/Terdekripsi/" . $nama_file_hasil_dekripsi_final;
                $_SESSION['download_file_name_' . $session_id_key] = $nama_file_asli_output;

                // Membuat pesan alert yang detail
                $pesan_alert = "DEKRIPSI DES-64 BERHASIL!\\n\\n";
                $pesan_alert .= "Nama File Asli: " . addslashes($nama_file_asli_output) . "\\n";
                $pesan_alert .= "File Hasil Dekripsi: " . addslashes($nama_file_hasil_dekripsi_final) . "\\n";
                $pesan_alert .= "Ukuran Sebelum Dekripsi: " . $ukuran_file_enkripsi_kb . " KB\\n";
                $pesan_alert .= "Ukuran Setelah Dekripsi: " . $ukuran_file_hasil_dekripsi_kb_val . " KB\\n";
                $pesan_alert .= "Durasi: " . round($durasi_detik_bulat_dek, 2) . " detik.";

                echo "<script>
                        alert('" . $pesan_alert . "'); 
                        window.location.href='daftar_dokumen.php';
                      </script>"; // Redirect ke daftar_dokumen.php agar riwayat langsung terlihat
                exit();

            } else {
                // Jika $berhasil_sepenuhnya false, hapus file output yang mungkin korup
                if (file_exists($path_hasil_dekripsi_absolut)) { 
                    unlink($path_hasil_dekripsi_absolut); 
                }
                // Tampilkan pesan error spesifik
                echo "<script>
                        alert('DEKRIPSI GAGAL!\\nPassword salah, atau file yang diunggah bukan file DES yang valid.'); 
                        window.history.back();
                      </script>";
                exit();
            }
        } else { // Jika gagal fopen
             echo "<script>alert('DEKRIPSI DES-64 GAGAL!\\nTidak bisa membuka file terenkripsi atau membuat file hasil.'); window.history.back();</script>";
             exit();
        }
    } else { // Jika file terenkripsi tidak ditemukan atau tidak bisa dibaca
        echo "<script>alert('DEKRIPSI DES-64 GAGAL!\\nFile sumber tidak ditemukan di server.'); window.history.back();</script>";
        exit();
    }
} else { // Jika akses tidak valid (bukan POST)
    echo "<script>window.alert('Akses tidak valid ke halaman proses dekripsi!'); window.location.href='daftar_dokumen.php';</script>";
    exit();
}

if (isset($conn)) { 
    mysqli_close($conn); 
}
?>