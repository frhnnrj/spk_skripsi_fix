# FRAMEWORK PENGAMBILAN DATA DARI PAKAR
## Sistem Pendukung Keputusan Investasi Generasi Z (AHP-TOPSIS)

**Dokumen ini menjelaskan secara detail:**
- Siapa pakar yang dibutuhkan (1 pakar)
- Apa saja yang ditanyakan ke pakar
- Bagaimana bentuk data mentah
- Cara mengubah data pakar menjadi input sistem
- Catatan: Representasi Gen Z sudah built-in dalam alternatif investasi yang dipilih

---

## BAGIAN 1: IDENTIFIKASI PAKAR

### 1.1 Pakar yang Dibutuhkan

**Untuk SPK investasi Gen Z, dibutuhkan 1 pakar saja:**

#### **PAKAR: Financial Expert / Investment Professional**
- **Peran**: Memberikan dua hal:
  1. **Penilaian Perbandingan Berpasangan** (untuk AHP)
  2. **Data Performa Alternatif Investasi** (untuk TOPSIS)
  
- **Kualifikasi**:
  - Minimal 3-5 tahun pengalaman di bidang investasi
  - Memahami karakteristik 5 alternatif investasi: Saham, Reksa Dana, SBN, Kripto, Emas Digital
  - Memiliki data akurat dan terkini tentang instrumen investasi
  - Dapat memberikan penilaian objektif terhadap karakteristik investasi
  
- **Jumlah yang direkomendasikan**: 1 pakar
  - *Alasan*: Simplifikasi proses pengambilan data, fokus pada kualitas data dari satu expert terpercaya

**Tugas Pakar:**
Memberikan penilaian perbandingan berpasangan AHP + data karakteristik 5 alternatif pada 6 kriteria.


---

### 1.2 Catatan tentang Representasi Gen Z

**Perspektif Gen Z sudah dipertimbangkan dalam:**

1. **Pilihan Alternatif Investasi:**
   - Saham (trading platform modern)
   - Reksa Dana (fintech products)
   - SBN Ritel (government product untuk retail)
   - **Kripto** (trending, Gen Z focused)
   - **Emas Digital** (accessibility, technology-first)
   
   → 2 dari 5 alternatif (Kripto & Emas Digital) adalah pilihan yang secara konvensional lebih dikenal di kalangan Gen Z

2. **Kriteria Evaluasi:**
   - Modal Awal (rendah = accessible untuk Gen Z)
   - Kemudahan Akses (user-friendly interface)
   - Likuiditas (instant needs support cash flow muda)
   
   → 3 kriteria ini langsung relevan dengan karakteristik Gen Z

3. **Dalam Penilaian Performa:**
   - Pakar mengevaluasi masing-masing alternatif dari perspektif Gen Z investor pemula
   - Tidak hanya dari sudut pandang investor institusional

**Oleh karena itu, tidak diperlukan "validator Gen Z" terpisah** - Gen Z voice sudah terintegrasi dalam design sistem.

---

## BAGIAN 2: PERTANYAAN YANG HARUS DIAJUKAN KE PAKAR

### 2.1 PERTANYAAN UNTUK PAKAR (Bagian A: AHP Pairwise Comparison)

**Konteks:**
Pakar akan membandingkan 6 kriteria investasi secara berpasangan menggunakan skala Saaty.

**6 Kriteria:**
1. **K1: Potensi Keuntungan (Return)** - Ukuran keuntungan yang bisa didapat
2. **K2: Risiko** - Kemungkinan kerugian atau volatilitas
3. **K3: Likuiditas** - Kemudahan mengubah investasi menjadi uang tunai
4. **K4: Modal Awal** - Jumlah uang minimum untuk memulai investasi
5. **K5: Pendapatan Berkala** - Hasil dividen/bunga yang diterima berkala
6. **K6: Kemudahan Akses** - Kemudahan untuk investor pemula (Gen Z) mengakses

**PERTANYAAN KE PAKAR 1:**

```
Saya akan menanyakan perbandingan antara pasangan kriteria investasi.
Gunakan skala berikut:

1 = Sama penting
3 = Sedikit lebih penting
5 = Lebih penting
7 = Sangat lebih penting
9 = Ekstrem lebih penting
2,4,6,8 = Nilai antara dua pilihan

INSTRUKSI:
- Berikan penilaian dari sudut pandang Gen Z yang baru memulai investasi
- Jika kriteria A lebih penting dari B, tulis nilai positif (misal: A lebih penting daripada B, score 3)
- Jika kriteria B lebih penting dari A, tulis nilai pecahan (misal: B lebih penting, score 1/3)
- Pertimbangkan keseimbangan antara return, safety, dan akses

PERTANYAAN:
1. Potensi Keuntungan (K1) dibanding Risiko (K2)?
   [Berikan alasan]
   
2. Potensi Keuntungan (K1) dibanding Likuiditas (K3)?
   [Berikan alasan]
   
3. Potensi Keuntungan (K1) dibanding Modal Awal (K4)?
   [Berikan alasan]
   
4. Potensi Keuntungan (K1) dibanding Pendapatan Berkala (K5)?
   [Berikan alasan]
   
5. Potensi Keuntungan (K1) dibanding Kemudahan Akses (K6)?
   [Berikan alasan]
   
6. Risiko (K2) dibanding Likuiditas (K3)?
   [Berikan alasan]
   
7. Risiko (K2) dibanding Modal Awal (K4)?
   [Berikan alasan]
   
8. Risiko (K2) dibanding Pendapatan Berkala (K5)?
   [Berikan alasan]
   
9. Risiko (K2) dibanding Kemudahan Akses (K6)?
   [Berikan alasan]
   
10. Likuiditas (K3) dibanding Modal Awal (K4)?
    [Berikan alasan]
    
11. Likuiditas (K3) dibanding Pendapatan Berkala (K5)?
    [Berikan alasan]
    
12. Likuiditas (K3) dibanding Kemudahan Akses (K6)?
    [Berikan alasan]
    
13. Modal Awal (K4) dibanding Pendapatan Berkala (K5)?
    [Berikan alasan]
    
14. Modal Awal (K4) dibanding Kemudahan Akses (K6)?
    [Berikan alasan]
    
15. Pendapatan Berkala (K5) dibanding Kemudahan Akses (K6)?
    [Berikan alasan]
```

**Output yang diharapkan:**
Matriks perbandingan 6x6 dengan nilai-nilai dari pakar

---

### 2.2 PERTANYAAN UNTUK PAKAR (Bagian B: Data Performa Alternatif)

**Konteks:**
Pakar akan memberikan rating/nilai untuk setiap alternatif investasi pada setiap kriteria.

**5 Alternatif Investasi:**
1. Saham
2. Reksa Dana
3. SBN Ritel (Surat Berharga Negara Ritel)
4. Kripto (Bitcoin/Ethereum)
5. Emas Digital

**6 Kriteria:**
(Sama seperti di pakar 1)

**PERTANYAAN KE PAKAR 2:**

```
Berdasarkan pengalaman dan data terkini, berikan rating untuk setiap 
alternatif investasi pada kriteria berikut:

INSTRUKSI PENILAIAN:
- Skala rating berbeda tergantung tipe kriteria
- Untuk setiap kriteria, pakar bisa memberikan nilai berdasarkan:
  * Data historis 1-5 tahun terakhir
  * Kondisi pasar saat ini
  * Proyeksi ke depan
  * Perspektif investor Gen Z

============================================
KRITERIA 1: POTENSI KEUNTUNGAN (Return)
============================================
Rata-rata return tahunan (%) dari masing-masing alternatif:
- Saham: ___% per tahun
- Reksa Dana: ___% per tahun
- SBN Ritel: ___% per tahun
- Kripto: ___% per tahun
- Emas Digital: ___% per tahun

[Catatan Pakar tentang volatilitas dan kondisi pasar]

============================================
KRITERIA 2: RISIKO
============================================
Tingkat risiko pada skala 1-100 (1=sangat aman, 100=sangat berisiko):
- Saham: ___ (alasan: ___)
- Reksa Dana: ___ (alasan: ___)
- SBN Ritel: ___ (alasan: ___)
- Kripto: ___ (alasan: ___)
- Emas Digital: ___ (alasan: ___)

[Catatan tentang volatilitas, regulasi, dan fail risk]

============================================
KRITERIA 3: LIKUIDITAS
============================================
Kemudahan mencairkan investasi pada skala 1-10 (1=sulit, 10=sangat mudah):
- Saham: ___ (waktu: ___ jam/hari)
- Reksa Dana: ___ (waktu: ___ hari kerja)
- SBN Ritel: ___ (waktu: ___ hari)
- Kripto: ___ (waktu: ___ menit)
- Emas Digital: ___ (waktu: ___ jam/hari)

[Catatan tentang biaya transaksi dan kesulitan praktis]

============================================
KRITERIA 4: MODAL AWAL (dalam Rupiah)
============================================
Berapa minimal uang yang harus disediakan untuk mulai investasi:
- Saham: Rp ___
- Reksa Dana: Rp ___
- SBN Ritel: Rp ___
- Kripto: Rp ___
- Emas Digital: Rp ___

[Catatan tentang persyaratan broker/platform]

============================================
KRITERIA 5: PENDAPATAN BERKALA
============================================
Rata-rata dividen/bunga/yield per tahun (%):
- Saham: ___% (dari dividen saja, tidak termasuk capital gain)
- Reksa Dana: ___% (yield)
- SBN Ritel: ___% (kupon/bunga pasti)
- Kripto: ___% (staking yield, jika ada)
- Emas Digital: ___% (tidak ada yield, tulis 0)

[Catatan tentang konsistensi pembayaran]

============================================
KRITERIA 6: KEMUDAHAN AKSES
============================================
Tingkat kesulitan untuk Gen Z pemula pada skala 1-10 
(1=sangat sulit/teknis, 10=sangat mudah):
- Saham: ___ (alasan: interface, riset yg diperlukan)
- Reksa Dana: ___ (alasan: setup platform, pilihan produk)
- SBN Ritel: ___ (alasan: birokrasi, proses registrasi)
- Kripto: ___ (alasan: teknis wallet, exchange)
- Emas Digital: ___ (alasan: app/platform, user-friendly)

[Catatan tentang mobile app, customer support, tutorial]
```

**Output yang diharapkan:**
Matriks performa 5x6 (alternatif × kriteria)

---

## BAGIAN 3: BENTUK DATA MENTAH DARI PAKAR

### 3.1 Output Pakar - Bagian A: Matriks Perbandingan Berpasangan

**Format Mentah (dari interview/form):**

```
Pakar (Investment Professional)
Nama: Dr. Ahmad Wijaya
Pengalaman: 10 tahun di industri investasi
Sertifikasi: CFP (Certified Financial Planner)

HASIL PENILAIAN:

K1 vs K2 (Return vs Risiko): Return 2x lebih penting
→ Alasan: Gen Z perlu return menarik, tapi harusnya ada kontrol risiko
→ Score: 2 (berarti K1 = 2 × K2)

K1 vs K3 (Return vs Likuiditas): Return sedikit lebih penting
→ Alasan: Return adalah prioritas utama
→ Score: 3 (berarti K1 = 3 × K3)

K1 vs K4 (Return vs Modal Awal): Return 3x lebih penting
→ Alasan: Gen Z lebih peduli return daripada modal awal
→ Score: 3

K1 vs K5 (Return vs Pendapatan Berkala): Return 2x lebih penting
→ Alasan: Capital appreciation lebih menarik daripada passive income untuk Gen Z
→ Score: 2

K1 vs K6 (Return vs Kemudahan Akses): Return 2x lebih penting
→ Alasan: Kualitas investasi lebih penting daripada kemudahan akses
→ Score: 2

K2 vs K3 (Risiko vs Likuiditas): Risiko 2x lebih penting
→ Alasan: Kontrol risiko lebih penting daripada likuiditas
→ Score: 2

K2 vs K4 (Risiko vs Modal Awal): Risiko 3x lebih penting
→ Alasan: Risiko adalah dampak finansial langsung
→ Score: 3

K2 vs K5 (Risiko vs Pendapatan Berkala): Risiko 2x lebih penting
→ Alasan: Kontrol risiko adalah prioritas dalam manajemen portofolio
→ Score: 2

K2 vs K6 (Risiko vs Kemudahan Akses): Risiko 3x lebih penting
→ Alasan: Risiko adalah faktor finansial, akses adalah teknis
→ Score: 3

K3 vs K4 (Likuiditas vs Modal Awal): Likuiditas 2x lebih penting
→ Alasan: Likuiditas membantu manajemen kas jangka pendek
→ Score: 2

K3 vs K5 (Likuiditas vs Pendapatan Berkala): Likuiditas sama penting
→ Alasan: Keduanya penting untuk strategi berbeda
→ Score: 1

K3 vs K6 (Likuiditas vs Kemudahan Akses): Likuiditas 2x lebih penting
→ Alasan: Likuiditas lebih penting untuk fleksibilitas keuangan
→ Score: 2

K4 vs K5 (Modal Awal vs Pendapatan Berkala): Modal Awal sama penting
→ Alasan: Keduanya adalah barrier dan benefit terpisah
→ Score: 1

K4 vs K6 (Modal Awal vs Kemudahan Akses): Modal Awal 2x lebih penting
→ Alasan: Kendala modal lebih nyata daripada kendala teknis untuk Gen Z
→ Score: 2

K5 vs K6 (Pendapatan Berkala vs Kemudahan Akses): Pendapatan Berkala 2x lebih penting
→ Alasan: Income stream lebih penting daripada kemudahan akses
→ Score: 2
```

**Diterjemahkan ke Matriks 6x6:**

```
        K1   K2   K3   K4   K5   K6
K1      1    2    3    3    2    2
K2     1/2   1   2    3    2    3
K3     1/3  1/2  1    2    1    2
K4     1/3  1/3  1/2  1   1    2
K5     1/2  1/2  1   1    1    2
K6     1/2  1/3  1/2  1/2  1/2  1
```

---

### 3.2 Output Pakar - Bagian B: Data Performa Alternatif

**Format Mentah (dari research/expert judgment):**

```
Pakar: Expert untuk semua instrumen

SPESIFIKASI 2024-2025:

K1 - POTENSI KEUNTUNGAN (Return Tahunan %):
-Saham: 12-15% (berdasarkan IHSG historis 5 tahun)
- Reksa Dana: 10-12% (rata-rata balanced fund)
- SBN Ritel: 6-7% (kupon pemerintah RI)
- Kripto: 40-50% (volatil, tapi potential tinggi)
- Emas Digital: 3-5% (mostly dari capital gain, nominal rendah)

→ Nilai gunakan: [12, 10, 6, 45, 4]

K2 - RISIKO (Skala 1-100):
- Saham: 50 (moderate-high, bergantung saham pilihan)
- Reksa Dana: 35 (moderate, lebih stabil dari saham murni)
- SBN Ritel: 5 (sangat rendah, backed by negara)
- Kripto: 85 (sangat tinggi, highly volatile)
- Emas Digital: 15 (rendah, relatively stable)

→ Nilai: [50, 35, 5, 85, 15]

K3 - LIKUIDITAS (Skala 1-10):
- Saham: 9 (bisa dijual dalam hitungan menit di jam trading)
- Reksa Dana: 7 (perlu 1-3 hari kerja)
- SBN Ritel: 6 (perlu 2-5 hari kerja)
- Kripto: 10 (instant, trading 24/7)
- Emas Digital: 8 (bisa dijual dalam hitungan jam)

→ Nilai: [9, 7, 6, 10, 8]

K4 - MODAL AWAL (dalam Rupiah):
- Saham: 100,000 (sekuritas minimal, tapi real minimum lebih tinggi)
- Reksa Dana: 50,000 (minimal investasi di platform)
- SBN Ritel: 1,000,000 (minimal pembelian per lot)
- Kripto: 10,000 (bisa beli 0.0001 BTC)
- Emas Digital: 100,000 (minimal untuk platform seperti Pegadaian)

→ Nilai: [100000, 50000, 1000000, 10000, 100000]

K5 - PENDAPATAN BERKALA (%):
- Saham: 2-3% (dividen yield rata-rata)
- Reksa Dana: 2% (dividen distribusi)
- SBN Ritel: 6-7% (kupon tetap, disbursement rutin)
- Kripto: 0% (tidak ada dividen/yield untuk BTC/ETH umumnya)
- Emas Digital: 0% (tidak ada passive income)

→ Nilai: [2.5, 2, 6.5, 0, 0]

K6 - KEMUDAHAN AKSES (Skala 1-10):
- Saham: 7 (aplikasi mudah, tapi perlu riset)
- Reksa Dana: 8 (setup mudah, bisa passive investing)
- SBN Ritel: 5 (proses registrasi panjang, tapi straightforward)
- Kripto: 6 (app ada, tapi perlu understanding teknis)
- Emas Digital: 8 (aplikasi very user-friendly)

→ Nilai: [7, 8, 5, 6, 8]
```

**Matriks Performa 5×6 (Alternatif × Kriteria):**

```
                K1(Return) K2(Risiko) K3(Likuid) K4(Modal) K5(Pendapatan) K6(Akses)
Saham          12         50         9          100000    2.5            7
Reksa Dana     10         35         7          50000     2              8
SBN Ritel      6          5          6          1000000   6.5            5
Kripto         45         85         10         10000     0              6
Emas Digital   4          15         8          100000    0              8
```

---

## BAGIAN 4: TRANSFORMASI DATA MENTAH KE FORMAT SISTEM

### 4.1 Dari Data Pakar → Input AHP

**Langkah 1: Kumpulkan perbandingan dari Pakar**

Karena hanya 1 pakar, gunakan data perbandingan pakar langsung tanpa averaging.

**Fitur tambahan:** Jika di masa depan akan menambah pakar lain, gunakan **Geometric Mean**

```
Geometric Mean Formula (jika multi-pakar di masa depan):
Aij(consensus) = (Aij_pakar1 × Aij_pakar2 × Aij_pakar3)^(1/3)

Contoh:
K1 vs K2 dari 3 pakar: [2, 2.5, 1.5]
Geometric Mean = (2 × 2.5 × 1.5)^(1/3) = (7.5)^(1/3) ≈ 1.96 ≈ 2
```

**Langkah 2: Pastikan Matriks Simetris**

```
Jika Aij = 2, maka Aji = 1/2
Jika Aij = 3, maka Aji = 1/3
```

**Result:** Matriks Perbandingan 6×6 siap untuk AHP

---

### 4.2 Dari Data Pakar → Input TOPSIS

**Langkah 1: Kumpulkan nilai performa dari Pakar**

Karena hanya 1 pakar, gunakan data performa pakar langsung.

**Langkah 2: Standardisasi Skala**

Beberapa kriteria punya unit berbeda:
- K1 (Return): % → Gunakan nilai actual
- K2 (Risiko): 1-100 → Gunakan nilai actual
- K3 (Likuiditas): 1-10 → Gunakan nilai actual
- K4 (Modal): Rupiah → Gunakan nilai actual
- K5 (Pendapatan): % → Gunakan nilai actual
- K6 (Akses): 1-10 → Gunakan nilai actual

**PENTING:** Jangan normalisasi di sini! Normalisasi dilakukan oleh formula TOPSIS.

**Langkah 3: Identifikasi Tipe Kriteria (Benefit vs Cost)**

```
K1 - Potensi Keuntungan: BENEFIT (semakin tinggi semakin baik)
K2 - Risiko: COST (semakin rendah semakin baik)
K3 - Likuiditas: BENEFIT (semakin tinggi semakin baik)
K4 - Modal Awal: COST (semakin rendah semakin baik)
K5 - Pendapatan Berkala: BENEFIT (semakin tinggi semakin baik)
K6 - Kemudahan Akses: BENEFIT (semakin tinggi semakin baik)

Tipe: [benefit, cost, benefit, cost, benefit, benefit]
```

**Result:** Matriks Performa 5×6 + Vektor Tipe Kriteria siap untuk TOPSIS

---

### 4.3 Bobot Kriteria dari AHP

**Output AHP:** Bobot prioritas dari 6 kriteria
```
Contoh (akan dihitung nanti):
w = [0.30, 0.20, 0.15, 0.10, 0.15, 0.10]

Total: 0.30 + 0.20 + 0.15 + 0.10 + 0.15 + 0.10 = 1.00 ✓
```

**Input untuk TOPSIS:** Vektor bobot w

---

## BAGIAN 5: DOKUMENTASI PENGAMBILAN DATA

### Template Form untuk Pakar

**Form Terpadu untuk Pakar (AHP + Performa):**
```
═════════════════════════════════════════════════════════════════
   FORM PENILAIAN PAKAR - SPK INVESTASI GEN Z (AHP + TOPSIS)
═════════════════════════════════════════════════════════════════

Nama Pakar: ___________________
Bidang Expertise: ___________________
Pengalaman (tahun): ___________________
Sertifikasi/Kredibilitas: ___________________
Tanggal: ___________________

═════════════════════════════════════════════════════════════════
BAGIAN A: PENILAIAN PERBANDINGAN BERPASANGAN (AHP)
═════════════════════════════════════════════════════════════════

INSTRUKSI:
Bandingkan setiap pasang kriteria. Gunakan skala Saaty:

1 = Sama penting
3 = Sedikit lebih penting
5 = Lebih penting
7 = Sangat lebih penting
9 = Ekstrem lebih penting
2, 4, 6, 8 = Nilai intermediate

Jika kriteria A lebih penting dari B:
  → Tulis nilai positif (misal: 2, 3, 5, dst)

Jika kriteria B lebih penting dari A:
  → Tulis pecahan (misal: 1/2, 1/3, 1/5, dst)

[15 pertanyaan pairwise comparison]
[Result: Matriks 6×6] ✓

═════════════════════════════════════════════════════════════════
BAGIAN B: DATA PERFORMA ALTERNATIF INVESTASI (TOPSIS)
═════════════════════════════════════════════════════════════════

INSTRUKSI:
Berdasarkan pengalaman & data terkini (2024-2025),
berikan rating untuk setiap alternatif pada setiap kriteria.

[6 set kriteria dengan instruksi penilaian yang detail]
[Result: Matriks 5×6] ✓
```

---

## KESIMPULAN BAGIAN 1

**Framework yang telah ditetapkan:**

| Aspek | Detail |
|-------|--------|
| **Jumlah Pakar** | 1 pakar (Investment Professional) |
| **Output Pakar - Bagian A** | Matriks Perbandingan AHP (6×6) = 15 penilaian berpasangan |
| **Output Pakar - Bagian B** | Data Performa Alternatif (5×6) = 30 nilai |
| **Total Data Pakar** | 45 data poin (15 + 30) |
| **Representasi Gen Z** | Built-in dalam pilihan alternatif & kriteria evaluasi |
| **Transformasi** | Data mentah → Input AHP → Input TOPSIS |
| **Keuntungan 1 Pakar** | Simplifikasi, fokus pada expert terpercaya, timeline lebih pendek |
| **Next Step** | Perhitungan Manual AHP & TOPSIS |

---

**File Berikutnya:**
`02_CONTOH_DATA_NYATA_DARI_PAKAR.md` - Contoh konkret dengan angka sebenarnya
