<?php

// Kelas AES ini menyediakan implementasi untuk enkripsi dan dekripsi AES-128.
// AES (Advanced Encryption Standard) adalah standar enkripsi blok simetris.
// Implementasi ini akan selalu bekerja pada blok data 16 byte.

class AES
{
    // Ukuran blok dalam byte untuk AES (selalu 16 byte / 128 bit)
    private const BLOCK_SIZE = 16;

    // Properti untuk tabel substitusi (S-Box) standar AES.
    // Private karena ini adalah konstanta internal algoritma.
    private $sBox = [
        [0x63, 0x7c, 0x77, 0x7b, 0xf2, 0x6b, 0x6f, 0xc5, 0x30, 0x01, 0x67, 0x2b, 0xfe, 0xd7, 0xab, 0x76],
        [0xca, 0x82, 0xc9, 0x7d, 0xfa, 0x59, 0x47, 0xf0, 0xad, 0xd4, 0xa2, 0xaf, 0x9c, 0xa4, 0x72, 0xc0],
        [0xb7, 0xfd, 0x93, 0x26, 0x36, 0x3f, 0xf7, 0xcc, 0x34, 0xa5, 0xe5, 0xf1, 0x71, 0xd8, 0x31, 0x15],
        [0x04, 0xc7, 0x23, 0xc3, 0x18, 0x96, 0x05, 0x9a, 0x07, 0x12, 0x80, 0xe2, 0xeb, 0x27, 0xb2, 0x75],
        [0x09, 0x83, 0x2c, 0x1a, 0x1b, 0x6e, 0x5a, 0xa0, 0x52, 0x3b, 0xd6, 0xb3, 0x29, 0xe3, 0x2f, 0x84],
        [0x53, 0xd1, 0x00, 0xed, 0x20, 0xfc, 0xb1, 0x5b, 0x6a, 0xcb, 0xbe, 0x39, 0x4a, 0x4c, 0x58, 0xcf],
        [0xd0, 0xef, 0xaa, 0xfb, 0x43, 0x4d, 0x33, 0x85, 0x45, 0xf9, 0x02, 0x7f, 0x50, 0x3c, 0x9f, 0xa8],
        [0x51, 0xa3, 0x40, 0x8f, 0x92, 0x9d, 0x38, 0xf5, 0xbc, 0xb6, 0xda, 0x21, 0x10, 0xff, 0xf3, 0xd2],
        [0xcd, 0x0c, 0x13, 0xec, 0x5f, 0x97, 0x44, 0x17, 0xc4, 0xa7, 0x7e, 0x3d, 0x64, 0x5d, 0x19, 0x73],
        [0x60, 0x81, 0x4f, 0xdc, 0x22, 0x2a, 0x90, 0x88, 0x46, 0xee, 0xb8, 0x14, 0xde, 0x5e, 0x0b, 0xdb],
        [0xe0, 0x32, 0x3a, 0x0a, 0x49, 0x06, 0x24, 0x5c, 0xc2, 0xd3, 0xac, 0x62, 0x91, 0x95, 0xe4, 0x79],
        [0xe7, 0xc8, 0x37, 0x6d, 0x8d, 0xd5, 0x4e, 0xa9, 0x6c, 0x56, 0xf4, 0xea, 0x65, 0x7a, 0xae, 0x08],
        [0xba, 0x78, 0x25, 0x2e, 0x1c, 0xa6, 0xb4, 0xc6, 0xe8, 0xdd, 0x74, 0x1f, 0x4b, 0xbd, 0x8b, 0x8a],
        [0x70, 0x3e, 0xb5, 0x66, 0x48, 0x03, 0xf6, 0x0e, 0x61, 0x35, 0x57, 0xb9, 0x86, 0xc1, 0x1d, 0x9e],
        [0xe1, 0xf8, 0x98, 0x11, 0x69, 0xd9, 0x8e, 0x94, 0x9b, 0x1e, 0x87, 0xe9, 0xce, 0x55, 0x28, 0xdf],
        [0x8c, 0xa1, 0x89, 0x0d, 0xbf, 0xe6, 0x42, 0x68, 0x41, 0x99, 0x2d, 0x0f, 0xb0, 0x54, 0xbb, 0x16]
    ];

    // Tabel Inverse S-Box standar AES.
    private $invSBox = [
        [0x52, 0x09, 0x6a, 0xd5, 0x30, 0x36, 0xa5, 0x38, 0xbf, 0x40, 0xa3, 0x9e, 0x81, 0xf3, 0xd7, 0xfb],
        [0x7c, 0xe3, 0x39, 0x82, 0x9b, 0x2f, 0xff, 0x87, 0x34, 0x8e, 0x43, 0x44, 0xc4, 0xde, 0xe9, 0xcb],
        [0x54, 0x7b, 0x94, 0x32, 0xa6, 0xc2, 0x23, 0x3d, 0xee, 0x4c, 0x95, 0x0b, 0x42, 0xfa, 0xc3, 0x4e],
        [0x08, 0x2e, 0xa1, 0x66, 0x28, 0xd9, 0x24, 0xb2, 0x76, 0x5b, 0xa2, 0x49, 0x6d, 0x8b, 0xd1, 0x25],
        [0x72, 0xf8, 0xf6, 0x64, 0x86, 0x68, 0x98, 0x16, 0xd4, 0xa4, 0x5c, 0xcc, 0x5d, 0x65, 0xb6, 0x92],
        [0x6c, 0x70, 0x48, 0x50, 0xfd, 0xed, 0xb9, 0xda, 0x5e, 0x15, 0x46, 0x57, 0xa7, 0x8d, 0x9d, 0x84],
        [0x90, 0xd8, 0xab, 0x00, 0x8c, 0xbc, 0xd3, 0x0a, 0xf7, 0xe4, 0x58, 0x05, 0xb8, 0xb3, 0x45, 0x06],
        [0xd0, 0x2c, 0x1e, 0x8f, 0xca, 0x3f, 0x0f, 0x02, 0xc1, 0xaf, 0xbd, 0x03, 0x01, 0x13, 0x8a, 0x6b],
        [0x3a, 0x91, 0x11, 0x41, 0x4f, 0x67, 0xdc, 0xea, 0x97, 0xf2, 0xcf, 0xce, 0xf0, 0xb4, 0xe6, 0x73],
        [0x96, 0xac, 0x74, 0x22, 0xe7, 0xad, 0x35, 0x85, 0xe2, 0xf9, 0x37, 0xe8, 0x1c, 0x75, 0xdf, 0x6e],
        [0x47, 0xf1, 0x1a, 0x71, 0x1d, 0x29, 0xc5, 0x89, 0x6f, 0xb7, 0x62, 0x0e, 0xaa, 0x18, 0xbe, 0x1b],
        [0xfc, 0x56, 0x3e, 0x4b, 0xc6, 0xd2, 0x79, 0x20, 0x9a, 0xdb, 0xc0, 0xfe, 0x78, 0xcd, 0x5a, 0xf4],
        [0x1f, 0xdd, 0xa8, 0x33, 0x88, 0x07, 0xc7, 0x31, 0xb1, 0x12, 0x10, 0x59, 0x27, 0x80, 0xec, 0x5f],
        [0x60, 0x51, 0x7f, 0xa9, 0x19, 0xb5, 0x4a, 0x0d, 0x2d, 0xe5, 0x7a, 0x9f, 0x93, 0xc9, 0x9c, 0xef],
        [0xa0, 0xe0, 0x3b, 0x4d, 0xae, 0x2a, 0xf5, 0xb0, 0xc8, 0xeb, 0xbb, 0x3c, 0x83, 0x53, 0x99, 0x61],
        [0x17, 0x2b, 0x04, 0x7e, 0xba, 0x77, 0xd6, 0x26, 0xe1, 0x69, 0x14, 0x63, 0x55, 0x21, 0x0c, 0x7d]
    ];

    // Konstanta Putaran (Round Constant - Rcon) untuk Key Expansion.
    // Untuk AES-128 (10 putaran), kita membutuhkan 10 nilai Rcon.
    // Formatnya adalah [rc, 0x00, 0x00, 0x00]
    private $rcon = [
        [0x00, 0x00, 0x00, 0x00], // Rcon[0] tidak digunakan, tapi disertakan agar indeks Rcon[i] = $rcon[$i-1]
        [0x01, 0x00, 0x00, 0x00], // Rcon[1]
        [0x02, 0x00, 0x00, 0x00], // Rcon[2]
        [0x04, 0x00, 0x00, 0x00], // Rcon[3]
        [0x08, 0x00, 0x00, 0x00], // Rcon[4]
        [0x10, 0x00, 0x00, 0x00], // Rcon[5]
        [0x20, 0x00, 0x00, 0x00], // Rcon[6]
        [0x40, 0x00, 0x00, 0x00], // Rcon[7]
        [0x80, 0x00, 0x00, 0x00], // Rcon[8]
        [0x1b, 0x00, 0x00, 0x00], // Rcon[9]
        [0x36, 0x00, 0x00, 0x00]  // Rcon[10]
    ];

    // Properti untuk menyimpan key schedule (semua round keys).
    // Akan diisi oleh metode keyExpansion().
    // Strukturnya: array dari word (setiap word adalah array 4 byte).
    // $w[word_index][byte_index_dalam_word]
    private $w = [];

    // Parameter-parameter AES yang akan di-set di konstruktor.
    private $Nb; // Jumlah kolom (dalam word 32-bit) pada State. Selalu 4 untuk AES.
    private $Nk; // Jumlah word 32-bit pada Kunci Cipher (4 untuk AES-128).
    private $Nr; // Jumlah putaran enkripsi/dekripsi (10 untuk AES-128).

// --- Akhir Part 1 ---
// Melanjutkan dari Part 1 (Definisi Kelas, S-Box, InvS-Box, Rcon, Properti)
// class AES {
// ... (properti dari Part 1) ...

    /**
     * Konstruktor untuk kelas AES.
     * Menginisialisasi parameter dan melakukan ekspansi kunci.
     *
     * @param string $key Kunci enkripsi/dekripsi 16 byte (untuk AES-128).
     */
    public function __construct($key)
    {
        // $this->Nb: Jumlah kolom (dalam word 32-bit) pada State.
        // Untuk AES, ini selalu 4 (yang berarti blok data 4x4 byte = 16 byte).
        $this->Nb = 4;

        // $this->Nk: Jumlah word 32-bit pada Kunci Cipher.
        // Untuk AES-128, panjang kunci adalah 16 byte, jadi Nk = 16 byte / 4 byte/word = 4 words.
        $keyLengthBytes = strlen($key);
        if ($keyLengthBytes !== 16) {
            // Menghentikan eksekusi jika panjang kunci bukan 16 byte (khusus untuk implementasi AES-128 ini).
            // Komentar: Untuk tugas akhir yang fokus pada AES-128, validasi ketat ini penting.
            die("Error: Panjang kunci harus tepat 16 byte untuk implementasi AES-128 ini. Diberikan: " . $keyLengthBytes . " byte.");
        }
        $this->Nk = 4; // Untuk AES-128, Nk selalu 4.

        // $this->Nr: Jumlah putaran dalam algoritma AES.
        // Untuk AES-128 (Nk=4), jumlah putaran (Nr) adalah 10.
        // Rumus standar: Nr = Nk + 6.
        $this->Nr = 10; // Ditetapkan langsung untuk AES-128.

        // Inisialisasi array untuk key schedule ($this->w).
        // $this->w akan menyimpan (Nb * (Nr + 1)) words, dimana setiap word adalah array 4 byte.
        // Untuk AES-128: 4 * (10 + 1) = 44 words.
        // $this->w[word_index][byte_index_dalam_word]
        $this->w = array_fill(0, $this->Nb * ($this->Nr + 1), array_fill(0, 4, 0));

        // Memanggil fungsi untuk ekspansi kunci (menghasilkan semua round keys).
        $this->keyExpansion($key);
    }

    /**
     * Melakukan Key Expansion untuk AES-128.
     * Menghasilkan semua round keys dan menyimpannya di $this->w.
     * $this->w[word_index (0-43)][byte_index_dalam_word (0-3)]
     *
     * @param string $key Kunci cipher awal 16 byte.
     */
    private function keyExpansion($key)
    {
        // Komentar: Fungsi ini spesifik untuk AES-128 (Nk=4, Nr=10).
        // Mengubah string key menjadi array nilai byte (integer 0-255).
        $keyBytes = array_values(unpack("C*", $key));

        // 1. Salin kunci asli (16 byte = 4 words) ke 4 word pertama dari key schedule ($this->w).
        //    $this->w[word_index][byte_index_dalam_word]
        $wordIndex = 0;
        for ($i = 0; $i < $this->Nk; $i++) { // $this->Nk adalah 4 untuk AES-128
            $this->w[$wordIndex][0] = $keyBytes[($i * 4) + 0];
            $this->w[$wordIndex][1] = $keyBytes[($i * 4) + 1];
            $this->w[$wordIndex][2] = $keyBytes[($i * 4) + 2];
            $this->w[$wordIndex][3] = $keyBytes[($i * 4) + 3];
            $wordIndex++;
        }

        // 2. Generate sisa word dalam key schedule.
        //    Loop dimulai dari $this->Nk (yaitu 4) hingga total word yang dibutuhkan (44 untuk AES-128).
        //    $i di sini adalah indeks word saat ini yang sedang digenerate.
        for ($i = $this->Nk; $i < ($this->Nb * ($this->Nr + 1)); $i++) {
            // Ambil word sebelumnya (w[i-1]) ke $temp.
            // $temp adalah array 4 byte.
            $temp = $this->w[$i - 1];

            // Jika $i adalah kelipatan dari Nk (panjang kunci asli dalam word),
            // maka lakukan transformasi khusus pada $temp (RotWord, SubWord, XOR dengan Rcon).
            // Untuk AES-128, Nk = 4. Jadi, ini terjadi pada i = 4, 8, 12, ..., 40.
            if ($i % $this->Nk == 0) {
                // a. RotWord: Geser byte dalam $temp ke kiri secara sirkular.
                //    [b0, b1, b2, b3] menjadi [b1, b2, b3, b0]
                $firstByte = $temp[0];
                $temp[0] = $temp[1];
                $temp[1] = $temp[2];
                $temp[2] = $temp[3];
                $temp[3] = $firstByte;

                // b. SubWord: Setiap byte dalam $temp (yang sudah di-RotWord)
                //    dilewatkan melalui S-Box menggunakan $this->sBoxLookup().
                //    (Kita akan buat fungsi sBoxLookup() nanti yang menggunakan $this->sBox).
                for ($k = 0; $k < 4; $k++) {
                    $temp[$k] = $this->sBoxLookup($temp[$k]);
                }

                // c. XOR hasil SubWord ($temp[0]) dengan Round Constant (Rcon).
                //    Hanya byte pertama dari $temp yang di-XOR dengan byte pertama Rcon.
                //    Indeks Rcon adalah $i / $this->Nk.
                //    Misal, jika i=4, Nk=4, maka Rcon index = 1. Rcon[1] = [0x01, 0, 0, 0].
                $rconIndex = $i / $this->Nk;
                $temp[0] = $temp[0] ^ $this->rcon[$rconIndex][0];
                // Byte Rcon lainnya (indeks 1,2,3) adalah 0, jadi XOR dengan 0 tidak mengubah nilai.
                // $temp[1] = $temp[1] ^ $this->rcon[$rconIndex][1]; // yaitu ^ 0
                // $temp[2] = $temp[2] ^ $this->rcon[$rconIndex][2]; // yaitu ^ 0
                // $temp[3] = $temp[3] ^ $this->rcon[$rconIndex][3]; // yaitu ^ 0
            }
            // Komentar: Untuk AES-256, ada kondisi tambahan 'else if (Nk > 6 && i % Nk == 4)'
            // yang melakukan SubWord pada $temp. Untuk AES-128, ini tidak diperlukan.

            // d. Word baru w[i] adalah XOR dari w[i - Nk] dengan $temp (yang sudah diproses).
            //    w[i] = w[i - Nk] XOR temp.
            for ($k = 0; $k < 4; $k++) {
                $this->w[$i][$k] = $this->w[$i - $this->Nk][$k] ^ $temp[$k];
            }
        }
        // Pada titik ini, $this->w sudah berisi semua 44 round keys untuk AES-128.
    }

// --- Akhir Part 2 ---
// Melanjutkan dari Part 2 (Konstruktor dan keyExpansion())
// class AES {
// ... (properti dan metode dari Part 1 & 2) ...

    /**
     * Fungsi helper private untuk melakukan operasi xtime (perkalian dengan 0x02)
     * di Galois Field GF(2^8) dengan polinomial primitif x^8 + x^4 + x^3 + x + 1 (0x11B).
     * Input: $byte (integer 0-255)
     * Output: integer 0-255 (hasil $byte * 0x02 di GF(2^8))
     */
    private function xtime($byte)
    {
        // Geser kiri 1 bit (sama dengan dikali 2)
        $result = $byte << 1;

        // Jika byte asli memiliki MSB (Most Significant Bit) bernilai 1 (yaitu >= 0x80 atau 128),
        // maka hasil geser kiri perlu di-XOR dengan 0x1B (polinomial primitif AES dikurangi x^8).
        if ($byte & 0x80) { // Cek apakah bit ke-7 (paling kiri, dari 0-7) adalah 1
            // $result &= 0xff; // Pastikan tetap 1 byte setelah shift (opsional jika $byte < 128)
            $result ^= 0x1b; // XOR dengan 00011011 (nilai dari x^4 + x^3 + x + 1)
        }
        // Pastikan hasilnya tetap dalam rentang byte (0-255) setelah XOR, meskipun <<1 biasanya sudah menjaga ini untuk input byte.
        return $result & 0xff;
    }

    /**
     * Fungsi helper private untuk perkalian dua byte di GF(2^8).
     * Hanya akan mengimplementasikan yang dibutuhkan untuk MixColumns dan InvMixColumns standar AES
     * yaitu perkalian dengan 0x01, 0x02, 0x03, 0x09, 0x0B, 0x0D, 0x0E.
     * * @param int $a Konstanta perkalian (0x01, 0x02, 0x03, 0x09, 0x0B, 0x0D, 0x0E)
     * @param int $b Byte yang akan dikalikan (0-255)
     * @return int Hasil perkalian di GF(2^8) (0-255)
     */
    private function gfMultiply($a, $b)
    {
        // kita bisa membangun perkalian ini dari fungsi xtime() untuk konstanta yang dibutuhkan AES.
        // Ini lebih ringkas dan menggunakan polinomial standar AES (0x11B) secara implisit melalui xtime().

        if ($a == 0x01) {
            return $b;
        }
        if ($a == 0x02) {
            return $this->xtime($b);
        }
        if ($a == 0x03) { // 0x03 * b = (0x02 * b) ^ b
            return $this->xtime($b) ^ $b;
        }
        
        // Untuk InvMixColumns, kita butuh perkalian dengan 0x09, 0x0B, 0x0D, 0x0E
        // 0x09 = ((0x02*0x02)*0x02) ^ 0x01 -> (b*2*2*2) ^ b
        // 0x0B = ((0x02*0x02)*0x02) ^ (0x02) ^ 0x01 -> (b*2*2*2) ^ (b*2) ^ b
        // 0x0D = ((0x02*0x02)*0x02) ^ (0x02*0x02) ^ 0x01 -> (b*2*2*2) ^ (b*2*2) ^ b
        // 0x0E = ((0x02*0x02)*0x02) ^ (0x02*0x02) ^ 0x02 -> (b*2*2*2) ^ (b*2*2) ^ (b*2)

        $b2 = $this->xtime($b);         // b * 0x02
        $b4 = $this->xtime($b2);        // b * 0x04
        $b8 = $this->xtime($b4);        // b * 0x08

        if ($a == 0x09) { // 0x08 ^ 0x01
            return $b8 ^ $b;
        }
        if ($a == 0x0b) { // 0x08 ^ 0x02 ^ 0x01
            return $b8 ^ $b2 ^ $b;
        }
        if ($a == 0x0d) { // 0x08 ^ 0x04 ^ 0x01
            return $b8 ^ $b4 ^ $b;
        }
        if ($a == 0x0e) { // 0x08 ^ 0x04 ^ 0x02
            return $b8 ^ $b4 ^ $b2;
        }

        // Fallback atau error jika konstanta $a tidak dikenali (seharusnya tidak terjadi jika hanya konstanta AES yang dipakai)
        // Untuk implementasi yang lebih general, diperlukan metode perkalian GF(2^8) yang lebih lengkap.
        // Namun untuk MixColumns/InvMixColumns AES, ini sudah mencakup.
        // Jika Anda menggunakan referensi tabel ltable/atable teman Anda, fungsi mult() mereka bisa dipakai di sini.
        // Tapi karena kita membuat ulang dengan teori, ini adalah pendekatan yang lebih mendasar.
        // error_log("gfMultiply dipanggil dengan konstanta tak terduga: " . dechex($a));
        return 0; // Seharusnya tidak sampai sini jika $a adalah konstanta AES yang valid
    }


    /**
     * Fungsi helper private untuk melakukan substitusi satu byte menggunakan S-Box.
     * Digunakan dalam langkah SubBytes dan KeyExpansion.
     * Input: $byte (integer 0-255)
     * Output: integer 0-255 (nilai dari S-Box)
     * Ini adalah versi yang sudah dikoreksi menggunakan operasi bitwise.
     */
    private function sBoxLookup($byte)
    {
        // Ambil 4 bit atas (nibble atas) untuk indeks baris (0-15)
        $row = ($byte >> 4) & 0x0F; 
        // ($byte >> 4) adalah operasi shift bit ke kanan sebanyak 4 posisi.
        // Contoh: Jika $byte = 0xAB (biner: 10101011)
        //         $byte >> 4 akan menjadi 0x0A (biner: 00001010)
        // (& 0x0F) adalah operasi bitwise AND dengan 00001111.
        //         Ini memastikan hasilnya selalu dalam rentang 0-15 (0x0 - 0xF).

        // Ambil 4 bit bawah (nibble bawah) untuk indeks kolom (0-15)
        $col = $byte & 0x0F;
        // ($byte & 0x0F) akan mengambil 4 bit terakhir dari $byte.
        // Contoh: Jika $byte = 0xAB (biner: 10101011)
        //         $byte & 0x0F (00001111) akan menjadi 0x0B (biner: 00001011)
        
        return $this->sBox[$row][$col];
    }

    /**
     * Fungsi helper private untuk melakukan substitusi satu byte menggunakan Inverse S-Box.
     * Digunakan dalam langkah InvSubBytes.
     * Input: $byte (integer 0-255)
     * Output: integer 0-255 (nilai dari Inverse S-Box)
     * Ini adalah versi yang sudah dikoreksi menggunakan operasi bitwise.
     */
    private function invSBoxLookup($byte)
    {
        // Logika sama dengan sBoxLookup, hanya menggunakan tabel $this->invSBox.
        $row = ($byte >> 4) & 0x0F;
        $col = $byte & 0x0F;
        
        return $this->invSBox[$row][$col];
    }

// --- Akhir Part 3 ---
// Melanjutkan dari Part 3 (Helper xtime, gfMultiply, sBoxLookup, invSBoxLookup)
// class AES {
// ... (properti dan metode dari Part 1, 2, & 3) ...

    /**
     * Melakukan operasi ShiftRows pada state array.
     * Baris-baris dari state digeser secara siklik ke kiri dengan offset yang berbeda.
     * - Baris 0: tidak ada pergeseran.
     * - Baris 1: pergeseran 1 byte ke kiri.
     * - Baris 2: pergeseran 2 byte ke kiri.
     * - Baris 3: pergeseran 3 byte ke kiri.
     * (Asumsi $this->state atau input $state adalah [baris][kolom])
     *
     * @param array $state Array 4x4 byte (state saat ini).
     * @return array Array 4x4 byte (state setelah ShiftRows).
     */
    protected function shiftRows($state)
    {
        // Inisialisasi state baru dengan nilai 0 atau salin dari state lama.
        // Lebih aman membuat array baru untuk hasil.
        $newState = array_fill(0, 4, array_fill(0, $this->Nb, 0));

        // Baris 0: tidak ada pergeseran.
        // Kolom state (dan $this->Nb) untuk AES selalu 4.
        for ($col = 0; $col < $this->Nb; $col++) {
            $newState[0][$col] = $state[0][$col];
        }

        // Baris 1: geser 1 byte ke kiri.
        // newState[1][0] = state[1][1]
        // newState[1][1] = state[1][2]
        // newState[1][2] = state[1][3]
        // newState[1][3] = state[1][0]
        for ($col = 0; $col < $this->Nb; $col++) {
            $newState[1][$col] = $state[1][($col + 1) % $this->Nb];
        }

        // Baris 2: geser 2 byte ke kiri.
        // newState[2][0] = state[2][2]
        // newState[2][1] = state[2][3]
        // newState[2][2] = state[2][0]
        // newState[2][3] = state[2][1]
        for ($col = 0; $col < $this->Nb; $col++) {
            $newState[2][$col] = $state[2][($col + 2) % $this->Nb];
        }

        // Baris 3: geser 3 byte ke kiri.
        // newState[3][0] = state[3][3]
        // newState[3][1] = state[3][0]
        // newState[3][2] = state[3][1]
        // newState[3][3] = state[3][2]
        for ($col = 0; $col < $this->Nb; $col++) {
            $newState[3][$col] = $state[3][($col + 3) % $this->Nb];
        }
        
        return $newState;
    }

    /**
     * Melakukan operasi Inverse ShiftRows pada state array.
     * Baris-baris dari state digeser secara siklik ke KANAN dengan offset yang berbeda.
     * - Baris 0: tidak ada pergeseran.
     * - Baris 1: pergeseran 1 byte ke kanan.
     * - Baris 2: pergeseran 2 byte ke kanan.
     * - Baris 3: pergeseran 3 byte ke kanan.
     * (Asumsi $this->state atau input $state adalah [baris][kolom])
     * Ini adalah implementasi yang sudah dikoreksi.
     *
     * @param array $state Array 4x4 byte (state saat ini).
     * @return array Array 4x4 byte (state setelah Inverse ShiftRows).
     */
    protected function invShiftRows($state)
    {
        $newState = array_fill(0, 4, array_fill(0, $this->Nb, 0));

        // Baris 0: tidak ada pergeseran.
        for ($col = 0; $col < $this->Nb; $col++) {
            $newState[0][$col] = $state[0][$col];
        }

        // Baris 1: geser 1 byte ke kanan.
        // newState[1][0] = state[1][3]
        // newState[1][1] = state[1][0]
        // newState[1][2] = state[1][1]
        // newState[1][3] = state[1][2]
        // Rumus: indeks_sumber = (indeks_tujuan - pergeseran + Nb) % Nb
        for ($col = 0; $col < $this->Nb; $col++) {
            $newState[1][$col] = $state[1][($col - 1 + $this->Nb) % $this->Nb];
        }

        // Baris 2: geser 2 byte ke kanan.
        for ($col = 0; $col < $this->Nb; $col++) {
            $newState[2][$col] = $state[2][($col - 2 + $this->Nb) % $this->Nb];
        }

        // Baris 3: geser 3 byte ke kanan.
        for ($col = 0; $col < $this->Nb; $col++) {
            $newState[3][$col] = $state[3][($col - 3 + $this->Nb) % $this->Nb];
        }
        
        return $newState;
    }

// --- Akhir Part 4 ---
// Melanjutkan dari Part 4 (Metode shiftRows() dan invShiftRows())
// class AES {
// ... (properti dan metode dari Part 1, 2, 3, & 4) ...

    /**
     * Melakukan operasi MixColumns pada state array.
     * Setiap kolom dari state diolah sebagai polinomial dan dikalikan (di GF(2^8))
     * dengan matriks sirkumpleks tetap.
     * (Asumsi $this->state atau input $state adalah [baris][kolom])
     *
     * @param array $state Array 4x4 byte (state saat ini).
     * @return array Array 4x4 byte (state setelah MixColumns).
     */
    protected function mixColumns($state)
    {
        $newState = array_fill(0, 4, array_fill(0, $this->Nb, 0));

        // Loop untuk setiap kolom dari state ($this->Nb = 4)
        for ($col = 0; $col < $this->Nb; $col++) {
            // Byte-byte dari kolom saat ini
            $s0 = $state[0][$col];
            $s1 = $state[1][$col];
            $s2 = $state[2][$col];
            $s3 = $state[3][$col];

            // Perhitungan untuk setiap byte di kolom baru berdasarkan matriks MixColumns:
            // [02 03 01 01]   [s0]   [s'0]
            // [01 02 03 01] . [s1] = [s'1]
            // [01 01 02 03]   [s2]   [s'2]
            // [03 01 01 02]   [s3]   [s'3]
            // Semua perkalian dan penjumlahan (XOR) dilakukan di GF(2^8).
            $newState[0][$col] = $this->gfMultiply(0x02, $s0) ^ $this->gfMultiply(0x03, $s1) ^ $s2                               ^ $s3;                              // Perkalian dengan 0x01 adalah identitas (nilai byte itu sendiri)
            $newState[1][$col] = $s0                               ^ $this->gfMultiply(0x02, $s1) ^ $this->gfMultiply(0x03, $s2) ^ $s3;
            $newState[2][$col] = $s0                               ^ $s1                               ^ $this->gfMultiply(0x02, $s2) ^ $this->gfMultiply(0x03, $s3);
            $newState[3][$col] = $this->gfMultiply(0x03, $s0) ^ $s1                               ^ $s2                               ^ $this->gfMultiply(0x02, $s3);
        }
        return $newState;
    }

    /**
     * Melakukan operasi Inverse MixColumns pada state array.
     * Setiap kolom dari state diolah sebagai polinomial dan dikalikan (di GF(2^8))
     * dengan matriks invers dari matriks MixColumns.
     * (Asumsi $this->state atau input $state adalah [baris][kolom])
     *
     * @param array $state Array 4x4 byte (state saat ini).
     * @return array Array 4x4 byte (state setelah Inverse MixColumns).
     */
    protected function invMixColumns($state)
    {
        $newState = array_fill(0, 4, array_fill(0, $this->Nb, 0));

        // Loop untuk setiap kolom dari state ($this->Nb = 4)
        for ($col = 0; $col < $this->Nb; $col++) {
            // Byte-byte dari kolom saat ini
            $s0 = $state[0][$col];
            $s1 = $state[1][$col];
            $s2 = $state[2][$col];
            $s3 = $state[3][$col];

            // Perhitungan untuk setiap byte di kolom baru berdasarkan matriks Inverse MixColumns:
            // [0e 0b 0d 09]   [s0]   [s'0]
            // [09 0e 0b 0d] . [s1] = [s'1]
            // [0d 09 0e 0b]   [s2]   [s'2]
            // [0b 0d 09 0e]   [s3]   [s'3]
            // Semua perkalian dan penjumlahan (XOR) dilakukan di GF(2^8).
            $newState[0][$col] = $this->gfMultiply(0x0e, $s0) ^ $this->gfMultiply(0x0b, $s1) ^ $this->gfMultiply(0x0d, $s2) ^ $this->gfMultiply(0x09, $s3);
            $newState[1][$col] = $this->gfMultiply(0x09, $s0) ^ $this->gfMultiply(0x0e, $s1) ^ $this->gfMultiply(0x0b, $s2) ^ $this->gfMultiply(0x0d, $s3);
            $newState[2][$col] = $this->gfMultiply(0x0d, $s0) ^ $this->gfMultiply(0x09, $s1) ^ $this->gfMultiply(0x0e, $s2) ^ $this->gfMultiply(0x0b, $s3);
            $newState[3][$col] = $this->gfMultiply(0x0b, $s0) ^ $this->gfMultiply(0x0d, $s1) ^ $this->gfMultiply(0x09, $s2) ^ $this->gfMultiply(0x0e, $s3);
        }
        return $newState;
    }

// --- Akhir Part 5 ---
// Melanjutkan dari Part 5 (Metode mixColumns() dan invMixColumns())
// class AES {
// ... (properti dan metode dari Part 1, 2, 3, 4, & 5) ...

    /**
     * Melakukan operasi AddRoundKey pada state array.
     * Setiap byte dari state di-XOR-kan dengan byte yang sesuai dari round key.
     * Round key diambil dari $this->w berdasarkan $this->pos_w (yang di-set oleh encrypt/decrypt).
     * (Asumsi $this->state atau input $state adalah [baris][kolom])
     *
     * @param array $state Array 4x4 byte (state saat ini).
     * @return array Array 4x4 byte (state setelah AddRoundKey).
     */
     protected function addRoundKey($state)
    {
        $newState = array_fill(0, 4, array_fill(0, $this->Nb, 0));

        for ($col = 0; $col < $this->Nb; $col++) { // Loop per kolom state (0-3)
            $current_word_index = $this->pos_w + $col; // Indeks word dalam key schedule
            for ($row = 0; $row < 4; $row++) {   // Loop per baris state (0-3), juga sbg indeks byte dlm word
                // AKSES YANG BENAR KE KEY SCHEDULE: $this->w[indeks_word][indeks_byte]
                $newState[$row][$col] = $state[$row][$col] ^ $this->w[$current_word_index][$row];
            }
        }
        return $newState;
    }

    /**
     * Melakukan operasi SubBytes pada state array.
     * Setiap byte dari state digantikan dengan nilai dari S-Box.
     * (Asumsi $this->state atau input $state adalah [baris][kolom])
     *
     * @param array $state Array 4x4 byte (state saat ini).
     * @return array Array 4x4 byte (state setelah SubBytes).
     */
    protected function subBytes($state)
    {
        $newState = array_fill(0, 4, array_fill(0, $this->Nb, 0));
        for ($row = 0; $row < 4; $row++) {
            for ($col = 0; $col < $this->Nb; $col++) {
                // Menggunakan helper sBoxLookup yang sudah dikoreksi.
                $newState[$row][$col] = $this->sBoxLookup($state[$row][$col]);
            }
        }
        return $newState;
    }

    /**
     * Melakukan operasi Inverse SubBytes pada state array.
     * Setiap byte dari state digantikan dengan nilai dari Inverse S-Box.
     * (Asumsi $this->state atau input $state adalah [baris][kolom])
     *
     * @param array $state Array 4x4 byte (state saat ini).
     * @return array Array 4x4 byte (state setelah Inverse SubBytes).
     */
    protected function invSubBytes($state)
    {
        $newState = array_fill(0, 4, array_fill(0, $this->Nb, 0));
        for ($row = 0; $row < 4; $row++) {
            for ($col = 0; $col < $this->Nb; $col++) {
                // Menggunakan helper invSBoxLookup yang sudah dikoreksi.
                $newState[$row][$col] = $this->invSBoxLookup($state[$row][$col]);
            }
        }
        return $newState;
    }

// --- Akhir Part 6 ---
// Melanjutkan dari Part 6 (Metode addRoundKey(), subBytes(), dan invSubBytes())
// class AES {
// ... (properti dan semua metode dari Part 1 hingga Part 6) ...

    /**
     * Mengenkripsi satu blok plaintext 16 byte menggunakan AES-128.
     * Metode ini mengasumsikan key schedule ($this->w) sudah diinisialisasi.
     * Input plaintext harus tepat 16 byte. Padding ditangani oleh pemanggil.
     *
     * @param string $plaintextBlock String 16 byte plaintext.
     * @return string String 16 byte ciphertext.
     * @throws Exception Jika input tidak 16 byte.
     */
    public function encryptBlock($plaintextBlock)
    {
        if (strlen($plaintextBlock) !== self::BLOCK_SIZE) {
            // Menggunakan Exception untuk error handling yang lebih baik daripada die()
            // Ini memungkinkan pemanggil untuk menangkap error jika diperlukan.
            throw new Exception("Input untuk encryptBlock() harus tepat " . self::BLOCK_SIZE . " byte.");
        }

        // 1. Ubah plaintext block (string) menjadi state array (4x4 byte)
        //    State diisi kolom per kolom: state[baris][kolom]
        $this->state = array_fill(0, 4, array_fill(0, $this->Nb, 0));
        $bytes = array_values(unpack("C*", $plaintextBlock));
        $k = 0;
        for ($col = 0; $col < $this->Nb; $col++) {
            for ($row = 0; $row < 4; $row++) {
                $this->state[$row][$col] = $bytes[$k++];
            }
        }

        // 2. Initial AddRoundKey
        $this->pos_w = 0; // Pointer ke word 0-3 dari key schedule
        $this->state = $this->addRoundKey($this->state);

        // 3. Main Rounds ($this->Nr - 1 putaran)
        //    Untuk AES-128, Nr = 10, jadi 9 putaran utama.
        for ($round = 1; $round < $this->Nr; $round++) {
            $this->state = $this->subBytes($this->state);
            $this->state = $this->shiftRows($this->state);
            $this->state = $this->mixColumns($this->state);
            $this->pos_w = $round * $this->Nb; // Setiap round key adalah $this->Nb (4) words
            $this->state = $this->addRoundKey($this->state);
        }

        // 4. Final Round (tanpa MixColumns)
        $this->state = $this->subBytes($this->state);
        $this->state = $this->shiftRows($this->state);
        $this->pos_w = $this->Nr * $this->Nb; // Round key terakhir
        $this->state = $this->addRoundKey($this->state);

        // 5. Ubah state array (ciphertext) kembali menjadi string 16 byte
        $ciphertextBlock = "";
        for ($col = 0; $col < $this->Nb; $col++) {
            for ($row = 0; $row < 4; $row++) {
                $ciphertextBlock .= chr($this->state[$row][$col]);
            }
        }
        return $ciphertextBlock;
    }

    /**
     * Mendekripsi satu blok ciphertext 16 byte menggunakan AES-128.
     * Metode ini mengasumsikan key schedule ($this->w) sudah diinisialisasi.
     * Input ciphertext harus tepat 16 byte. Unpadding ditangani oleh pemanggil.
     *
     * @param string $ciphertextBlock String 16 byte ciphertext.
     * @return string String 16 byte plaintext (mungkin masih mengandung padding).
     * @throws Exception Jika input tidak 16 byte.
     */
    public function decryptBlock($ciphertextBlock)
    {
        if (strlen($ciphertextBlock) !== self::BLOCK_SIZE) {
            throw new Exception("Input untuk decryptBlock() harus tepat " . self::BLOCK_SIZE . " byte.");
        }

        // 1. Ubah ciphertext block (string) menjadi state array (4x4 byte)
        //    State diisi kolom per kolom: state[baris][kolom]
        $this->state = array_fill(0, 4, array_fill(0, $this->Nb, 0));
        $bytes = array_values(unpack("C*", $ciphertextBlock));
        $k = 0;
        for ($col = 0; $col < $this->Nb; $col++) {
            for ($row = 0; $row < 4; $row++) {
                $this->state[$row][$col] = $bytes[$k++];
            }
        }

        // 2. Initial AddRoundKey (untuk dekripsi, menggunakan round key terakhir)
        //    $this->Nr adalah 10. Round key terakhir dimulai dari word ke- (10 * 4) = 40.
        $this->pos_w = $this->Nr * $this->Nb;
        $this->state = $this->addRoundKey($this->state);

        // 3. Main Rounds ($this->Nr - 1 putaran), urutan operasi invers
        //    Loop dari putaran ke-($this->Nr - 1) turun ke 1.
        for ($round = ($this->Nr - 1); $round >= 1; $round--) {
            $this->state = $this->invShiftRows($this->state);
            $this->state = $this->invSubBytes($this->state);
            $this->pos_w = $round * $this->Nb; // Round key yang sesuai untuk putaran ini
            $this->state = $this->addRoundKey($this->state);
            $this->state = $this->invMixColumns($this->state);
        }

        // 4. Final Round (untuk dekripsi, tanpa InvMixColumns)
        $this->state = $this->invShiftRows($this->state);
        $this->state = $this->invSubBytes($this->state);
        $this->pos_w = 0; // Round key pertama (original key setelah ekspansi)
        $this->state = $this->addRoundKey($this->state);

        // 5. Ubah state array (plaintext) kembali menjadi string 16 byte
        $plaintextBlock = "";
        for ($col = 0; $col < $this->Nb; $col++) {
            for ($row = 0; $row < 4; $row++) {
                $plaintextBlock .= chr($this->state[$row][$col]);
            }
        }
        return $plaintextBlock; // Plaintext ini mungkin masih mengandung padding PKCS#7
    }

} // Akhir dari class AES
?>
// --- AKHIR FILE AES.php ---