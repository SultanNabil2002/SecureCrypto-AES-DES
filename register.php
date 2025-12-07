<?php 
require_once "includes/functions.php";

if( isset($_POST["daftar"]) ) {
  if( registrasi($_POST) > 0 ) {
        echo "<script>
                alert('user baru berhasil ditambahkan!');
            </script>";
    } else {
        echo mysqli_error($conn);
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Registrasi Akun</title>
  <link rel="stylesheet" href="assets/css/register.css" />
  <link rel="icon" href="assets/img/Jasa Raharja Logo.png" type="image/png">
</head>
<body>
  <main>
    <form action="#" method="post">
      <h1>Daftar Akun</h1>

      <div>
        <label for="username">Username</label>
        <input type="text" id="username" name="username" autocomplete="off" autofocus required />
      </div>

      <div>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required />
      </div>

      <div>
        <label for="confirm_password">Konfirmasi Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required />
      </div>

      <fieldset class="radioButton-group">
        <legend>Pilih Role</legend>
        <label><input type="radio" name="role" value="user" required> User</label>
        <label><input type="radio" name="role" value="admin"> Admin</label>
      </fieldset>

      <button type="submit" name="daftar">Daftar</button>

      <p class="login-link">
        Sudah punya akun? <a href="index.php">Login di sini</a>
      </p>
    </form>

    <aside>
      <img src="assets/img/pt.png" alt="PT Jasa Raharja Putera" draggable="false"/>
    </aside>
  </main>
</body>
</html>
