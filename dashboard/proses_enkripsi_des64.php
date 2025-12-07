<?php
// File: Kriptografi-Aes/dashboard/proses_enkripsi_des64.php

// Memulai atau melanjutkan sesi PHP.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Error reporting untuk development.
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include file yang dibutuhkan
require_once '../config/config.php';
require_once '../includes/DES.php';

// Pengecekan koneksi database
if (!$conn) {
    echo "<script>alert('ENKRIPSI DES GAGAL! Tidak bisa terhubung ke database.'); window.location.href='enkripsi_des64.php';</script>";
    exit();
}

// Pengecekan sesi login
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Akses ditolak! Anda harus login terlebih dahulu.'); window.location.href='../index.php';</script>";
    exit();
}

// Memeriksa apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['encrypt_des_now'])) {

    // Mengambil semua input dari form dan sesi
    $username_pengguna = $_SESSION['username'];
    $kunci_des_64_bit = substr(md5($_POST["pwdfile"]), 0, 8);
    $deskripsi_file = isset($_POST['desc']) ? mysqli_real_escape_string($conn, $_POST['desc']) : '';
    $algoritma_yang_digunakan = "DES-64";

    // Validasi file upload awal
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        
        $file_tmp_path    = $_FILES['file']['tmp_name'];
        $nama_file_asli   = $_FILES['file']['name'];
        $ukuran_file_byte_asli = $_FILES['file']['size'];
        $ukuran_file_kb_asli   = round($ukuran_file_byte_asli / 1024, 2);

        $info_file_asli   = pathinfo($nama_file_asli);
        $ekstensi_file    = isset($info_file_asli['extension']) ? strtolower($info_file_asli['extension']) : '';
        
        $nama_file_tanpa_ekstensi_aman = preg_replace("/[^a-zA-Z0-9._-]/", "_", $info_file_asli['filename']);
        $nama_file_enkripsi_unik = time() . "_" . uniqid() . "_DES_" . $nama_file_tanpa_ekstensi_aman . "." . $ekstensi_file . ".enc";
        
        $path_hasil_enkripsi_relatif = "../hasil/Terenkripsi/" . $nama_file_enkripsi_unik;
        $path_hasil_enkripsi_absolut = __DIR__ . "/../hasil/Terenkripsi/" . $nama_file_enkripsi_unik;

        // Validasi format file yang diizinkan
        $ekstensi_diizinkan = array("docx", "doc", "txt", "pdf", "xls", "xlsx", "ppt", "pptx", "jpg", "jpeg", "png", "gif", "mp3", "mp4", "mov", "mpg");
        if (!in_array($ekstensi_file, $ekstensi_diizinkan)) {
            echo ("<script>window.alert('Format file tidak diizinkan.'); window.location.href='enkripsi_des64.php';</script>");
            exit();
        }

        // Validasi ukuran file
        if ($ukuran_file_kb_asli > 8192) { // Batas 8MB
            echo ("<script>window.alert('Ukuran file tidak boleh lebih besar dari 8MB.'); window.location.href='enkripsi_des64.php';</script>");
            exit();
        }

        // ... (lanjutan di Part 2)
// Melanjutkan dari Part 1 (setelah validasi file selesai)
// ...

        $waktu_mulai = microtime(true);

        // Menyiapkan query INSERT untuk mencatat metadata file sebelum enkripsi.
        // Kolom untuk hasil enkripsi/dekripsi (seperti ukuran, durasi, nama file) akan diisi NULL pada tahap ini.
        $sql_insert = "INSERT INTO dokumen_terenkripsi 
                       (username, nama_asli_file, nama_file_enkripsi, path_file_enkripsi, ukuran_file_kb, kunci_enkripsi, algoritma_enkripsi, tanggal_unggah, status_proses, deskripsi_file) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)";
        
        $stmt_insert = mysqli_prepare($conn, $sql_insert);
        if (!$stmt_insert) {
             echo "<script>
                alert('ENKRIPSI DES GAGAL!\\nError database (INSERT prepare): " . addslashes(mysqli_error($conn)) . "');
                window.location.href='enkripsi_des64.php';
            </script>";
            exit();
        }
        
        $status_awal = "Proses Enkripsi";
        // Mengikat variabel PHP ke placeholder '?' dalam statement SQL.
        // Tipe data: s(string), d(double/decimal). Total 9 parameter.
        mysqli_stmt_bind_param($stmt_insert, "ssssdssss", 
            $username_pengguna, 
            $nama_file_asli,
            $nama_file_enkripsi_unik,
            $path_hasil_enkripsi_relatif,
            $ukuran_file_kb_asli,
            $kunci_des_64_bit,
            $algoritma_yang_digunakan,
            $status_awal,
            $deskripsi_file
        );

        // Eksekusi query INSERT. Jika berhasil, lanjutkan ke proses enkripsi file.
        if (mysqli_stmt_execute($stmt_insert)) {
            $id_dokumen_baru = mysqli_insert_id($conn);
            
            // ... (lanjutan di Part 3: Proses Enkripsi Inti) ...
// Melanjutkan dari Part 2 (di dalam blok "if (mysqli_stmt_execute($stmt_insert))")
// ...

            $id_dokumen_baru = mysqli_insert_id($conn);
            
            // --- PROSES ENKRIPSI FILE FISIK ---

            // Buka file tujuan untuk ditulis (mode 'wb' untuk binary write)
            $file_hasil_enkripsi = fopen($path_hasil_enkripsi_absolut, 'wb');

            // Pastikan file tujuan berhasil dibuat/dibuka
            if ($file_hasil_enkripsi) {
                // Pengaturan server untuk file besar
                ini_set('max_execution_time', 0);
                ini_set('memory_limit', '-1');

                // Baca seluruh konten file asli ke dalam memori
                $konten_asli = file_get_contents($file_tmp_path);
                
                // 1. Tambahkan "Magic Number" sebagai tanda pengenal file DES dari sistem ini
                $magic_number = "DES_v1_";
                $data_untuk_enkripsi = $magic_number . $konten_asli;
                
                // 2. Terapkan PKCS#7 Padding secara manual ke seluruh data
                $block_size = 8; // Ukuran blok untuk DES
                $panjang_data = strlen($data_untuk_enkripsi);
                $padding_needed = $block_size - ($panjang_data % $block_size);
                // Jika panjang data sudah kelipatan block_size, PKCS#7 akan menambahkan satu blok padding penuh
                if ($padding_needed == 0) {
                    $padding_needed = $block_size;
                }
                $padding_char = chr($padding_needed);
                $data_untuk_enkripsi .= str_repeat($padding_char, $padding_needed);
                
                // 3. Enkripsi data yang sudah dipad, blok per blok
                $des = new DES($kunci_des_64_bit);
                $berhasil_sepenuhnya = true;
                $ciphertext_final = '';
                
                $total_bytes = strlen($data_untuk_enkripsi);
                for ($i = 0; $i < $total_bytes; $i += $block_size) {
                    $chunk = substr($data_untuk_enkripsi, $i, $block_size);
                    // Panggil metode enkripsi dari kelas DES Anda
                    $ciphertext_final .= $des->encryptBlock($chunk);
                }
                
                // 4. Tulis hasil enkripsi ke file tujuan
                if (fwrite($file_hasil_enkripsi, $ciphertext_final) === false) {
                    $berhasil_sepenuhnya = false;
                }
                
                // Selalu tutup file setelah selesai
                fclose($file_hasil_enkripsi);

            } else { // Jika gagal membuka file tujuan
                $berhasil_sepenuhnya = false;
                // Hapus record DB jika sudah terlanjur insert
                if (isset($id_dokumen_baru)) {
                    $sql_delete = "DELETE FROM dokumen_terenkripsi WHERE id_dokumen = ?";
                    $stmt_delete = mysqli_prepare($conn, $sql_delete);
                    if($stmt_delete) {
                        mysqli_stmt_bind_param($stmt_delete, "i", $id_dokumen_baru);
                        mysqli_stmt_execute($stmt_delete);
                        mysqli_stmt_close($stmt_delete);
                    }
                }
                 echo "<script>
                    alert('ENKRIPSI DES GAGAL!\\nTidak bisa membuat file tujuan di server.');
                    window.location.href='enkripsi_des64.php';
                </script>";
                exit();
            }
            // --- AKHIR PROSES ENKRIPSI FILE FISIK ---

            // ... (lanjutan di Part 4: Penanganan Hasil Proses) ...
// Melanjutkan dari Part 3 (setelah proses enkripsi file dan fclose())
// ...

            // --- EVALUASI AKHIR SETELAH PROSES ENKRIPSI ---
            // Cek apakah proses berhasil DAN (file output memiliki isi ATAU file input memang kosong)
            if ($berhasil_sepenuhnya && (filesize($path_hasil_enkripsi_absolut) > 0 || filesize($file_tmp_path) == 0) ) {
                
                // Jika berhasil, hitung metrik akhir
                $waktu_selesai = microtime(true);
                $durasi_detik_bulat = round($waktu_selesai - $waktu_mulai, 2);
                
                $ukuran_file_enkripsi_byte = filesize($path_hasil_enkripsi_absolut);
                $ukuran_file_enkripsi_kb_val = round($ukuran_file_enkripsi_byte / 1024, 2);

                // Format pesan durasi untuk ditampilkan di alert
                $total_detik_floor = floor($durasi_detik_bulat);
                $menit = floor($total_detik_floor / 60);
                $detik = $total_detik_floor % 60;
                $pesan_durasi = "";
                if ($menit > 0) { $pesan_durasi .= $menit . " menit "; }
                $pesan_durasi .= $detik . " detik (Total: " . $durasi_detik_bulat . "s)";

                // Update Database dengan durasi, status "Terenkripsi", dan ukuran file .enc
                $sql_update = "UPDATE dokumen_terenkripsi 
                               SET durasi_enkripsi_detik = ?, status_proses = ?, ukuran_file_enkripsi_kb = ?
                               WHERE id_dokumen = ?";
                $stmt_update = mysqli_prepare($conn, $sql_update);
                
                if ($stmt_update) {
                    $status_selesai = "Terenkripsi";
                    // Tipe data untuk bind_param: d (double untuk durasi), s (string untuk status), d (double untuk ukuran), i (integer untuk id)
                    mysqli_stmt_bind_param($stmt_update, "dsdi", 
                        $durasi_detik_bulat, 
                        $status_selesai, 
                        $ukuran_file_enkripsi_kb_val, 
                        $id_dokumen_baru
                    );
                    mysqli_stmt_execute($stmt_update);
                    mysqli_stmt_close($stmt_update);
                    
                    // Tampilkan alert sukses
                    echo "<script>
                        alert('ENKRIPSI " . $algoritma_yang_digunakan . " BERHASIL!\\n\\nFile Asli: " . addslashes($nama_file_asli) . "\\nFile Enkripsi: " . addslashes($nama_file_enkripsi_unik) . "\\nDurasi: " . $pesan_durasi . "');
                        window.location.href='enkripsi_des64.php';
                    </script>";
                    exit();
                } else {
                     echo "<script>
                        alert('ENKRIPSI " . $algoritma_yang_digunakan . " BERHASIL, TAPI GAGAL UPDATE DATABASE!\\nError: " . addslashes(mysqli_error($conn)) . "');
                        window.location.href='enkripsi_des64.php';
                    </script>";
                    exit();
                }
            } else { // Jika $berhasil_sepenuhnya adalah false (ada kegagalan saat enkripsi atau tulis file)
                // Hapus file parsial yang mungkin sudah terbuat
                if (file_exists($path_hasil_enkripsi_absolut)) { unlink($path_hasil_enkripsi_absolut); }
                // Hapus record DB yang sudah di-INSERT di awal untuk menjaga kebersihan data
                if (isset($id_dokumen_baru)) {
                    $sql_delete_on_fail = "DELETE FROM dokumen_terenkripsi WHERE id_dokumen = ?";
                    $stmt_delete = mysqli_prepare($conn, $sql_delete_on_fail);
                    if ($stmt_delete) {
                        mysqli_stmt_bind_param($stmt_delete, "i", $id_dokumen_baru);
                        mysqli_stmt_execute($stmt_delete);
                        mysqli_stmt_close($stmt_delete);
                    }
                }
                echo "<script>
                        alert('ENKRIPSI " . $algoritma_yang_digunakan . " GAGAL!\\nTerjadi masalah saat proses enkripsi atau menulis file.');
                        window.location.href='enkripsi_des64.php';
                    </script>";
                exit(); 
            }
// Tutup kurung kurawal untuk blok if (mysqli_stmt_execute($stmt_insert))
} else { 
    echo "<script>
        alert('ENKRIPSI " . $algoritma_yang_digunakan . " GAGAL!\\nError database (INSERT execute): " . addslashes(mysqli_stmt_error($stmt_insert)) . "');
        window.location.href='enkripsi_des64.php';
    </script>";
    exit();
}
if ($stmt_insert) mysqli_stmt_close($stmt_insert);

// Tutup kurung kurawal untuk blok if (isset($_FILES['file']) ...)
} else { 
    $error_msg = "Tidak ada file yang diunggah atau terjadi error saat proses upload awal.";
    if (isset($_FILES['file']['error']) && $_FILES['file']['error'] != UPLOAD_ERR_NO_FILE) {
        $upload_errors = [ // Menggunakan sintaks array singkat []
            UPLOAD_ERR_INI_SIZE   => "File melebihi batas ukuran upload server (upload_max_filesize di php.ini).",
            UPLOAD_ERR_FORM_SIZE  => "File melebihi batas ukuran yang ditentukan di form HTML (MAX_FILE_SIZE).",
            UPLOAD_ERR_PARTIAL    => "File hanya terupload sebagian.",
            UPLOAD_ERR_NO_TMP_DIR => "Folder temporary untuk upload tidak ditemukan di server.",
            UPLOAD_ERR_CANT_WRITE => "Gagal menulis file ke disk server.",
            UPLOAD_ERR_EXTENSION  => "Ekstensi PHP menghentikan proses upload file.",
        ];
        $error_code = $_FILES['file']['error'];
        $error_msg = $upload_errors[$error_code] ?? "Error upload tidak diketahui (kode: $error_code).";
    }
    echo ("<script>
        window.alert('ENKRIPSI " . ($algoritma_yang_digunakan ?? 'DES') . " GAGAL!\\n" . addslashes($error_msg) . "');
        window.location.href='enkripsi_des64.php';
    </script>");
    exit();
}

// Tutup kurung kurawal untuk blok if ($_SERVER["REQUEST_METHOD"] == "POST" ...)
} else { 
    echo ("<script>
        window.alert('Akses tidak valid ke halaman proses enkripsi!');
        window.location.href='enkripsi_des64.php';
    </script>");
    exit();
}

if (isset($conn)) {
    mysqli_close($conn);
}
?>