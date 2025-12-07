<?php 
$absolutePathToConfig = realpath(__DIR__ . '/../config/config.php');

if ($absolutePathToConfig && file_exists($absolutePathToConfig)) {
    require_once $absolutePathToConfig;
} else {
    // Jika realpath() atau file_exists() gagal untuk path yang sudah di-debug sebelumnya berhasil,
    // ini sangat aneh. Mungkin ada masalah sementara.
    die("Error Kritis: Tidak bisa menemukan atau memvalidasi path ke config.php dari functions.php. Path yang dicoba: " . __DIR__ . '/../config/config.php');
}

function registrasi($data) {
    global $conn;
    $username = strtolower(stripslashes($data["username"]));
    $password = mysqli_real_escape_string($conn, $data["password"]);
    $confirm_password = mysqli_real_escape_string($conn, $data["confirm_password"]);
    $role = mysqli_real_escape_string($conn, $data["role"]);
    date_default_timezone_set('Asia/Jakarta');
    $created_at = date("Y-m-d H:i:s");

    $result = mysqli_query($conn, "SELECT username FROM user WHERE username = '$username'");
    // cek username sudah terdaftar atau belum.
    if( mysqli_fetch_assoc($result) ) {
        echo "<script>
                alert('username yang dipilih sudah terdaftar!');   
            </script>";
            return false; // ingat ini supaya kode yang dibawah nggak dijalankan
    }

    // Cek apakah password dan konfirmasi password sama.
    if( $password !== $confirm_password ) {
        echo "<script>
                alert('konfirmasi password tidak sesuai!');
            </script>";
        return false;
    }

    // enkripsi password
    $password = password_hash($password, PASSWORD_DEFAULT);

    // insert data ke database.
    $query = "INSERT INTO user ( id, username, password, role, created_at)
              VALUES ('', '$username', '$password', '$role', '$created_at')";
    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}

// --- FUNGSI UNTUK DOWNLOAD FILE (VERSI TERBARU) ---
/**
 * Menangani proses download file (baik terenkripsi maupun hasil dekripsi).
 * @param mysqli $conn Objek koneksi database.
 * @param int $id_dokumen ID dokumen yang akan didownload.
 * @param string $username Username pengguna yang login (untuk verifikasi kepemilikan).
 * @param string $tipe_file Tipe file ('enkripsi' untuk file .enc, 'dekripsi' untuk file hasil dekripsi).
 * @return bool False jika gagal, atau skrip akan exit setelah download berhasil.
 */
function handleFileDownload($conn, $id_dokumen, $username, $tipe_file) {
    // Validasi input dasar
    if (empty($id_dokumen) || empty($username) || !in_array($tipe_file, ['enkripsi', 'dekripsi'])) {
        error_log("Parameter tidak lengkap atau tipe file tidak valid untuk download. ID: $id_dokumen, User: $username, Tipe: $tipe_file");
        return false;
    }

    // Ambil detail file dari database
    // Kita butuh nama_asli_file (untuk nama download), path_file_enkripsi (jika tipe='enkripsi'),
    // dan nama_file_terdekripsi (jika tipe='dekripsi')
    $sql = "SELECT nama_asli_file, nama_file_enkripsi, path_file_enkripsi, nama_file_terdekripsi 
            FROM dokumen_terenkripsi 
            WHERE id_dokumen = ? AND username = ?";
    
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        error_log("Gagal prepare statement SELECT untuk download: " . mysqli_error($conn));
        return false;
    }

    mysqli_stmt_bind_param($stmt, "is", $id_dokumen, $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) == 1) {
        $file_data = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        $path_ke_file_fisik_absolut = ''; // Path absolut ke file di server
        $nama_file_untuk_download_pengguna = ''; // Nama file yang akan dilihat pengguna saat download

        // Dapatkan root direktori proyek. functions.php ada di Kriptografi-Aes/includes/,
        // jadi dirname(__DIR__) akan menghasilkan Kriptografi-Aes/
        $project_root = dirname(__DIR__);

        if ($tipe_file === 'enkripsi') {
            if (!empty($file_data['path_file_enkripsi'])) {
                // path_file_enkripsi di DB adalah relatif dari folder 'dashboard/', misal: ../hasil/Terenkripsi/namafile.enc
                // Path absolutnya menjadi: Kriptografi-Aes/dashboard/../hasil/Terenkripsi/namafile.enc
                // yang akan di-resolve oleh realpath() menjadi: Kriptografi-Aes/hasil/Terenkripsi/namafile.enc
                $path_konstruksi = $project_root . DIRECTORY_SEPARATOR . 'dashboard' . DIRECTORY_SEPARATOR . $file_data['path_file_enkripsi'];
                $path_ke_file_fisik_absolut = realpath($path_konstruksi);
                $nama_file_untuk_download_pengguna = $file_data['nama_file_enkripsi']; // Pengguna download file .enc
            } else {
                error_log("Path file enkripsi kosong di DB untuk ID: " . $id_dokumen);
                return false;
            }
        } elseif ($tipe_file === 'dekripsi') {
            if (!empty($file_data['nama_file_terdekripsi'])) {
                // nama_file_terdekripsi adalah nama file unik yang disimpan di folder hasil/Terdekripsi/
                $path_ke_file_fisik_absolut = $project_root . DIRECTORY_SEPARATOR . 'hasil' . DIRECTORY_SEPARATOR . 'Terdekripsi' . DIRECTORY_SEPARATOR . $file_data['nama_file_terdekripsi'];
                $nama_file_untuk_download_pengguna = $file_data['nama_file_terdekripsi'];
            } else {
                error_log("Nama file terdekripsi terakhir kosong di DB untuk ID: " . $id_dokumen . ". File mungkin belum pernah didekripsi atau gagal disimpan.");
                return false; 
            }
        } else {
            error_log("Tipe file tidak dikenali untuk download: " . $tipe_file);
            return false;
        }
        
        // --- DEBUGGING PATH (Aktifkan jika masih gagal download) ---
        /*
        echo "<pre>";
        echo "Tipe Download: " . htmlspecialchars($tipe_file) . "<br>";
        echo "Path Konstruksi (jika ada): " . (isset($path_konstruksi) ? htmlspecialchars($path_konstruksi) : "N/A") . "<br>";
        echo "Path Absolut Terhitung: "; var_dump($path_ke_file_fisik_absolut);
        echo "Nama File untuk Pengguna: "; var_dump($nama_file_untuk_download_pengguna);
        if ($path_ke_file_fisik_absolut) {
            echo "File Exists? "; var_dump(file_exists($path_ke_file_fisik_absolut));
            echo "File is Readable? "; var_dump(is_readable($path_ke_file_fisik_absolut));
            echo "Filesize: "; var_dump(file_exists($path_ke_file_fisik_absolut) ? filesize($path_ke_file_fisik_absolut) : 'File tidak ada');
        }
        die("Debug download path selesai.");
        */
        // --- AKHIR DEBUGGING ---

        if ($path_ke_file_fisik_absolut && file_exists($path_ke_file_fisik_absolut) && is_readable($path_ke_file_fisik_absolut)) {
            // Pastikan tidak ada output lain sebelum header()
            if (ob_get_level()) {
                ob_end_clean(); 
            }
            
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream'); // Tipe MIME generik
            header('Content-Disposition: attachment; filename="' . basename($nama_file_untuk_download_pengguna) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($path_ke_file_fisik_absolut));
            
            if (readfile($path_ke_file_fisik_absolut) !== false) {
                exit; 
            } else {
                error_log("Gagal readfile untuk: " . $path_ke_file_fisik_absolut);
                // Beri tahu pengguna bahwa ada masalah internal jika readfile gagal
                // (meskipun file ada dan bisa dibaca, ini jarang terjadi)
                echo "<script>alert('Terjadi kesalahan internal saat mencoba mengirim file.'); window.history.back();</script>";
                exit();
            }
        } else {
            error_log("File tidak ditemukan atau tidak bisa dibaca untuk download. Path terhitung: " . ($path_ke_file_fisik_absolut ? $path_ke_file_fisik_absolut : "Path tidak valid dari realpath") . (isset($path_konstruksi) ? ". Path konstruksi: " . $path_konstruksi : ""));
            return false;
        }
    } else {
        error_log("Data file tidak ditemukan di DB untuk download (ID: " . $id_dokumen . ", User: " . $username . ") atau bukan milik user. Error DB: " . mysqli_error($conn));
        if ($stmt) mysqli_stmt_close($stmt);
        return false;
    }
    // Seharusnya tidak pernah sampai sini jika download berhasil (karena ada exit) atau return false di atas.
    return false; 
}


// --- FUNGSI BARU UNTUK HAPUS ---
/**
 * Menangani proses hapus file (data dari DB dan file fisik).
 * @param mysqli $conn Objek koneksi database.
 * @param int $id_dokumen ID dokumen yang akan dihapus.
 * @param string $username Username pengguna yang login (untuk verifikasi kepemilikan).
 * @return bool True jika berhasil, false jika gagal.
 */
function handleFileDelete($conn, $id_dokumen, $username) {
    if (empty($id_dokumen) || empty($username)) {
        return false;
    }

    $id_dokumen = mysqli_real_escape_string($conn, $id_dokumen);

    // 1. Ambil path file terenkripsi dari database untuk dihapus dari server
    $sql_select = "SELECT path_file_enkripsi, nama_asli_file FROM dokumen_terenkripsi WHERE id_dokumen = ? AND username = ?";
    $stmt_select = mysqli_prepare($conn, $sql_select);

    if (!$stmt_select) {
        error_log("Gagal prepare statement SELECT untuk hapus: " . mysqli_error($conn));
        return false;
    }

    mysqli_stmt_bind_param($stmt_select, "is", $id_dokumen, $username);
    mysqli_stmt_execute($stmt_select);
    $result_select = mysqli_stmt_get_result($stmt_select);

    if ($result_select && mysqli_num_rows($result_select) == 1) {
        $file_data = mysqli_fetch_assoc($result_select);
        $path_file_enkripsi_relatif = $file_data['path_file_enkripsi'];
        // $nama_file_asli_untuk_hapus_dekripsi = $file_data['nama_asli_file']; // Simpan untuk menghapus file dekripsi

        mysqli_stmt_close($stmt_select); // Tutup statement SELECT

        // Hapus file terenkripsi fisik
        // Path di DB: ../hasil/Terenkripsi/namafile.enc
        // Path absolut: __DIR_dari_skrip_pemanggil . / . path_relatif_db
        $path_file_enkripsi_absolut = realpath(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'dashboard' . DIRECTORY_SEPARATOR . $path_file_enkripsi_relatif);
        if ($path_file_enkripsi_absolut && file_exists($path_file_enkripsi_absolut)) {
            unlink($path_file_enkripsi_absolut);
        }

        // Hapus juga file terdekripsi jika ada (dengan pola nama atau path dari DB jika disimpan)
        // Ini bagian yang lebih kompleks karena nama file dekripsi kita buat unik dengan timestamp.
        // Untuk implementasi sederhana, kita bisa scan folder hasil/Terdekripsi/
        // dan hapus file yang mengandung nama_asli_file dari record ini.
        // Namun, ini bisa berisiko jika ada file lain dengan nama serupa.
        // Cara yang lebih baik adalah jika proses_dekripsi_aes128.php menyimpan path file dekripsi terakhir ke DB
        // atau jika Anda ingin menghapus semua file di folder Terdekripsi yang berkaitan dengan id_dokumen ini (jika ada konvensi penamaan).
        // Untuk saat ini, kita akan fokus menghapus file terenkripsi dan record DB.
        // Anda bisa tambahkan logika hapus file terdekripsi di sini jika Anda punya cara melacaknya.

        // 2. Hapus record dari database
        $sql_delete = "DELETE FROM dokumen_terenkripsi WHERE id_dokumen = ? AND username = ?";
        $stmt_delete = mysqli_prepare($conn, $sql_delete);
        if (!$stmt_delete) {
            error_log("Gagal prepare statement DELETE: " . mysqli_error($conn));
            return false;
        }
        mysqli_stmt_bind_param($stmt_delete, "is", $id_dokumen, $username);
        if (mysqli_stmt_execute($stmt_delete)) {
            mysqli_stmt_close($stmt_delete);
            return true; // Berhasil hapus record
        } else {
            error_log("Gagal eksekusi statement DELETE: " . mysqli_stmt_error($stmt_delete));
            mysqli_stmt_close($stmt_delete);
            return false;
        }
    } else {
        // File tidak ditemukan atau bukan milik pengguna
        if ($stmt_select) mysqli_stmt_close($stmt_select);
        return false;
    }
}

?>