# 04_KODE_PHP_VALIDATION

## Deskripsi

Folder ini berisi **3 file PHP** untuk validasi logika AHP & TOPSIS sesuai dengan perhitungan manual di:
- BAGIAN 1: Perhitungan Manual AHP (BAGIAN 1.3-1.6)
- BAGIAN 2-5: Perhitungan Manual TOPSIS (BAGIAN 4-5)

Semua kode didesain untuk **berjalan di terminal** tanpa perlu web server.

---

## Struktur File

```
04_KODE_PHP_VALIDATION/
├── AHPCalculator.php        (Class - AHP calculation logic)
├── TOPSISCalculator.php      (Class - TOPSIS calculation logic)
├── test_validation.php       (Test suite - validasi semua logic)
└── README.md                 (File ini)
```

---

## File 1: AHPCalculator.php

**Fungsi:** Implementasi Analytic Hierarchy Process

**Step-by-step calculation:**
1. Normalisasi pairwise comparison matrix
2. Hitung eigenvector (bobot prioritas)
3. Hitung lambda_max (eigenvalue terbesar)
4. Hitung Consistency Index (CI)
5. Hitung Consistency Ratio (CR) dengan Random Index
6. Validasi: CR < 10% = VALID

**Input:** Pairwise comparison matrix 6×6

**Output:**
```php
[
    'weights' => [0.3067, 0.2377, 0.1377, 0.1055, 0.1308, 0.0817],
    'lambda_max' => 6.2187,
    'ci' => 0.0438,
    'cr' => 0.0353,
    'status' => 'VALID'
]
```

---

## File 2: TOPSISCalculator.php

**Fungsi:** Implementasi Technique for Order Preference by Similarity to Ideal Solution

**Step-by-step calculation:**
1. Normalisasi decision matrix
2. Hitung weighted matrix (V = w × r)
3. Tentukan ideal positive & negative solutions
4. Hitung jarak Euclidean
5. Hitung preference score (C_i)
6. Ranking berdasarkan score

**Input:** 
- Decision matrix (alternatif × kriteria)
- Bobot dari AHP
- Tipe kriteria (benefit/cost)

**Output:**
```php
[
    ['rank' => 1, 'name' => 'Kripto', 'score' => 0.684],
    ['rank' => 2, 'name' => 'SBN Ritel', 'score' => 0.300],
    ...
]
```

---

## File 3: test_validation.php

**Fungsi:** Test suite untuk validasi semua logic

**Mengetes 3 skenario:**

### **TEST 1: AHP Calculator**
- Input: Pairwise comparison matrix (dari BAGIAN 1.1)
- Validasi: Output matches dengan BAGIAN 1.6
- Expected: weights, lambda_max, CR sesuai manual

### **TEST 2: TOPSIS Calculator**
- Input: Decision matrix + AHP weights (dari BAGIAN 2 & 4)
- Validasi: Output ranking matches manual
- Expected: Ranking order & scores sesuai BAGIAN 4

### **TEST 3: Hybrid Fusion**
- Input: User A adjustment scenario (dari BAGIAN 4.1)
- Calculation: w_adjusted = w_expert + adjustment → normalize
- Validasi: Recalculate TOPSIS dengan adjusted weights
- Expected: Ranking changes sesuai preferensi user

---

## Cara Menjalankan

### **Persyaratan:**
- PHP 7.0+ sudah terinstall
- Terminal (cmd, PowerShell, atau linux terminal)

### **Step 1: Verifikasi PHP**
```powershell
php -v
```

Jika PHP tidak recognized, set PATH ke folder PHP Anda.

### **Step 2: Jalankan Test**
```powershell
cd C:\Users\farha\OneDrive\Gambar\Dokumen\SKRIPSI BRAYYY\04_KODE_PHP_VALIDATION

php test_validation.php
```

### **Output esperado:**
```
╔════════════════════════════════════════════════════════════════════════════════╗
║  AHP & TOPSIS VALIDATION TEST - SPK INVESTASI GEN Z                           ║
║  Referensi: BAGIAN 1.6 & BAGIAN 4 (Manual Calculation)                        ║
╚════════════════════════════════════════════════════════════════════════════════╝

═══════════════════════════════════════════════════════════════════════════════
TEST 1: AHP CALCULATOR (Pairwise Comparison Matrix)
═══════════════════════════════════════════════════════════════════════════════

INPUT: Pairwise Comparison Matrix 6×6
Kriteria: K1: Return, K2: Risk, K3: Liquidity, K4: Capital, K5: Income, K6: Access

OUTPUT:
────────────────────────────────────────────────────────────────────────────────
Weights (Eigenvector):
  K1: Return: 0.3067 (30.67%)
  K2: Risk: 0.2377 (23.77%)
  ...

Consistency Test:
  λ_max (Lambda Max):  6.2187
  CI (Consistency Index): 0.0438
  RI (Random Index n=6): 1.24
  CR (Consistency Ratio): 0.0353 (3.53%)
  Status: VALID ✓

────────────────────────────────────────────────────────────────────────────────
VALIDATION vs MANUAL CALCULATION (BAGIAN 1.6):
────────────────────────────────────────────────────────────────────────────────
  Weights[0]: 0.3067 vs 0.3067 ... ✓ OK
  ...
  λ_max: 6.2187 vs 6.2187 ... ✓ OK
  CI: 0.0438 vs 0.0438 ... ✓ OK
  CR: 0.0353 vs 0.0353 ... ✓ OK

✓ TEST 1 PASSED - AHP Calculator Valid!

... (TEST 2 & 3 output)

═══════════════════════════════════════════════════════════════════════════════
SUMMARY
═══════════════════════════════════════════════════════════════════════════════

TEST 1 (AHP Calculator):       ✓ PASSED
TEST 2 (TOPSIS Calculator):    ✓ PASSED
TEST 3 (Hybrid Fusion):        ✓ PASSED

✓✓✓ ALL TESTS PASSED - CODE IS READY FOR DEPLOYMENT ✓✓✓
```

---

## Troubleshooting

### **Error: "PHP command not found"**
Solusi: Set PATH ke folder PHP
```powershell
# Windows
$env:PATH += ";C:\xampp\php"  # atau C:\php (sesuai instalasi)
php -v
```

### **Error: "Division by zero"**
Periksa: Apakah semua elemen pairwise matrix valid (tidak ada 0)?

### **Score tidak sesuai manual**
Periksa:
1. Decision matrix input sudah benar?
2. Bobot dari AHP sudah benar?
3. Criteria type (benefit/cost) sudah benar?

---

## Data Input Reference

### **Pairwise Comparison Matrix (dari BAGIAN 1.1):**
```
         K1     K2     K3     K4     K5     K6
K1 [   1      3      2      3      2      4    ]
K2 [ 1/3      1     1/2     2     1/2     2    ]
K3 [ 1/2      2      1      2     1/2     3    ]
K4 [ 1/3     1/2    1/2     1     1/3     2    ]
K5 [ 1/2      2      2      3      1      2    ]
K6 [ 1/4     1/2    1/3     1/2    1/2     1    ]
```

### **Decision Matrix (dari BAGIAN 2.1):**
```
                K1       K2     K3     K4        K5     K6
Saham          12       50      9      100000    2.5    7
Reksa Dana     10       35      7      50000     2      8
SBN Ritel      6        5       6      1000000   6.5    5
Kripto         45       85      10     10000     0      6
Emas Digital   4        15      8      100000    0      8
```

---

## Expected Results

### **AHP Weights (dari BAGIAN 1.6):**
- K1 (Return): 0.3067 (30.67%)
- K2 (Risk): 0.2377 (23.77%)
- K3 (Liquidity): 0.1377 (13.77%)
- K4 (Capital): 0.1055 (10.55%)
- K5 (Income): 0.1308 (13.08%)
- K6 (Access): 0.0817 (8.17%)

**Consistency:**
- λ_max = 6.2187
- CI = 0.0438
- CR = 0.0353 (3.53%)
- Status = VALID ✓

### **TOPSIS Ranking (dari BAGIAN 4.6 - User A Aggressive):**
- 1. Kripto: 0.684
- 2. Saham: 0.300
- 3. SBN Ritel: 0.299
- 4. Emas Digital: 0.282
- 5. Reksa Dana: 0.238

---

## Selanjutnya

Setelah validasi successful, kode ini siap untuk:
1. **Integrate ke web application** (HTML/CSS/JavaScript)
2. **Connect dengan MySQL database**
3. **Create REST API** dengan PHP
4. **Deploy ke production** server

---

**Status:** Ready for validation ✓  
**Last Updated:** 29 April 2026
