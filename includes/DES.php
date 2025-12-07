<?php

class DES
{
    private const BLOCK_SIZE_BYTES = 8;
    private const NUM_ROUNDS = 16;

    // Initial Permutation (IP)
    private static $IP_TABLE = [
        58, 50, 42, 34, 26, 18, 10, 2,
        60, 52, 44, 36, 28, 20, 12, 4,
        62, 54, 46, 38, 30, 22, 14, 6,
        64, 56, 48, 40, 32, 24, 16, 8,
        57, 49, 41, 33, 25, 17, 9,  1,
        59, 51, 43, 35, 27, 19, 11, 3,
        61, 53, 45, 37, 29, 21, 13, 5,
        63, 55, 47, 39, 31, 23, 15, 7
    ];

    // Final Permutation (IP^-1)
    private static $FP_TABLE = [
        40, 8, 48, 16, 56, 24, 64, 32,
        39, 7, 47, 15, 55, 23, 63, 31,
        38, 6, 46, 14, 54, 22, 62, 30,
        37, 5, 45, 13, 53, 21, 61, 29,
        36, 4, 44, 12, 52, 20, 60, 28,
        35, 3, 43, 11, 51, 19, 59, 27,
        34, 2, 42, 10, 50, 18, 58, 26,
        33, 1, 41,  9, 49, 17, 57, 25
    ];

    // Expansion Permutation (E-box)
    private static $E_TABLE = [
        32,  1,  2,  3,  4,  5,
         4,  5,  6,  7,  8,  9,
         8,  9, 10, 11, 12, 13,
        12, 13, 14, 15, 16, 17,
        16, 17, 18, 19, 20, 21,
        20, 21, 22, 23, 24, 25,
        24, 25, 26, 27, 28, 29,
        28, 29, 30, 31, 32,  1
    ];

    // P-Box Permutation
    private static $P_TABLE = [
        16,  7, 20, 21, 29, 12, 28, 17,
         1, 15, 23, 26,  5, 18, 31, 10,
         2,  8, 24, 14, 32, 27,  3,  9,
        19, 13, 30,  6, 22, 11,  4, 25
    ];

    // S-Boxes
    private static $S_BOXES = [
        [[14,4,13,1,2,15,11,8,3,10,6,12,5,9,0,7],[0,15,7,4,14,2,13,1,10,6,12,11,9,5,3,8],[4,1,14,8,13,6,2,11,15,12,9,7,3,10,5,0],[15,12,8,2,4,9,1,7,5,11,3,14,10,0,6,13]],
        [[15,1,8,14,6,11,3,4,9,7,2,13,12,0,5,10],[3,13,4,7,15,2,8,14,12,0,1,10,6,9,11,5],[0,14,7,11,10,4,13,1,5,8,12,6,9,3,2,15],[13,8,10,1,3,15,4,2,11,6,7,12,0,5,14,9]],
        [[10,0,9,14,6,3,15,5,1,13,12,7,11,4,2,8],[13,7,0,9,3,4,6,10,2,8,5,14,12,11,15,1],[13,6,4,9,8,15,3,0,11,1,2,12,5,10,14,7],[1,10,13,0,6,9,8,7,4,15,14,3,11,5,2,12]],
        [[7,13,14,3,0,6,9,10,1,2,8,5,11,12,4,15],[13,8,11,5,6,15,0,3,4,7,2,12,1,10,14,9],[10,6,9,0,12,11,7,13,15,1,3,14,5,2,8,4],[3,15,0,6,10,1,13,8,9,4,5,11,12,7,2,14]],
        [[2,12,4,1,7,10,11,6,8,5,3,15,13,0,14,9],[14,11,2,12,4,7,13,1,5,0,15,10,3,9,8,6],[4,2,1,11,10,13,7,8,15,9,12,5,6,3,0,14],[11,8,12,7,1,14,2,13,6,15,0,9,10,4,5,3]],
        [[12,1,10,15,9,2,6,8,0,13,3,4,14,7,5,11],[10,15,4,2,7,12,9,5,6,1,13,14,0,11,3,8],[9,14,15,5,2,8,12,3,7,0,4,10,1,13,11,6],[4,3,2,12,9,5,15,10,11,14,1,7,6,0,8,13]],
        [[4,11,2,14,15,0,8,13,3,12,9,7,5,10,6,1],[13,0,11,7,4,9,1,10,14,3,5,12,2,15,8,6],[1,4,11,13,12,3,7,14,10,15,6,8,0,5,9,2],[6,11,13,8,1,4,10,7,9,5,0,15,14,2,3,12]],
        [[13,2,8,4,6,15,11,1,10,9,3,14,5,0,12,7],[1,15,13,8,10,3,7,4,12,5,6,11,0,14,9,2],[7,11,4,1,9,12,14,2,0,6,10,13,15,3,5,8],[2,1,14,7,4,10,8,13,15,12,9,0,3,5,6,11]]
    ];

    // Permuted Choice 1 (PC-1)
    private static $PC1_TABLE = [
        57, 49, 41, 33, 25, 17,  9,  1,
        58, 50, 42, 34, 26, 18, 10,  2,
        59, 51, 43, 35, 27, 19, 11,  3,
        60, 52, 44, 36, 63, 55, 47, 39,
        31, 23, 15,  7, 62, 54, 46, 38,
        30, 22, 14,  6, 61, 53, 45, 37,
        29, 21, 13,  5, 28, 20, 12,  4
    ];

    // Permuted Choice 2 (PC-2)
    private static $PC2_TABLE = [
        14, 17, 11, 24,  1,  5,  3, 28,
        15,  6, 21, 10, 23, 19, 12,  4,
        26,  8, 16,  7, 27, 20, 13,  2,
        41, 52, 31, 37, 47, 55, 30, 40,
        51, 45, 33, 48, 44, 49, 39, 56,
        34, 53, 46, 42, 50, 36, 29, 32
    ];

    // Key shift schedule
    private static $KEY_SHIFTS = [
        1, 1, 2, 2, 2, 2, 2, 2, 1, 2, 2, 2, 2, 2, 2, 1
    ];

    private $roundKeys = []; // Untuk menyimpan 16 subkunci 48-bit

    // (Melanjutkan dari Bagian 1: Tabel Konstanta)

    public function __construct($key)
    {
        if (strlen($key) !== self::BLOCK_SIZE_BYTES) {
            throw new Exception("Panjang kunci untuk DES harus tepat " . self::BLOCK_SIZE_BYTES . " byte.");
        }
        $keyBytes = array_values(unpack("C*", $key));
        $this->keySchedule($keyBytes);
    }

    private function permute(array $inputBits, array $permutationTable, $outputSize)
    {
        $outputBits = array_fill(0, $outputSize, 0);
        foreach ($permutationTable as $outPos => $inPos) {
            // Nilai di tabel adalah posisi bit input (1-based), jadi kurangi 1 untuk indeks array (0-based)
            if (isset($inputBits[$inPos - 1])) {
                 $outputBits[$outPos] = $inputBits[$inPos - 1];
            }
            // Jika $inPos tidak ada di $inputBits, biarkan 0 (seharusnya tidak terjadi untuk tabel standar)
        }
        return $outputBits;
    }

    private function leftCircularShift(array $bits, $shifts)
    {
        $numBits = count($bits);
        if ($numBits == 0) return [];
        $shifts = $shifts % $numBits;
        return array_merge(array_slice($bits, $shifts), array_slice($bits, 0, $shifts));
    }
    
    private function bitsToString(array $bits) {
        $string = '';
        $numBytes = count($bits) / 8;
        for ($i = 0; $i < $numBytes; $i++) {
            $byteValue = 0;
            for ($j = 0; $j < 8; $j++) {
                $byteValue = ($byteValue << 1) | $bits[($i * 8) + $j];
            }
            $string .= chr($byteValue);
        }
        return $string;
    }

    private function stringToBits(string $string) {
        $bits = [];
        $bytes = array_values(unpack("C*", $string));
        foreach ($bytes as $byte) {
            for ($i = 7; $i >= 0; $i--) {
                $bits[] = ($byte >> $i) & 1;
            }
        }
        return $bits;
    }


    private function keySchedule(array $keyBytes)
    {
        $keyBits = $this->stringToBits(pack("C*", ...$keyBytes)); // Ubah array byte ke string, lalu ke array bit

        // PC-1: 64-bit key -> 56-bit permuted key
        $permutedKey56 = $this->permute($keyBits, self::$PC1_TABLE, 56);

        $C = array_slice($permutedKey56, 0, 28);
        $D = array_slice($permutedKey56, 28, 28);

        $this->roundKeys = [];
        for ($i = 0; $i < self::NUM_ROUNDS; $i++) {
            $C = $this->leftCircularShift($C, self::$KEY_SHIFTS[$i]);
            $D = $this->leftCircularShift($D, self::$KEY_SHIFTS[$i]);
            
            $CnDn = array_merge($C, $D);
            // PC-2: 56-bit CnDn -> 48-bit round key
            $this->roundKeys[$i] = $this->permute($CnDn, self::$PC2_TABLE, 48);
        }
    }
    // (Melanjutkan dari Bagian 2)

    private function feistelFunction(array $halfBlock32Bits, array $roundKey48Bits)
    {
        // Expansion: 32-bit -> 48-bit using E-Table
        $expanded48Bits = $this->permute($halfBlock32Bits, self::$E_TABLE, 48);

        // XOR with round key
        $xorWithKey = [];
        for ($i = 0; $i < 48; $i++) {
            $xorWithKey[$i] = $expanded48Bits[$i] ^ $roundKey48Bits[$i];
        }

        // S-Box substitution
        $sBoxOutput32Bits = [];
        for ($sBoxNum = 0; $sBoxNum < 8; $sBoxNum++) {
            $sBoxInput6Bits = array_slice($xorWithKey, $sBoxNum * 6, 6);

            // Row: bit 0 and bit 5
            $row = ($sBoxInput6Bits[0] << 1) | $sBoxInput6Bits[5];
            // Column: bits 1, 2, 3, 4
            $col = ($sBoxInput6Bits[1] << 3) | ($sBoxInput6Bits[2] << 2) | 
                   ($sBoxInput6Bits[3] << 1) | $sBoxInput6Bits[4];
            
            $sBoxVal = self::$S_BOXES[$sBoxNum][$row][$col]; // Output is integer 0-15

            // Convert 4-bit integer output to 4 bits
            for ($j = 3; $j >= 0; $j--) {
                $sBoxOutput32Bits[] = ($sBoxVal >> $j) & 1;
            }
        }

        // P-Box Permutation
        return $this->permute($sBoxOutput32Bits, self::$P_TABLE, 32);
    }
    // (Melanjutkan dari Bagian 3)

    public function encryptBlock($plaintextBlock)
    {
        if (strlen($plaintextBlock) !== self::BLOCK_SIZE_BYTES) {
            throw new Exception("Input untuk encryptBlock() DES harus tepat " . self::BLOCK_SIZE_BYTES . " byte.");
        }
        $bits = $this->stringToBits($plaintextBlock);
        
        // Initial Permutation
        $permutedBits = $this->permute($bits, self::$IP_TABLE, 64);

        $L = array_slice($permutedBits, 0, 32);
        $R = array_slice($permutedBits, 32, 32);

        // 16 Rounds
        for ($i = 0; $i < self::NUM_ROUNDS; $i++) {
            $L_prev = $L;
            $L = $R;
            $f_output = $this->feistelFunction($R, $this->roundKeys[$i]);
            for ($j = 0; $j < 32; $j++) {
                $R[$j] = $L_prev[$j] ^ $f_output[$j];
            }
        }

        // No final swap in FIPS standard; result of 16 rounds is L16 R16
        $preOutput64Bits = array_merge($L, $R);
        
        // Final Permutation
        $finalPermutedBits = $this->permute($preOutput64Bits, self::$FP_TABLE, 64);
        
        return $this->bitsToString($finalPermutedBits);
    }

    public function decryptBlock($ciphertextBlock)
    {
        if (strlen($ciphertextBlock) !== self::BLOCK_SIZE_BYTES) {
            throw new Exception("Input untuk decryptBlock() DES harus tepat " . self::BLOCK_SIZE_BYTES . " byte.");
        }
        $bits = $this->stringToBits($ciphertextBlock);

        // Initial Permutation
        $permutedBits = $this->permute($bits, self::$IP_TABLE, 64);
        
        $L = array_slice($permutedBits, 0, 32);
        $R = array_slice($permutedBits, 32, 32);

        // 16 Rounds, keys in reverse order
        for ($i = 0; $i < self::NUM_ROUNDS; $i++) {
            $R_prev = $R; // For L_i = R_{i-1} in decryption logic
            $R = $L;      // R_i = L_{i-1}
            
            // f_output uses L (which is R_{i-1} of this stage) and K_{16-i}
            $f_output = $this->feistelFunction($L, $this->roundKeys[self::NUM_ROUNDS - 1 - $i]);
            
            for ($j = 0; $j < 32; $j++) {
                $L[$j] = $R_prev[$j] ^ $f_output[$j];
            }
        }
        
        // No final swap in FIPS standard; result of 16 rounds is L0 R0
        $preOutput64Bits = array_merge($L, $R);

        // Final Permutation
        $finalPermutedBits = $this->permute($preOutput64Bits, self::$FP_TABLE, 64);

        return $this->bitsToString($finalPermutedBits);
    }

} // Akhir dari class DES
?>