<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/DES.php'; 

$kunci_des = "kuncides"; // Tepat 8 byte
$plaintext_des = "tesdata1"; // Tepat 8 byte

echo "<h2>Pengujian DES Block Cipher</h2>";
echo "Plaintext Asli: \"" . htmlspecialchars($plaintext_des) . "\"<br>";
echo "Kunci Digunakan: \"" . htmlspecialchars($kunci_des) . "\"<br><br>";

if (strlen($kunci_des) !== 8) {
    die("Error: Kunci DES harus 8 byte.");
}
if (strlen($plaintext_des) !== 8) {
    die("Error: Plaintext DES untuk test_des.php ini harus 8 byte.");
}

try {
    $des = new DES($kunci_des);

    echo "<h3>Proses Enkripsi DES:</h3>";
    $ciphertext_des = $des->encryptBlock($plaintext_des);
    echo "Ciphertext DES (Hex): <span style='font-family: monospace; background-color: #f0f0f0; padding: 2px 5px;'>" . strtoupper(bin2hex($ciphertext_des)) . "</span><br><br>";

    echo "<h3>Proses Dekripsi DES:</h3>";
    $decrypted_plaintext_des = $des->decryptBlock($ciphertext_des);
    echo "Plaintext Hasil Dekripsi DES: \"" . htmlspecialchars($decrypted_plaintext_des) . "\"<br><br>";

    echo "<h3>Verifikasi Akhir DES:</h3>";
    if ($plaintext_des === $decrypted_plaintext_des) {
        echo "<strong style='color:green; font-size: 1.2em;'>HASIL DES: Enkripsi dan Dekripsi BERHASIL! Plaintext kembali sama persis.</strong><br>";
    } else {
        echo "<strong style='color:red; font-size: 1.2em;'>HASIL DES: Enkripsi dan Dekripsi GAGAL! Plaintext tidak kembali sama.</strong><br>";
    }

} catch (Exception $e) {
    echo "Terjadi error PHP Exception: " . htmlspecialchars($e->getMessage());
}
?>