<?php
// Memulai atau melanjutkan sesi PHP.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Error reporting untuk development.
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/config.php';
require_once '../includes/AES.php';

if (!$conn) {
    echo "<script>alert('DEKRIPSI AES GAGAL! Tidak bisa terhubung ke database.'); window.history.back();</script>";
    exit();
}
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Akses ditolak! Anda harus login terlebih dahulu.'); window.location.href='../index.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['decrypt_now'])) {

    $password_input_user = isset($_POST['pwdfile']) ? $_POST['pwdfile'] : '';
    if (empty($password_input_user)) {
        echo "<script>alert('DEKRIPSI AES GAGAL!\\nPassword tidak boleh kosong.'); window.history.back();</script>";
        exit();
    }
    $kunci_aes_dari_user = substr(md5($password_input_user), 0, 16);

    $id_dokumen = null;
    $nama_file_asli_output = '';
    $path_file_terenkripsi_absolut = '';
    $kunci_tersimpan_db = '';
    $is_upload_mode = false;
    $nama_file_upload = '';

    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
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
        $kunci_tersimpan_db = $kunci_aes_dari_user;
    } elseif (isset($_POST['fileid']) && !empty($_POST['fileid'])) {
        $is_upload_mode = false;
        $id_dokumen = mysqli_real_escape_string($conn, $_POST['fileid']);
        
        $sql_file = "SELECT nama_asli_file, path_file_enkripsi, kunci_enkripsi, algoritma_enkripsi FROM dokumen_terenkripsi WHERE id_dokumen = ? AND username = ?";
        $stmt_file = mysqli_prepare($conn, $sql_file);
        if ($stmt_file) {
            mysqli_stmt_bind_param($stmt_file, "is", $id_dokumen, $_SESSION['username']);
            mysqli_stmt_execute($stmt_file);
            $result = mysqli_stmt_get_result($stmt_file);
            if ($result && $file_data = mysqli_fetch_assoc($result)) {
                if (strtoupper($file_data['algoritma_enkripsi']) !== 'AES-128') {
                    echo "<script>alert('DEKRIPSI GAGAL!\\nFile ini tidak dienkripsi dengan AES-128.'); window.history.back();</script>";
                    exit();
                }
                $nama_file_asli_output = $file_data['nama_asli_file'];
                $kunci_tersimpan_db = $file_data['kunci_enkripsi'];
                $path_file_terenkripsi_absolut = realpath(__DIR__ . DIRECTORY_SEPARATOR . $file_data['path_file_enkripsi']);
            } else {
                 echo "<script>alert('DEKRIPSI AES GAGAL!\\nDetail file tidak ditemukan.'); window.location.href='daftar_dokumen.php';</script>";
                 exit();
            }
            mysqli_stmt_close($stmt_file);
        }
    } else {
        echo "<script>alert('DEKRIPSI AES GAGAL!\\nTidak ada file yang dipilih atau ID dokumen tidak valid.'); window.location.href='dekripsi_aes128.php';</script>";
        exit();
    }
    
    if (!$is_upload_mode && ($kunci_aes_dari_user !== $kunci_tersimpan_db)) {
    echo "<script>
        alert('DEKRIPSI AES-128 GAGAL!\\nPassword yang Anda masukkan salah.');
        window.history.back();
    </script>"; 
    exit();
}
    
    $folder_hasil_dekripsi = __DIR__ . "/../hasil/Terdekripsi/";
    if (!is_dir($folder_hasil_dekripsi)) mkdir($folder_hasil_dekripsi, 0775, true);
    $path_hasil_dekripsi_absolut = $folder_hasil_dekripsi . time() . "_" . uniqid() . "_AES_" . $nama_file_asli_output;

    if ($path_file_terenkripsi_absolut && file_exists($path_file_terenkripsi_absolut)) {
        $file_sumber_enkripsi = fopen($path_file_terenkripsi_absolut, 'rb');
        $file_hasil_dekripsi = fopen($path_hasil_dekripsi_absolut, 'wb'); // Variabel $file_hasil_dekripsi didefinisikan di sini

        if ($file_sumber_enkripsi && $file_hasil_dekripsi) {
            
            // --- MULAI BLOK UTAMA PROSES DEKRIPSI ---
            $waktu_mulai_dekripsi = microtime(true);
            $aes = new AES($kunci_tersimpan_db);
            $decrypted_padded_content = '';
            
            $ciphertext = file_get_contents($path_file_terenkripsi_absolut);
            fclose($file_sumber_enkripsi);

            $berhasil_sepenuhnya = true;
            if (strlen($ciphertext) % 16 !== 0) {
                $berhasil_sepenuhnya = false;
            } else {
                $ciphertext_chunks = str_split($ciphertext, 16);
                foreach($ciphertext_chunks as $chunk) {
                    $decrypted_padded_content .= $aes->decryptBlock($chunk);
                }
            }
            
            $magic_number = "AES_v1_";
            $final_plaintext = null;
            if ($berhasil_sepenuhnya && strpos($decrypted_padded_content, $magic_number) === 0) {
                $decrypted_content_no_magic = substr($decrypted_padded_content, strlen($magic_number));
                $len = strlen($decrypted_content_no_magic);
                if ($len > 0) {
                    $pad = ord($decrypted_content_no_magic[$len - 1]);
                    if ($pad > 0 && $pad <= 16 && $len >= $pad) {
                        $final_plaintext = substr($decrypted_content_no_magic, 0, $len - $pad);
                    } else { $berhasil_sepenuhnya = false; }
                } else { $final_plaintext = ""; }
            } else {
                $berhasil_sepenuhnya = false;
            }
            
            if ($berhasil_sepenuhnya && $final_plaintext !== null) {
                if (fwrite($file_hasil_dekripsi, $final_plaintext) === false) { $berhasil_sepenuhnya = false; }
            } else { $berhasil_sepenuhnya = false; }
            
            // Variabel $file_hasil_dekripsi (sebelumnya $file_tujuan) masih ada dalam scope ini
            fclose($file_hasil_dekripsi);
            
            // Evaluasi akhir
            if ($berhasil_sepenuhnya) {
                $waktu_selesai_dekripsi = microtime(true);
                $durasi_detik_bulat_dek = round($waktu_selesai_dekripsi - $waktu_mulai_dekripsi, 2);
                $nama_file_hasil_dekripsi_final = basename($path_hasil_dekripsi_absolut);
                $ukuran_file_hasil_dekripsi_kb_val = round(filesize($path_hasil_dekripsi_absolut) / 1024, 2);
                $ukuran_file_enkripsi_kb = round(filesize($path_file_terenkripsi_absolut) / 1024, 2);

                if ($is_upload_mode) {
                    $sql_insert = "INSERT INTO dokumen_terenkripsi (username, nama_asli_file, nama_file_enkripsi, path_file_enkripsi, algoritma_enkripsi, kunci_enkripsi, tanggal_unggah, status_proses, durasi_dekripsi_detik, nama_file_terdekripsi, ukuran_file_terdekripsi_kb, ukuran_file_kb, ukuran_file_enkripsi_kb) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?)";
                    $stmt_insert = mysqli_prepare($conn, $sql_insert);
                    if ($stmt_insert) {
                        $path_enkrip_info = "diunggah: " . $nama_file_upload;
                        $ukuran_asli_info = $ukuran_file_enkripsi_kb;
                        $status_proses = "Terdekripsi";
                        $algoritma_db = "AES-128";
                        mysqli_stmt_bind_param($stmt_insert, "sssssssdsddd", $_SESSION['username'], $nama_file_asli_output, $nama_file_upload, $path_enkrip_info, $algoritma_db, $kunci_tersimpan_db, $status_proses, $durasi_detik_bulat_dek, $nama_file_hasil_dekripsi_final, $ukuran_file_hasil_dekripsi_kb_val, $ukuran_asli_info, $ukuran_file_enkripsi_kb);
                        mysqli_stmt_execute($stmt_insert);
                        $id_dokumen = mysqli_insert_id($conn);
                        mysqli_stmt_close($stmt_insert);
                    }
                } else {
                    $sql_update = "UPDATE dokumen_terenkripsi SET durasi_dekripsi_detik = ?, status_proses = ?, nama_file_terdekripsi = ?, ukuran_file_terdekripsi_kb = ? WHERE id_dokumen = ?";
                    $stmt_update = mysqli_prepare($conn, $sql_update);
                    if ($stmt_update) {
                        $status_terdekripsi = "Terdekripsi";
                        mysqli_stmt_bind_param($stmt_update, "dssdi", $durasi_detik_bulat_dek, $status_terdekripsi, $nama_file_hasil_dekripsi_final, $ukuran_file_hasil_dekripsi_kb_val, $id_dokumen);
                        mysqli_stmt_execute($stmt_update);
                        mysqli_stmt_close($stmt_update);
                    }
                }
                
                $session_id_key = $id_dokumen;
                $_SESSION['download_file_path_' . $session_id_key] = "hasil/Terdekripsi/" . $nama_file_hasil_dekripsi_final;
                $_SESSION['download_file_name_' . $session_id_key] = $nama_file_asli_output;

                $pesan_alert = "DEKRIPSI AES-128 BERHASIL!\\n\\nNama File Asli: " . addslashes($nama_file_asli_output) . "\\nFile Hasil Dekripsi: " . addslashes($nama_file_hasil_dekripsi_final) . "\\nUkuran Sebelum Dekripsi: " . $ukuran_file_enkripsi_kb . " KB\\nUkuran Setelah Dekripsi: " . $ukuran_file_hasil_dekripsi_kb_val . " KB\\nDurasi: " . round($durasi_detik_bulat_dek, 2) . " detik.";
                
                echo "<script>alert('" . $pesan_alert . "'); window.location.href='daftar_dokumen.php';</script>";
                exit();
            } else {
                if (file_exists($path_hasil_dekripsi_absolut)) { unlink($path_hasil_dekripsi_absolut); }
                echo "<script>
                    alert('DEKRIPSI GAGAL!\\nPassword salah, atau file yang diunggah bukan file AES-128 yang valid.');
                    window.history.back();
                </script>";
                exit();
            }

        } // AKHIR DARI BLOK if ($file_sumber_enkripsi && $file_hasil_dekripsi)
        else {
            echo "<script>alert('DEKRIPSI AES-128 GAGAL!\\nTidak bisa membuka file terenkripsi atau membuat file hasil.');</script>";
            exit();
        }
    } else {
        echo "<script>alert('DEKRIPSI AES-128 GAGAL!\\nFile sumber tidak ditemukan di server.');</script>";
        exit();
    }
} else {
    echo "<script>window.alert('Akses tidak valid ke halaman proses dekripsi!'); window.location.href='daftar_dokumen.php';</script>";
    exit();
}

if (isset($conn)) { mysqli_close($conn); }
?>