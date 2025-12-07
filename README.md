# SecureCrypto: Implementasi Kriptografi File Berbasis Web (AES-128 & DES-64)

[![PHP Version](https://img.shields.io/badge/php-8.0%2B-blue.svg)](https://www.php.net/)
[![Database](https://img.shields.io/badge/database-MySQL-orange.svg)](https://www.mysql.com/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

Aplikasi keamanan dokumen berbasis web yang dirancang untuk mengamankan file digital menggunakan implementasi algoritma **Advanced Encryption Standard (AES-128)** dan **Data Encryption Standard (DES-64)**.

Proyek ini dikembangkan sebagai bagian dari Tugas Akhir/Skripsi, dengan fokus utama pada **implementasi manual (from scratch)** logika algoritma kriptografi untuk tujuan analisis perbandingan kinerja dan keamanan.

## 🌟 Fitur Utama

### 1. Kriptografi (Core Engine)
* **Implementasi Manual AES-128:**
    * Membangun kelas `AES.php` dari dasar tanpa *library* kriptografi eksternal.
    * Implementasi lengkap transformasi: *SubBytes*, *ShiftRows*, *MixColumns*, dan *AddRoundKey*.
    * Proses *Key Expansion* 128-bit yang presisi.
* **Implementasi Manual DES-64:**
    * Membangun kelas `DES.php` mengikuti standar FIPS PUB 46-3.
    * Implementasi lengkap struktur *Feistel Network* 16 putaran.
    * Permutasi lengkap: *Initial Permutation (IP)*, *Final Permutation (FP)*, *Expansion (E)*, dan *S-Boxes*.
* **Keamanan Data:**
    * Penerapan **PKCS#7 Padding** untuk menangani blok data.
    * Validasi integritas file menggunakan **Magic Number** (`AES_v1_` & `DES_v1_`) custom header.
    * Mencegah dekripsi silang (mendekripsi file AES dengan algoritma DES, dan sebaliknya).

### 2. Manajemen Sistem
* **Multi-User Role:** Sistem autentikasi aman dengan pemisahan hak akses (Admin vs User).
* **Dashboard Interaktif:** Visualisasi statistik jumlah dokumen terenkripsi dan terdekripsi.
* **Manajemen File:** Upload, Enkripsi, Dekripsi, Download, dan Hapus dokumen.
* **Riwayat Aktivitas:** Pencatatan log aktivitas enkripsi dan dekripsi yang terintegrasi database.

### 3. Keamanan Aplikasi
* **Secure Login:** Password hashing menggunakan `password_hash()` (Bcrypt).
* **SQL Injection Prevention:** Penggunaan *Prepared Statements* (MySQLi) secara menyeluruh pada semua query database.
* **Session Management:** Proteksi akses halaman menggunakan validasi sesi yang ketat.

## 🛠️ Teknologi yang Digunakan

* **Backend:** PHP Native (OOP Style untuk Modul Kriptografi).
* **Database:** MySQL / MariaDB.
* **Frontend:** HTML5, CSS3 (Custom Styling), JavaScript (Vanilla).
* **Server Environment:** XAMPP (Apache).

## 🚀 Cara Instalasi

1.  **Clone Repository**
    ```bash
    git clone [https://github.com/USERNAME_ANDA/SecureCrypto-AES-DES.git](https://github.com/USERNAME_ANDA/SecureCrypto-AES-DES.git)
    ```

2.  **Setup Database**
    * Buat database baru di phpMyAdmin dengan nama `kripto_aes`.
    * Impor file `database.sql` (disertakan dalam repo) ke dalam database tersebut.

3.  **Konfigurasi**
    * Buka file `config/config.php`.
    * Sesuaikan kredensial database jika diperlukan:
        ```php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "kripto_aes";
        ```

4.  **Jalankan**
    * Pindahkan folder project ke `htdocs` (jika menggunakan XAMPP).
    * Akses melalui browser: `http://localhost/SecureCrypto-AES-DES/`

## 📸 Screenshots

*(Tempatkan screenshot halaman login, dashboard, dan proses enkripsi di sini)*

## 📄 Lisensi

Project ini dilisensikan di bawah [MIT License](LICENSE).

---
**Catatan Pengembang:**
Tantangan terbesar dalam proyek ini adalah memastikan akurasi hasil enkripsi pada level bit, terutama dalam menangani *padding* dan *key scheduling* pada DES dan AES secara manual untuk memastikan data dapat kembali ke bentuk aslinya tanpa korupsi sedikitpun.
