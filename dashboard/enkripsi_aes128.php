<?php
// Definisikan variabel spesifik untuk halaman ini SEBELUM meng-include header
$currentPageTitle = "Enkripsi File";
$pageSpecificCss = "encrypt-style.css"; // File CSS khusus untuk halaman ini

// Memanggil file header.php (yang sudah ada session_start() dan cek login)
include '../includes/header.php'; 

    // Memanggil file navigasi
    include '../includes/navigation.php'; 
?>

        <div class="main">
            <?php include '../includes/topbar.php'; // Memanggil file topbar ?>

            <div class="form-container">
                <div class="form-card">
                    <div class="cardHeader">
                        <h2>Formulir Enkripsi File AES-128</h2>
                    </div>

                    <form action="proses_enkripsi_aes128.php" method="POST" enctype="multipart/form-data" id="encryptionForm">
                        
                        <div class="form-group">
                            <label for="fileToEncrypt">Pilih File untuk Dienkripsi:</label>
                            <input type="file" name="file" id="fileToEncrypt" class="form-control-file" required>
                            <small class="form-text text-muted">Format yang diizinkan: docx, doc, txt, pdf, xls, xlsx, ppt, pptx, jpg, jpeg, png, gif, mp3, mp4, mov, mpg. Maks 8MB.</small>
                        </div>

                        <div class="form-group">
                            <label for="encryptionPassword">Password Enkripsi:</label>
                            <input type="password" name="pwdfile" id="encryptionPassword" class="form-control" placeholder="Masukkan Password" required>
                            <small class="form-text text-muted">Password ini akan digunakan untuk menghasilkan kunci enkripsi 128-bit.</small>
                        </div>

                        <div class="form-group">
                            <label for="fileDescription">Deskripsi File (Opsional):</label>
                            <textarea name="desc" id="fileDescription" class="form-control" rows="3" placeholder="Deskripsi singkat mengenai file..."></textarea>
                        </div>
                        
                        <div class="form-group button-group">
                            <button type="submit" name="encrypt_now" class="btn btn-primary">Enkripsi File Sekarang</button>
                            <button type="reset" class="btn btn-secondary">Reset Form</button>
                        </div>

                        <div id="encryptionStatus" class="mt-3"></div>
                    </form>
                </div>
            </div>
            </div> </div> <script src="../assets/js/dashboard.js"></script> 
</body>
</html>