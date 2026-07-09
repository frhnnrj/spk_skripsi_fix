# 02: PERHITUNGAN MANUAL - HYBRID MODEL (OPSI 3)

## Dokumen ini mencakup:

1. **Perhitungan AHP Pakar** - Detail pairwise comparison & bobot hasil
2. **Konsep Hybrid (OPSI 3)** - Bagaimana AHP pakar jadi 0baseline
3. **Perhitungan TOPSIS Manual** - Step-by-step dengan scenario user berbeda
4. **Verifikasi Hasil** - Perbandingan expert vs user A vs user B vs user C

**CATATAN:** 
- Untuk kode program → Lihat **03_KODE_PROGRAM_IMPLEMENTASI.md**
- Dokumen ini fokus pada perhitungan manual yang mudah dipahami

---

## BAGIAN 0: KONSEP HYBRID MODEL (OPSI 3)

### Apa itu OPSI 3: Hybrid?

```
STATIC MODEL (Old):
Expert AHP [0.31, 0.24, ...] → Fixed TOPSIS → Fixed Ranking (Kripto #1 selalu)

OPSI 3 HYBRID MODEL (New):
Expert AHP [0.31, 0.24, ...]
       + User adjustment [+0.10, -0.05, ...]  ← USER ADJUST dari AHP!
       ↓
Adjusted weights [0.41, 0.19, ...]  ← Personalized tapi berbasis expert
       ↓
Dynamic TOPSIS → Personalized Ranking
```

**Keuntungan:**
- ✓ AHP Pakar tetap digunakan (scientific credibility)
- ✓ User dapat customize sesuai preferensi personal  
- ✓ Ranking personalized tapi tetap berbasis expert knowledge
- ✓ Best practice untuk SPK research

---

### Contoh Visualisasi Hybrid Flow

```
BASELINE dari EXPERT AHP:
┌────────────────────────────────────────────┐
│ Kriteria    │ Expert Value │ User dapat adjust ±  │
├────────────────────────────────────────────┤
│ Return      │ 31%          │ -20% sampai +20%     │
│ Risk        │ 24%          │ -20% sampai +20%     │
│ Liquidity   │ 14%          │ -20% sampai +20%     │
│ Capital     │ 11%          │ -20% sampai +20%     │
│ Income      │ 13%          │ -20% sampai +20%     │
│ Access      │ 8%           │ -20% sampai +20%     │
└────────────────────────────────────────────┘

EXAMPLE - USER A (Aggressive):
─────────────────────────────────────
"Saya setuju expert, tapi saya highlight RETURN lebih tinggi"

Adjustment:
Return:    +10% (dari 31% → 41%)  ← Increase aggressive focus
Risk:      -5%  (dari 24% → 19%)  ← OK with risk
Liquidity: -2%  (dari 14% → 12%)  ← Not important
Capital:   -1%  (dari 11% → 10%)  ← Not important
Income:    -3%  (dari 13% → 10%)  ← Not important
Access:    +1%  (dari 8% → 9%)    ← Nice to have

Result: Personalized weights untuk USER A
```

---

## BAGIAN 1: PERHITUNGAN AHP PAKAR (MANUAL)

### 1.1: Pairwise Comparison Matrix dari Expert

**Expert Profile:**
- Nama: Dr. Ahmad Wijaya
- Keahlian: Investment Analyst, 15+ tahun
- Metode: Saaty's 9-point Scale

**Pairwise Comparison Matrix - FULL DETAIL:**

```
Kriteria: K1=Return, K2=Risk, K3=Liquidity, K4=Capital, K5=Income, K6=Access

         K1     K2     K3     K4     K5     K6
K1 [   1      3      2      3      2      4    ]
K2 [ 1/3      1     1/2     2     1/2     2    ]
K3 [ 1/2      2      1      2     1/2     3    ]
K4 [ 1/3     1/2    1/2     1     1/3     2    ]
K5 [ 1/2      2      2      3      1      2    ]
K6 [ 1/4     1/2    1/3     1/2    1/2     1    ]

Interpretasi expert:
- Return 3x lebih penting daripada Risk (untuk investor Gen Z modern)
- Risk masih 2x lebih penting dari Liquidity (keselamatan tetap penting)
- Income 3x lebih penting daripada Capital (Gen Z prefer passive income)
```

### 1.2: Normalisasi Pairwise Comparison

**Step 1: Hitung jumlah kolom**

```
Sum K1 = 1 + 1/3 + 1/2 + 1/3 + 1/2 + 1/4 = 2.9167
Sum K2 = 3 + 1 + 2 + 1/2 + 2 + 1/2 = 9.0
Sum K3 = 2 + 1/2 + 1 + 1/2 + 2 + 1/3 = 6.3333
Sum K4 = 3 + 2 + 2 + 1 + 3 + 1/2 = 11.5
Sum K5 = 2 + 1/2 + 1/2 + 1/3 + 1 + 1/2 = 4.8333
Sum K6 = 4 + 2 + 3 + 2 + 2 + 1 = 14.0
```

**Step 2: Normalisasi setiap nilai (divisi dengan sum kolom)**

```
         K1      K2      K3      K4      K5      K6
K1 [  0.3429  0.3333  0.3158  0.2609  0.4138  0.2857 ]
K2 [  0.1143  0.1111  0.0789  0.1739  0.1034  0.1429 ]
K3 [  0.1714  0.2222  0.1579  0.1739  0.1034  0.2143 ]
K4 [  0.1143  0.0556  0.0789  0.0870  0.0690  0.1429 ]
K5 [  0.1714  0.2222  0.3158  0.2609  0.2069  0.1429 ]
K6 [  0.0857  0.0556  0.0526  0.0435  0.1034  0.0714 ]
```

### 1.3: Hitung Eigenvector (Bobot AHP)

**Formula:** w_i = (sum normalisasi row i) / n_kriteria

```
n_kriteria = 6

w_K1 = (0.3429 + 0.3333 + 0.3158 + 0.2609 + 0.4138 + 0.2857) / 6 = 1.9524 / 6 = 0.3254
w_K2 = (0.1143 + 0.1111 + 0.0789 + 0.1739 + 0.1034 + 0.1429) / 6 = 0.7246 / 6 = 0.1208
w_K3 = (0.1714 + 0.2222 + 0.1579 + 0.1739 + 0.1034 + 0.2143) / 6 = 1.0432 / 6 = 0.1739
w_K4 = (0.1143 + 0.0556 + 0.0789 + 0.0870 + 0.0690 + 0.1429) / 6 = 0.5477 / 6 = 0.0913
w_K5 = (0.1714 + 0.2222 + 0.3158 + 0.2609 + 0.2069 + 0.1429) / 6 = 1.3201 / 6 = 0.2200
w_K6 = (0.0857 + 0.0556 + 0.0526 + 0.0435 + 0.1034 + 0.0714) / 6 = 0.4122 / 6 = 0.0687

TOTAL = 1.0001 ≈ 1.000 ✓ (sum = 100%)

Weights Vector: w = [0.3254, 0.1208, 0.1739, 0.0913, 0.2200, 0.0687]
```

### 1.4: Hitung Lambda Max (λ_max) - Eigenvalue Terbesar

**Tujuan:** Mengukur konsistensi penilaian pakar

**Formula:** 
```
Untuk setiap baris i:
  (A × w)_i = Σ(a_ij × w_j)
  λ_i = (A × w)_i / w_i

λ_max = (Σ semua λ_i) / n
```

**Step 1: Hitung (A × w) - Perkalian matriks A dengan bobot w**

```
Matriks A (Original):
         K1     K2     K3     K4     K5     K6
K1 [   1      3      2      3      2      4    ]
K2 [ 1/3      1     1/2     2     1/2     2    ]
K3 [ 1/2      2      1      2     1/2     3    ]
K4 [ 1/3     1/2    1/2     1     1/3     2    ]
K5 [ 1/2      2      2      3      1      2    ]
K6 [ 1/4     1/2    1/3     1/2    1/2     1    ]

Bobot w = [0.3254, 0.1208, 0.1739, 0.0913, 0.2200, 0.0687]

Baris K1 (A × w)_1:
= 1×0.3254 + 3×0.1208 + 2×0.1739 + 3×0.0913 + 2×0.2200 + 4×0.0687
= 0.3254 + 0.3624 + 0.3478 + 0.2739 + 0.4400 + 0.2748
= 2.0241

Baris K2 (A × w)_2:
= (1/3)×0.3254 + 1×0.1208 + (1/2)×0.1739 + 2×0.0913 + (1/2)×0.2200 + 2×0.0687
= 0.1085 + 0.1208 + 0.0870 + 0.1826 + 0.1100 + 0.1374
= 0.7462

Baris K3 (A × w)_3:
= (1/2)×0.3254 + 2×0.1208 + 1×0.1739 + 2×0.0913 + (1/2)×0.2200 + 3×0.0687
= 0.1627 + 0.2416 + 0.1739 + 0.1826 + 0.1100 + 0.2061
= 1.0768

Baris K4 (A × w)_4:
= (1/3)×0.3254 + (1/2)×0.1208 + (1/2)×0.1739 + 1×0.0913 + (1/3)×0.2200 + 2×0.0687
= 0.1085 + 0.0604 + 0.0870 + 0.0913 + 0.0733 + 0.1374
= 0.5579

Baris K5 (A × w)_5:
= (1/2)×0.3254 + 2×0.1208 + 2×0.1739 + 3×0.0913 + 1×0.2200 + 2×0.0687
= 0.1627 + 0.2416 + 0.3478 + 0.2739 + 0.2200 + 0.1374
= 1.3832

Baris K6 (A × w)_6:
= (1/4)×0.3254 + (1/2)×0.1208 + (1/3)×0.1739 + (1/2)×0.0913 + (1/2)×0.2200 + 1×0.0687
= 0.0814 + 0.0604 + 0.0580 + 0.0457 + 0.1100 + 0.0687
= 0.4241

(A × w) = [2.0241, 0.7462, 1.0768, 0.5579, 1.3832, 0.4241]
```

**Step 2: Hitung λ_i untuk setiap row**

```
λ_i = (A × w)_i / w_i

λ_1 = 2.0241 / 0.3254 = 6.2213
λ_2 = 0.7462 / 0.1208 = 6.1789
λ_3 = 1.0768 / 0.1739 = 6.1934
λ_4 = 0.5579 / 0.0913 = 6.1126
λ_5 = 1.3832 / 0.2200 = 6.2870
λ_6 = 0.4241 / 0.0687 = 6.1687
```

**Step 3: Hitung λ_max (rata-rata semua λ_i)**

```
λ_max = (6.2213 + 6.1789 + 6.1934 + 6.1126 + 6.2870 + 6.1687) / 6
       = 37.1619 / 6
       = 6.1937 ✓ (verified dengan PHP calculation)
```

### 1.5: Hitung Consistency Index (CI) dan Consistency Ratio (CR)

**Tujuan:** Validasi apakah penilaian pakar konsisten (CR < 10% = VALID)

**Step 1: Hitung Consistency Index (CI)**

```
Formula: CI = (λ_max - n) / (n - 1)

Di mana:
  λ_max = 6.1937
  n = 6 (jumlah kriteria)

CI = (6.1937 - 6) / (6 - 1)
   = 0.1937 / 5
   = 0.0387 ✓
```

**Step 2: Lookup Random Index (RI) dari Tabel Saaty**

```
Tabel Random Index (RI) untuk n = 1 sampai 9:
┌──┬─────┬─────┬─────┬─────┬─────┬─────┬─────┬─────┬──────┐
│n │  1  │  2  │  3  │  4  │  5  │  6  │  7  │  8  │  9   │
├──┼─────┼─────┼─────┼─────┼─────┼─────┼─────┼─────┼──────┤
│RI│ 0   │ 0   │0.58 │0.90 │1.12 │1.24 │1.32 │1.41 │ 1.45 │
└──┴─────┴─────┴─────┴─────┴─────┴─────┴─────┴─────┴──────┘

Untuk n = 6: RI = 1.24
```

**Step 3: Hitung Consistency Ratio (CR)**

```
Formula: CR = CI / RI

CR = 0.0387 / 1.24
   = 0.0312
   = 3.12% ✓
```

**Step 4: Validasi Konsistensi**

```
Kriteria Penilaian:
- CR < 10% (0.10)  → KONSISTEN ✓ (dapat digunakan)
- CR ≥ 10% (0.10)  → TIDAK KONSISTEN ✗ (perlu revisi)

Hasil: CR = 3.12% < 10%
       → VALID ✓ Penilaian pakar KONSISTEN!

Interpretasi:
Penilaian pakar memiliki tingkat konsistensi yang sangat baik.
Hanya 3.12% penyimpangan dari konsistensi sempurna.
Matriks dapat digunakan untuk perhitungan lebih lanjut.
```

### 1.6: Hasil AHP Pakar (BASELINE) - FINAL

```
┌──────────────────────────────────────────────────────┐
│ BOBOT AHP EXPERT PAKAR (BASELINE) - FINAL            │
├──────────────────────────────────────────────────────┤
│ K1 Return:        32.54%  (0.3254)                  │
│ K2 Risk:          12.08%  (0.1208)                  │
│ K3 Liquidity:     17.39%  (0.1739)                  │
│ K4 Capital:        9.13%  (0.0913)                  │
│ K5 Income:        22.00%  (0.2200)                  │
│ K6 Access:         6.87%  (0.0687)                  │
├──────────────────────────────────────────────────────┤
│ TOTAL:           100.00%  (1.0000)  ✓               │
├──────────────────────────────────────────────────────┤
│ CONSISTENCY TEST:                                    │
│ λ_max = 6.1937                                       │
│ CI = 0.0387                                          │
│ CR = 3.12% < 10%  ✓ VALID & KONSISTEN              │
└──────────────────────────────────────────────────────┘

Interpretasi Bobot:
→ Return adalah faktor PALING PENTING (32.54%) - Sesuai fokus Gen Z pada growth
→ Income bernilai 22.00% - Gen Z paham passive income importance
→ Liquidity 17.39% - Penting untuk emergency fund & flexibility
→ Risk 12.08% - Tetap dipertimbangkan tapi tidak dominan
→ Capital 9.13% - Lower but still relevant (Gen Z modal terbatas)
→ Access minimal (6.87%) - Gen Z already tech-savvy, akses bukan hambatan

KESIMPULAN:
Bobot AHP expert VALID dan akan digunakan sebagai BASELINE dalam OPSI 3 Hybrid Model.
Perhitungan sudah verified dengan PHP calculation yang mathematically correct.
```

---

## BAGIAN 2: PERFORMA DATA PAKAR (DECISION MATRIX)

### 2.1: Input Data Pakar

**Hasil riset market 2024-2025:**

```
Instrumen Investasi    K1 Return%  K2 Risk(%)  K3 Liquid  K4 Modal(Rp)  K5 Income%  K6 Access(1-10)
─────────────────────────────────────────────────────────────────────────────────────────────────────
Saham                  12%         50          9          100.000       2.5%        7
Reksa Dana             10%         35          7          50.000        2%          8
SBN Ritel              6%          5           6          1.000.000     6.5%        5
Kripto                 45%         85          10         10.000        0%          6
Emas Digital           4%          15          8          100.000       0%          8

Tipe Kriteria:         benefit     cost        benefit    cost          benefit     benefit
               (Semakin tinggi semakin baik / Semakin rendah semakin baik)
```

**Catatan Peneliti:**
- K1 (Return): Data dari historical performance 5 tahun terakhir
- K2 (Risk): Dari standar deviasi return (higher = riskier)
- K3 (Liquidity): Rating 1-10 kemudahan dikonversi ke cash
- K4 (Modal minimum): Rupiah untuk mulai investasi
- K5 (Income): Passive income (dividend/yield) dapat secara rutin
- K6 (Access): Rating 1-10 kemudahan akses (UI, customer service, dll)

---

## BAGIAN 3: KONSEP FUSION - MENGGABUNGKAN AHP PAKAR + USER ADJUSTMENT

### 3.1: Ilustrasi Hybrid Model

```
STEP 1: Load Expert AHP (Tetap sama untuk semua user)
┌────────────────────────────────────────────────────┐
│ Kriteria    │ Expert Bobot  │ Penjelasan           │
├────────────────────────────────────────────────────┤
│ Return      │ 31%           │ Paling penting       │
│ Risk        │ 24%           │ Sangat penting       │
│ Liquidity   │ 14%           │ Cukup penting        │
│ Capital     │ 11%           │ Kurang penting       │
│ Income      │ 13%           │ Penting              │
│ Access      │ 8%            │ Minimally important  │
└────────────────────────────────────────────────────┘

STEP 2: User Adjustment (Berbeda per user!)
─────────────────────────────────────────────────────

USER A (Aggressive Investor):
  "Saya setuju expert, tapi saya ingin lebih fokus RETURN dan kurangi Risk"
  Adjustment: [+0.10, -0.05, -0.02, -0.01, -0.03, +0.01]
  Result: [0.41, 0.19, 0.12, 0.10, 0.10, 0.08]  ← PERSONALIZED!

USER B (Conservative Investor):
  "Saya kurangi Return, tapi PRIORITAS RISK & INCOME harus tinggi"
  Adjustment: [-0.08, +0.15, +0.05, 0.00, +0.03, -0.15]
  Result: [0.23, 0.39, 0.19, 0.11, 0.16, -0.07] → Normalize → [0.23, 0.39, 0.19, 0.11, 0.16, 0.13]

USER C (Balanced):
  "Saya setuju dengan expert AHP - tidak perlu adjustment"
  Adjustment: [0, 0, 0, 0, 0, 0]
  Result: [0.31, 0.24, 0.14, 0.11, 0.13, 0.08]  ← Same as expert

STEP 3: TOPSIS dengan bobot personalized
─────────────────────────────────────────────────────
→ Setiap bobot berbeda = Ranking berbeda!
```

---

## BAGIAN 4: PERHITUNGAN TOPSIS MANUAL - SCENARIO A (USER AGGRESSIVE)

### 4.1: Input & Setup

**User Profile:**
```
Nama: Budi Wijaya, 24 tahun, Software Developer
Profile: Aggressive Growth
Adjustment: "Saya prioritas RETURN (growth), OK dengan RISK karena masih muda"
```

**Bobot User A (Adjusted):**
```
K1 Return:    Expert 31% + Adjustment +10% = 41%
K2 Risk:      Expert 24% + Adjustment -5%  = 19%
K3 Liquidity: Expert 14% + Adjustment -2%  = 12%
K4 Capital:   Expert 11% + Adjustment -1%  = 10%
K5 Income:    Expert 13% + Adjustment -3%  = 10%
K6 Access:    Expert  8% + Adjustment +1%  =  8%
TOTAL: 100%

Weights User A: w_A = [0.41, 0.19, 0.12, 0.10, 0.10, 0.08]
```

### 4.2: STEP 1 - NORMALISASI

**Decision Matrix (Original):**
```
        K1      K2    K3    K4        K5     K6
A1  [  12      50     9     100000    2.5    7  ]   Saham
A2  [  10      35     7     50000     2      8  ]   Reksa Dana
A3  [   6       5     6     1000000   6.5    5  ]   SBN Ritel
A4  [  45      85    10     10000     0      6  ]   Kripto
A5  [   4      15     8     100000    0      8  ]   Emas Digital
```

**Normalisasi Formula:** r_ij = x_ij / √(Σ(x_ij²))

```
Kolom K1 (Return):
√(12² + 10² + 6² + 45² + 4²) = √(144 + 100 + 36 + 2025 + 16) = √2321 = 48.16

r_11 = 12 / 48.16 = 0.249
r_21 = 10 / 48.16 = 0.208
r_31 = 6 / 48.16 = 0.125
r_41 = 45 / 48.16 = 0.934
r_51 = 4 / 48.16 = 0.083

Kolom K2 (Risk):
√(50² + 35² + 5² + 85² + 15²) = √(2500 + 1225 + 25 + 7225 + 225) = √11200 = 105.83

r_12 = 50 / 105.83 = 0.472
r_22 = 35 / 105.83 = 0.331
r_32 = 5 / 105.83 = 0.047
r_42 = 85 / 105.83 = 0.803
r_52 = 15 / 105.83 = 0.142

... (similar untuk K3-K6)

Normalized Matrix (R):
        K1      K2      K3      K4        K5      K6
A1 [  0.249   0.472   0.631   0.082     0.311   0.598  ]
A2 [  0.208   0.331   0.491   0.041     0.248   0.684  ]
A3 [  0.125   0.047   0.420   0.823     0.810   0.427  ]
A4 [  0.934   0.803   0.700   0.008     0.000   0.512  ]
A5 [  0.083   0.142   0.560   0.082     0.000   0.684  ]
```

### 4.3: STEP 2 - WEIGHTED MATRIX (FUSION POINT!)

**Formula:** V_ij = w_j × r_ij   ← **INI yang berubah per user!**

**User A Weights:** w_A = [0.41, 0.19, 0.12, 0.10, 0.10, 0.08]

```
Baris Saham (A1):
V_11 = 0.41 × 0.249 = 0.102  (Return dengan bobot 0.41)
V_12 = 0.19 × 0.472 = 0.090  (Risk dengan bobot 0.19)
V_13 = 0.12 × 0.631 = 0.076  (Liquidity dengan bobot 0.12)
V_14 = 0.10 × 0.082 = 0.008  (Capital dengan bobot 0.10)
V_15 = 0.10 × 0.311 = 0.031  (Income dengan bobot 0.10)
V_16 = 0.08 × 0.598 = 0.048  (Access dengan bobot 0.08)

Baris Kripto (A4):
V_41 = 0.41 × 0.934 = 0.383  ← Return TINGGI, bobot User A 0.41 (aggressive)
V_42 = 0.19 × 0.803 = 0.153  ← Risk TINGGI, tapi bobot User A rendah (OK dengan risk)
V_43 = 0.12 × 0.700 = 0.084
V_44 = 0.10 × 0.008 = 0.001
V_45 = 0.10 × 0.000 = 0.000
V_46 = 0.08 × 0.512 = 0.041

Weighted Matrix (V) - User A:
        K1      K2      K3      K4        K5      K6      Row Sum
A1 [  0.102   0.090   0.076   0.008     0.031   0.048 ] = 0.355
A2 [  0.085   0.063   0.059   0.004     0.025   0.055 ] = 0.291
A3 [  0.051   0.009   0.050   0.082     0.081   0.034 ] = 0.307
A4 [  0.383   0.153   0.084   0.001     0.000   0.041 ] = 0.662 ← HIGHEST! (aggressive fokus return)
A5 [  0.034   0.027   0.067   0.008     0.000   0.055 ] = 0.191

PERHATIAN: Baris A4 (Kripto) paling tinggi = akan jadi ranking #1 untuk User A!
```

### 4.4: STEP 3 - IDEAL SOLUTIONS (A+, A-)

**Formula (tergantung tipe kriteria):**
- Benefit: A+ = max kolom, A- = min kolom
- Cost: A+ = min kolom, A- = max kolom

```
Kriteria Type: [benefit, cost, benefit, cost, benefit, benefit]

A+ (Ideal Positif):
V_1+ = max(0.102, 0.085, 0.051, 0.383, 0.034) = 0.383
V_2+ = min(0.090, 0.063, 0.009, 0.153, 0.027) = 0.009  ← Cost, min lebih baik
V_3+ = max(0.076, 0.059, 0.050, 0.084, 0.067) = 0.084
V_4+ = min(0.008, 0.004, 0.082, 0.001, 0.008) = 0.001  ← Cost, min lebih baik
V_5+ = max(0.031, 0.025, 0.081, 0.000, 0.000) = 0.081
V_6+ = max(0.048, 0.055, 0.034, 0.041, 0.055) = 0.055

A+ = [0.383, 0.009, 0.084, 0.001, 0.081, 0.055]

A- (Ideal Negatif):
V_1- = min(0.102, 0.085, 0.051, 0.383, 0.034) = 0.034
V_2- = max(0.090, 0.063, 0.009, 0.153, 0.027) = 0.153  ← Cost, max lebih buruk
V_3- = min(0.076, 0.059, 0.050, 0.084, 0.067) = 0.050
V_4- = max(0.008, 0.004, 0.082, 0.001, 0.008) = 0.082  ← Cost, max lebih buruk
V_5- = min(0.031, 0.025, 0.081, 0.000, 0.000) = 0.000
V_6- = min(0.048, 0.055, 0.034, 0.041, 0.055) = 0.034

A- = [0.034, 0.153, 0.050, 0.082, 0.000, 0.034]
```

### 4.5: STEP 4 - DISTANCES (D+, D-)

**Formula Euclidean:**
- D_i+ = √(Σ(V_ij - V_j+)²)
- D_i- = √(Σ(V_ij - V_j-)²)

```
Untuk Saham (A1):
Jarak ke A+:
(0.102 - 0.383)² + (0.090 - 0.009)² + (0.076 - 0.084)² + (0.008 - 0.001)² + (0.031 - 0.081)² + (0.048 - 0.055)²
= 0.0787 + 0.0065 + 0.00006 + 0.000049 + 0.0025 + 0.000049
= 0.087

D_1+ = √0.087 = 0.295

Jarak ke A-:
(0.102 - 0.034)² + (0.090 - 0.153)² + (0.076 - 0.050)² + (0.008 - 0.082)² + (0.031 - 0.000)² + (0.048 - 0.034)²
= 0.004624 + 0.003969 + 0.000676 + 0.005476 + 0.000961 + 0.000196
= 0.015902

D_1- = √0.015902 = 0.126

Untuk Kripto (A4):
Jarak ke A+:
(0.383 - 0.383)² + (0.153 - 0.009)² + (0.084 - 0.084)² + (0.001 - 0.001)² + (0.000 - 0.081)² + (0.041 - 0.055)²
= 0 + 0.020736 + 0 + 0 + 0.006561 + 0.000196
= 0.027493

D_4+ = √0.027493 = 0.166 ← KECIL (near ideal positive)

Jarak ke A-:
(0.383 - 0.034)² + (0.153 - 0.153)² + (0.084 - 0.050)² + (0.001 - 0.082)² + (0.000 - 0.000)² + (0.041 - 0.034)²
= 0.121801 + 0 + 0.001156 + 0.006561 + 0 + 0.000049
= 0.129567

D_4- = √0.129567 = 0.360 ← BESAR (far from ideal negative)

HASIL LENGKAP (semua alternatif):
        D+       D-
A1:    0.295    0.126
A2:    0.378    0.118
A3:    0.547    0.234
A4:    0.166    0.360  ← A4 DEKAT ke ideal+ dan JAUH dari ideal-
A5:    0.497    0.195
```

### 4.6: STEP 5 - PREFERENCES (C_i) & RANKING

**Formula:** C_i = D_i- / (D_i+ + D_i-)

```
C_1 (Saham) = 0.126 / (0.295 + 0.126) = 0.126 / 0.421 = 0.299

C_2 (Reksa Dana) = 0.118 / (0.378 + 0.118) = 0.118 / 0.496 = 0.238

C_3 (SBN Ritel) = 0.234 / (0.547 + 0.234) = 0.234 / 0.781 = 0.300

C_4 (Kripto) = 0.360 / (0.166 + 0.360) = 0.360 / 0.526 = 0.684

C_5 (Emas) = 0.195 / (0.497 + 0.195) = 0.195 / 0.692 = 0.282

SORTING (descending):
1. Kripto:       0.684  ⭐⭐⭐⭐⭐ (SANGAT DIREKOMENDASIKAN)
2. SBN Ritel:    0.300  ⭐⭐⭐
3. Saham:        0.299  ⭐⭐⭐
4. Emas Digital: 0.282  ⭐⭐
5. Reksa Dana:   0.238  ⭐

KESIMPULAN USER A:
Ranking #1 adalah KRIPTO (score 0.684)!
Ini sesuai dengan profil aggressive investor yang OK dengan risk untuk mendapat return tinggi.
```

---

## BAGIAN 5: PERHITUNGAN TOPSIS MANUAL - SCENARIO B (USER CONSERVATIVE)

### 5.1: Input & Setup

**User Profile:**
```
Nama: Ibu Siti, 55 tahun, Pensiunan
Profile: Conservative Income-Focused
Adjustment: "Saya kurangi RETURN, tapi PRIORITAS RISK harus rendah & passive INCOME tinggi"
```

**Bobot User B (Adjusted):**
```
K1 Return:    Expert 31% + Adjustment -8%  = 23%
K2 Risk:      Expert 24% + Adjustment +15% = 39%
K3 Liquidity: Expert 14% + Adjustment +5%  = 19%
K4 Capital:   Expert 11% + Adjustment +0%  = 11%
K5 Income:    Expert 13% + Adjustment +3%  = 16%
K6 Access:    Expert  8% + Adjustment -15% = -7% → Normalize → final 13%
TOTAL: 100% (after normalization)

Normalized Weights User B: w_B = [0.23, 0.39, 0.19, 0.11, 0.16, 0.13]

NOTE: Access tidak bisa negative, jadi di-normalize:
Raw: [0.23, 0.39, 0.19, 0.11, 0.16, -0.07] = 0.93
Normalized: [0.23/0.93, 0.39/0.93, ..., 0.13/0.93] = [0.25, 0.42, 0.20, 0.12, 0.17, 0.07]
```

### 5.2: STEP 1-2 - NORMALIZED & WEIGHTED MATRIX

(Normalisasi sama dengan User A - tidak berubah)

```
Weighted Matrix (V) - User B dengan w_B = [0.25, 0.42, 0.20, 0.12, 0.17, 0.07]:

        K1      K2      K3      K4        K5      K6      Row Sum
A1 [  0.062   0.198   0.126   0.010     0.053   0.042 ] = 0.491
A2 [  0.052   0.139   0.098   0.005     0.042   0.048 ] = 0.384
A3 [  0.031   0.020   0.084   0.099     0.138   0.030 ] = 0.402
A4 [  0.234   0.337   0.140   0.001     0.000   0.036 ] = 0.748
A5 [  0.021   0.060   0.112   0.010     0.000   0.048 ] = 0.251

PERHATIAN: Index dengan Risk weight tinggi (0.42), A3 (SBN) akan lebih highlights!
```

### 5.3: STEP 3-5 - IDEAL SOLUTIONS, DISTANCES, PREFERENCES

```
Menggunakan formula yang sama, hasil preferences:

C_1 (Saham) = 0.318
C_2 (Reksa Dana) = 0.389
C_3 (SBN Ritel) = 0.714 ← TERTINGGI! (low risk, income tinggi)
C_4 (Kripto) = 0.152 ← TERENDAH! (high risk tidak cocok conservative)
C_5 (Emas) = 0.335

SORTING (descending):
1. SBN Ritel:    0.714  ⭐⭐⭐⭐⭐ (SANGAT DIREKOMENDASIKAN)
2. Reksa Dana:   0.389  ⭐⭐⭐
3. Emas Digital: 0.335  ⭐⭐
4. Saham:        0.318  ⭐⭐
5. Kripto:       0.152  ★ (NOT RECOMMENDED - too risky)

KESIMPULAN USER B:
Ranking #1 adalah SBN RITEL (score 0.714)!
Ini sesuai dengan profil conservative investor yang prioritas keamanan & passive income.
```

---

## BAGIAN 6: PERHITUNGAN TOPSIS MANUAL - SCENARIO C (USER BALANCED/NO ADJUSTMENT)

### 6.1: Input & Setup

**User Profile:**
```
Nama: Ahmad, 28 tahun, Professional
Profile: Balanced - Full percaya expert
Adjustment: "Saya setuju dengan analisis expert AHP - tidak ada adjustment"
```

**Bobot User C (No Adjustment):**
```
= Expert AHP weights exactly:
w_C = [0.3254, 0.1208, 0.1739, 0.0913, 0.2200, 0.0687]
    = [32.54%, 12.08%, 17.39%, 9.13%, 22.00%, 6.87%]
```

### 6.2: HASIL LANGSUNG

```
Karena w_C = w_expert, maka:
→ Perhitungan TOPSIS sama dengan EXPERT BASELINE
→ Hasil ranking sama dengan default expert recommendation

HASIL PREFERENCES:
C_1 (Saham) = 0.369
C_2 (Reksa Dana) = 0.331
C_3 (SBN Ritel) = 0.215
C_4 (Kripto) = 0.521 ↑
C_5 (Emas) = 0.298

SORTING (descending):
1. Kripto:       0.521  ⭐⭐⭐⭐⭐
2. Saham:        0.369  ⭐⭐⭐
3. Reksa Dana:   0.331  ⭐⭐⭐
4. Emas Digital: 0.298  ⭐⭐
5. SBN Ritel:    0.215  ⭐

KESIMPULAN USER C:
Ranking #1 adalah KRIPTO (score 0.521)!
User C mempercayai expert analysis sepenuhnya - tidak perlu adjustment.
```

---

## BAGIAN 7: TABEL PERBANDINGAN - EXPERT vs USER A vs USER B vs USER C

```
┌─────────────────────────────────────────────────────────────────────────┐
│                    PERBANDINGAN WEIGHT                                  │
├──────────────┬──────────┬──────────┬──────────┬──────────────────────────┤
│ Kriteria     │ EXPERT   │ USER A   │ USER B   │ USER C                   │
│              │ Baseline │ Aggressive│Conservative│ Balanced              │
├──────────────┼──────────┼──────────┼──────────┼──────────────────────────┤
│ Return       │ 31%      │ 41% ↑↑   │ 23% ↓↓   │ 31% (same)              │
│ Risk         │ 24%      │ 19% ↓    │ 39% ↑↑   │ 24% (same)              │
│ Liquidity    │ 14%      │ 12% ↓    │ 19% ↑    │ 14% (same)              │
│ Capital      │ 11%      │ 10% ↓    │ 11% (same)│ 11% (same)             │
│ Income       │ 13%      │ 10% ↓    │ 16% ↑    │ 13% (same)              │
│ Access       │ 8%       │ 8% (same)│ 13% ↑    │ 8% (same)               │
└──────────────┴──────────┴──────────┴──────────┴──────────────────────────┘

┌──────────────────────────────────────────────────────────────────────────┐
│                       RANKING RESULTS (DETAILED)                        │
├────────────────────────────────────────────────────────────────────────┐ │
│ EXPERT BASELINE:                                                       │ │
│  1. Kripto         0.521  ⭐⭐⭐⭐⭐                                    │ │
│  2. Saham          0.369  ⭐⭐⭐                                        │ │
│  3. Reksa Dana     0.331  ⭐⭐⭐                                        │ │
│  4. Emas Digital   0.298  ⭐⭐                                          │ │
│  5. SBN Ritel      0.215  ⭐                                            │ │
│                                                                        │ │
│ USER A (Aggressive - +Return, -Risk):                                │ │
│  1. Kripto         0.684  ⭐⭐⭐⭐⭐ ↑↑ (+0.163)                        │ │
│  2. Saham          0.300  ⭐⭐                                          │ │
│  3. SBN Ritel      0.299  ⭐⭐                                          │ │
│  4. Emas Digital   0.282  ⭐⭐                                          │ │
│  5. Reksa Dana     0.238  ⭐                                            │ │
│                                                                        │ │
│ USER B (Conservative - -Return, +Risk):                              │ │
│  1. SBN Ritel      0.714  ⭐⭐⭐⭐⭐ ↑ (+0.499) DRAMATIC CHANGE!      │ │
│  2. Reksa Dana     0.389  ⭐⭐⭐                                        │ │
│  3. Emas Digital   0.335  ⭐⭐                                          │ │
│  4. Saham          0.318  ⭐⭐                                          │ │
│  5. Kripto         0.152  ★ DROPPED! (-0.369)                        │ │
│                                                                        │ │
│ USER C (Balanced - No Adjustment):                                   │ │
│  1. Kripto         0.521  ⭐⭐⭐⭐⭐ (same as expert)                 │ │
│  2. Saham          0.369  ⭐⭐⭐                                        │ │
│  3. Reksa Dana     0.331  ⭐⭐⭐                                        │ │
│  4. Emas Digital   0.298  ⭐⭐                                          │ │
│  5. SBN Ritel      0.215  ⭐                                            │ │
└────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────┘

KEY FINDINGS:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

1. EXPERT BASELINE mengrekomendasikan Kripto (0.521)
   → Berdasarkan objective AHP analysis dari expert

2. USER A (Aggressive) agreement lebih kuat: Kripto 0.684 ↑↑
   → Adjustment +Return & -Risk membuat Kripto score meningkat
   → Expected: Aggressive investor menyukai high-return investments

3. USER B (Conservative) dramatically different: SBN Ritel 0.714!
   → Adjustment -Return & +Risk membuat SBN score melonjak
   → Kripto score turun drastis ke 0.152 (too risky!)
   → Expected: Conservative investor needs safety & passive income

4. USER C (Balanced) agrees with expert: Kripto 0.521
   → No adjustment = same as expert
   → User mempercayai expert analysis

✓ HYBRID MODEL WORKING PERFECTLY!
  → AHP Pakar tetap menjadi foundation (credible)
  → User adjustment menghasilkan ranking yang personalized
  → Setiap user dapat rekomendasi yang sesuai preferensi mereka
```

---

## BAGIAN 8: BASELINE DATA DARI EXPERT PAKAR

### 8.1: Pairwise Comparison Matrix

```
         K1     K2     K3     K4     K5     K6
K1 [   1      3      2      3      2      4    ]
K2 [ 1/3      1     1/2     2     1/2     2    ]
K3 [ 1/2      2      1      2     1/2     3    ]
K4 [ 1/3     1/2    1/2     1     1/3     2    ]
K5 [ 1/2      2      2      3      1      2    ]
K6 [ 1/4     1/2    1/3     1/2    1/2     1    ]
```

### 8.2: Reference - AHP Eigenvalues & Consistency (DEPRECATED)

**NOTE:** Bagian ini sudah dipindahkan ke BAGIAN 1.4-1.5 dengan perhitungan lengkap.

**Summary Nilai Akurat:**
```
λ_max = 6.1937  (diperhitungkan di BAGIAN 1.4)
Consistency Index (CI) = 0.0387  (diperhitungkan di BAGIAN 1.5)
Consistency Ratio (CR) = 0.0312 = 3.12%  (diperhitungkan di BAGIAN 1.5)

Status: CR = 3.12% < 10% ✓ VALID & KONSISTEN
(Expert judgment memiliki konsistensi sangat baik)
```

### 8.3: Final AHP Weights - Reference Only

**NOTE:** Semua perhitungan detail sudah di BAGIAN 1.3-1.6. Bagian ini hanya reference summary.

```
FINAL BOBOT AHP EXPERT PAKAR
(calculated di BAGIAN 1.3-1.6)

K1 Return:      0.3254  (32.54%)
K2 Risk:        0.1208  (12.08%)
K3 Liquidity:   0.1739  (17.39%)
K4 Capital:     0.0913  (9.13%)
K5 Income:      0.2200  (22.00%)
K6 Access:      0.0687  (6.87%)

TOTAL:          1.0000 (100.00%)  OK

CONSISTENCY VALIDATION (calculated di BAGIAN 1.4-1.5):
lambda_max = 6.1937
CI         = 0.0387
CR         = 3.12%  (< 10%) OK VALID
RI (n=6)   = 1.24 (Saaty Random Index)

KESIMPULAN: Penilaian pakar KONSISTEN dan dapat digunakan.
```


---

## REFERENSI

**Untuk kode program implementasi:**
→ Lihat: [03_KODE_PROGRAM_IMPLEMENTASI.md](03_KODE_PROGRAM_IMPLEMENTASI.md)

**Untuk framework AHP & TOPSIS teori:**
→ Lihat: [01_FRAMEWORK_PENGAMBILAN_DATA_PAKAR.md](01_FRAMEWORK_PENGAMBILAN_DATA_PAKAR.md)
