<?php
// Definisikan variabel spesifik untuk halaman ini SEBELUM meng-include header
$currentPageTitle = "Formulir Enkripsi File DES";
$pageSpecificCss = "encrypt-style.css"; // Menggunakan CSS yang sama dengan form enkripsi AES

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
                        <h2><?php echo htmlspecialchars($currentPageTitle); ?> (Blok 64-bit)</h2>
                    </div>

                    <form action="proses_enkripsi_des64.php" method="POST" enctype="multipart/form-data" id="desEncryptionForm">
                        
                        <div class="form-group">
                            <label for="fileToEncryptDes">Pilih File untuk Dienkripsi:</label>
                            <input type="file" name="file" id="fileToEncryptDes" class="form-control-file" required>
                            <small class="form-text text-muted">Format yang diizinkan: docx, doc, txt, pdf, xls, xlsx, ppt, pptx, jpg, jpeg, png, gif, mp3, mp4, mov, mpg. Maks 8MB. Data akan diproses per blok 8 byte.</small>
                        </div>

                        <div class="form-group">
                            <label for="encryptionPasswordDes">Password Enkripsi (Kunci 8 byte):</label>
                            <input type="password" name="pwdfile" id="encryptionPasswordDes" class="form-control" placeholder="Masukkan Password (akan diambil 8 byte pertama)" required>
                            <small class="form-text text-muted">Password ini akan digunakan untuk menghasilkan kunci DES 64-bit (efektif 56-bit). Direkomendasikan 8 karakter.</small>
                        </div>

                        <div class="form-group">
                            <label for="fileDescriptionDes">Deskripsi File (Opsional):</label>
                            <textarea name="desc" id="fileDescriptionDes" class="form-control" rows="3" placeholder="Deskripsi singkat mengenai file..."></textarea>
                        </div>
                        
                        <div class="form-group button-group">
                            <button type="submit" name="encrypt_des_now" class="btn btn-primary">Enkripsi File dengan DES</button>
                            <button type="reset" class="btn btn-secondary">Reset Form</button>
                        </div>

                        <div id="encryptionStatusDes" class="mt-3"></div>
                    </form>
                </div>
            </div>
            </div> </div> <script src="../assets/js/dashboard.js"></script> 
    <?php // Ionicons sudah ada di header.php ?>
    
    <?php 
    // Anda mungkin perlu include config.php dan mysqli_close($conn) jika ada operasi DB di halaman ini.
    // Untuk form sederhana ini, biasanya tidak perlu.
    ?>
</body>
</html>