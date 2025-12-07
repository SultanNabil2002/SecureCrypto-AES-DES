<?php 
session_start(); // Selalu mulai sesi di paling atas
require_once 'config/config.php'; // Path ke config.php dari root

// Jika pengguna sudah login, redirect ke dashboard
if (isset($_SESSION["username"])) {
    header("Location: dashboard/index.php");
    exit;
}

$error_message = ""; // Variabel untuk pesan error

if (isset($_POST["submit"])) {
    if (empty($_POST["username"]) || empty($_POST["password"])) {
        $error_message = "Username dan password tidak boleh kosong!";
    } else {
        $username = $_POST["username"];
        $password = $_POST["password"];

        // Menggunakan Prepared Statement untuk keamanan dari SQL Injection
        $sql = "SELECT id, username, password, role FROM user WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) === 1) {
                $row = mysqli_fetch_assoc($result);
                if (password_verify($password, $row["password"])) {
                    // Password cocok, set session
                    $_SESSION["login"] = true;
                    $_SESSION["username"] = $row["username"]; // Ambil dari DB untuk konsistensi case
                    $_SESSION["role"] = $row["role"];     // Simpan role dari DB ke session

                    header("Location: dashboard/index.php");
                    exit;
                } else {
                    $error_message = "Username atau password salah!";
                }
            } else {
                $error_message = "Username atau password salah!";
            }
            mysqli_stmt_close($stmt);
        } else {
            // Gagal menyiapkan statement SQL
            $error_message = "Terjadi kesalahan pada sistem. Silakan coba lagi nanti.";
            error_log("Login prepare statement error: " . mysqli_error($conn)); // Catat error untuk admin
        }
    }
}
if (isset($conn)) { mysqli_close($conn); } // Tutup koneksi jika sudah tidak diperlukan
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aplikasi Kriptografi AES</title>
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="icon" href="assets/img/Jasa Raharja Logo.png" type="image/png">
</head>
<body>
    <section class="login-form">
        <header>
            <img src="assets/img/sitdown.png" draggable="false" alt="Login Header Image">
        </header>
        <div class="container">
            <main class="main">
                <article class="content">
                    <h2>Log In</h2>
                    <?php if (!empty($error_message)) : ?>
                        <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
                    <?php endif; ?>
                    <form action="" method="post">
                        <input type="text" id="username" name="username" placeholder="Masukkan username" autocomplete="off" required autofocus>
                        <input type="password" id="password" name="password" placeholder="Masukkan Password" required>
                        <button class="btn" type="submit" name="submit">Login</button>
                    </form>
                    <p class="akun">
                        Belum punya akun? <a href="register.php">Daftar disini!</a>
                    </p>
                </article>
                <aside class="form-img">
                    <img src="assets/img/pt.png" alt="Logo Perusahaan" draggable="false">
                </aside>
            </main>
        </div>
    </section>
</body>
</html>