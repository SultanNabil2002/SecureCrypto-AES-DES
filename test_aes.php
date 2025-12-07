<?php
// Atur error reporting untuk melihat semua masalah selama pengujian
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Sertakan file kelas AES.php yang baru Anda buat
// Pastikan path ini benar. Jika test_aes.php ada di root Kriptografi-Aes/, 
// dan AES.php ada di Kriptografi-Aes/includes/, maka pathnya 'includes/AES.php'
require_once 'includes/AES.php'; 

// Plaintext dan Kunci yang Anda tentukan
$kunci_aes128 = "KRIPTOGRAFIAESKU";      // 16 byte
$plaintext_asli = "ARIANDOHAREDISON";    // 16 byte

echo "<h2>Pengujian AES-128 Block Cipher</h2>";
echo "Plaintext Asli: \"" . htmlspecialchars($plaintext_asli) . "\" (Panjang: " . strlen($plaintext_asli) . " byte)<br>";
echo "Kunci Digunakan: \"" . htmlspecialchars($kunci_aes128) . "\" (Panjang: " . strlen($kunci_aes128) . " byte)<br><br>";

// Validasi panjang kunci (penting untuk konstruktor AES)
if (strlen($kunci_aes128) !== 16) {
    die("Error: Kunci harus tepat 16 byte untuk implementasi AES-128 ini.");
}

// Validasi panjang plaintext (karena encryptBlock() sekarang mengharapkan 16 byte)
if (strlen($plaintext_asli) !== 16) {
    die("Error: Plaintext untuk encryptBlock() harus tepat 16 byte untuk pengujian ini.");
}

try {
    // 1. Buat instance dari kelas AES dengan kunci yang ditentukan
    // Konstruktor __construct($key) akan dipanggil dan keyExpansion() akan dijalankan.
    $aes = new AES($kunci_aes128);

    // 2. Lakukan Enkripsi
    echo "<h3>Proses Enkripsi:</h3>";
    $ciphertext_block = $aes->encryptBlock($plaintext_asli);
    
    if ($ciphertext_block !== false && strlen($ciphertext_block) === 16) {
        echo "Ciphertext (Raw): " . htmlspecialchars($ciphertext_block) . "<br>";
        echo "Ciphertext (Hex): <span style='font-family: monospace; background-color: #f0f0f0; padding: 2px 5px;'>" . strtoupper(bin2hex($ciphertext_block)) . "</span><br>";
        echo "Ciphertext (Base64): " . base64_encode($ciphertext_block) . "<br><br>";

        // --- PERBANDINGAN DENGAN GAMBAR ANDA ---
        $expected_hex_from_image = "4846A719D08073E81AB8F4FB483B423D"; // Dari gambar Anda
        echo "Ciphertext Hex yang Diharapkan (dari gambar): <span style='font-family: monospace; background-color: #f0f0f0; padding: 2px 5px;'>" . $expected_hex_from_image . "</span><br>";
        if (strtoupper(bin2hex($ciphertext_block)) === $expected_hex_from_image) {
            echo "<strong style='color:green;'>VERIFIKASI GAMBAR: Ciphertext Hex SAMA dengan gambar Anda!</strong><br><br>";
        } else {
            echo "<strong style='color:red;'>VERIFIKASI GAMBAR: Ciphertext Hex BERBEDA dengan gambar Anda.</strong> Ini bisa terjadi jika implementasi (misalnya, tabel GF atau detail kecil) sedikit berbeda, atau gambar Anda dari implementasi AES lain. Yang penting adalah konsistensi internal enkripsi-dekripsi kelas ini.<br><br>";
        }

        // 3. Lakukan Dekripsi
        echo "<h3>Proses Dekripsi:</h3>";
        $decrypted_plaintext_block = $aes->decryptBlock($ciphertext_block);

        if ($decrypted_plaintext_block !== false) {
            echo "Plaintext Hasil Dekripsi (Raw): \"" . htmlspecialchars($decrypted_plaintext_block) . "\" (Panjang: " . strlen($decrypted_plaintext_block) . " byte)<br><br>";

            // 4. Verifikasi Hasil
            echo "<h3>Verifikasi Akhir:</h3>";
            if ($plaintext_asli === $decrypted_plaintext_block) {
                echo "<strong style='color:green; font-size: 1.2em;'>HASIL: Enkripsi dan Dekripsi BERHASIL! Plaintext kembali sama persis.</strong><br>";
            } else {
                echo "<strong style='color:red; font-size: 1.2em;'>HASIL: Enkripsi dan Dekripsi GAGAL! Plaintext tidak kembali sama.</strong><br>";
                echo "Detail Perbedaan:<br>";
                echo "Plaintext Asli (Hex): " . bin2hex($plaintext_asli) . "<br>";
                echo "Hasil Dekripsi (Hex): " . bin2hex($decrypted_plaintext_block) . "<br>";
            }
        } else {
            echo "<strong style='color:red;'>Dekripsi GAGAL menghasilkan output.</strong><br>";
        }
    } else {
        echo "<strong style='color:red;'>Enkripsi GAGAL menghasilkan output yang valid (16 byte).</strong><br>";
        echo "Output enkripsi (var_dump): ";
        var_dump($ciphertext_block);
    }

} catch (Exception $e) {
    echo "Terjadi error PHP Exception: " . htmlspecialchars($e->getMessage());
}
?>