# 🎯 SPK AHP-TOPSIS Investasi Gen Z Indonesia

## Panduan Setup & Instalasi

### 📋 Prasyarat
- XAMPP dengan Apache + MySQL
- PHP 7.4 atau lebih tinggi
- Browser modern (Chrome, Firefox, Safari, Edge)

---

## 🚀 Langkah Instalasi

### **Langkah 1: Persiapan Folder**
✅ Folder sudah tersedia di: `C:\xampp\htdocs\SKRIPSI BRAYYY\05_WEBSITE_SPK\`

### **Langkah 2: Jalankan XAMPP**
1. Buka Control Panel XAMPP
2. Klik tombol **"Start"** untuk Apache
3. Klik tombol **"Start"** untuk MySQL

### **Langkah 3: Inisialisasi Database**
1. Buka browser → ketik: `http://localhost/SKRIPSI%20BRAYYY/05_WEBSITE_SPK/setup.php`
2. Tunggu sampai muncul pesan: **"✅ Database setup complete!"**
3. Database akan berisi:
   - 9 tabel kosong (siap diisi admin dan user)
   - 5 alternatif investasi (Kripto, Saham, SBN Ritel, Reksa Dana, Emas Digital)
   - 6 kriteria evaluasi (Return, Risk, Liquidity, Capital, Income, Access)

### **Langkah 4: Akses Sistem**

#### **Landing Page**
- URL: `http://localhost/SKRIPSI%20BRAYYY/05_WEBSITE_SPK/`
- Tampilan: Daftar menu Admin & User Panel

#### **Panel Admin**
- URL: `http://localhost/SKRIPSI%20BRAYYY/05_WEBSITE_SPK/admin/`
- Fungsi: Input data pakar, pairwise matrix, decision matrix, & trigger kalkulasi

#### **Panel User**
- URL: `http://localhost/SKRIPSI%20BRAYYY/05_WEBSITE_SPK/user/`
- Fungsi: Belajar tentang investasi & melakukan penilaian preferensi

---

## 📁 Struktur Folder

```
05_WEBSITE_SPK/
├── index.php                           # Landing page
├── setup.php                           # Inisialisasi database (jalankan 1x)
├── config/
│   └── database.php                    # Koneksi & helper functions
├── assets/
│   └── css/
│       └── style.css                   # CSS global (2000+ lines)
├── admin/
│   ├── index.php                       # Dashboard admin
│   ├── README.md                       # Dokumentasi admin
│   └── pages/                          # Form pages (belum dibuat)
│       ├── pakar_form.php              # Form tambah/edit pakar
│       ├── pairwise_form.php           # Form pairwise matrix (15 comparisons)
│       ├── decision_form.php           # Form decision matrix (30 values)
│       └── results.php                 # Tampil hasil AHP-TOPSIS
├── user/
│   ├── index.php                       # Beranda user panel
│   ├── README.md                       # Dokumentasi user
│   └── pages/                          # Content pages (belum dibuat)
│       ├── education.php               # Materi pembelajaran
│       ├── assessment.php              # Kuesioner penilaian user
│       └── results.php                 # Hasil & rekomendasi
├── sql/
│   └── schema.sql                      # Database schema (9 tabel)
└── README.md                           # File ini
```

---

## 🗄️ Database Schema (9 Tabel)

### **1. tbl_pakar** - Data Pakar (Expert)
```
- pakar_id (Primary Key)
- pakar_nama (Nama pakar/expert)
- pakar_deskripsi (Deskripsi)
- created_at, updated_at
```
**Catatan:** Sistem hanya mendukung 1 pakar (unique constraint)

### **2. tbl_pairwise_matrix** - Perbandingan Berpasangan AHP
```
- pairwise_id (Primary Key)
- pakar_id (Foreign Key)
- k1_vs_k2, k1_vs_k3, ..., k5_vs_k6 (15 perbandingan)
- status ('draft' atau 'complete')
```
**15 Perbandingan:**
- K1 vs K2, K1 vs K3, K1 vs K4, K1 vs K5, K1 vs K6
- K2 vs K3, K2 vs K4, K2 vs K5, K2 vs K6
- K3 vs K4, K3 vs K5, K3 vs K6
- K4 vs K5, K4 vs K6
- K5 vs K6

### **3. tbl_decision_matrix** - Matriks Keputusan
```
- decision_id (Primary Key)
- pakar_id, alternatif_id (Foreign Keys)
- k1_return, k2_risk, k3_liquidity, k4_capital, k5_income, k6_access
- status ('draft' atau 'complete')
```
**30 Nilai:** 5 alternatif × 6 kriteria

### **4. tbl_ahp_results** - Hasil Perhitungan AHP
```
- ahp_id (Primary Key)
- pakar_id (Foreign Key)
- w_k1, w_k2, w_k3, w_k4, w_k5, w_k6 (Bobot dari AHP)
- lambda_max, ci (Consistency Index), cr (Consistency Ratio)
- is_consistent (Apakah CR < 10%?)
```

### **5. tbl_topsis_results** - Hasil Ranking TOPSIS
```
- topsis_id (Primary Key)
- pakar_id (Foreign Key)
- kripto_preference, kripto_rank
- saham_preference, saham_rank
- ... (5 alternatif × 2 kolom)
```

### **6. tbl_user_assessment** - Penilaian User
```
- assessment_id (Primary Key)
- pakar_id (Foreign Key)
- session_id, user_nama, user_usia
- user_tujuan_investasi
- risk_tolerance, return_expectation, liquidity_need, capital_available
- user_top1_alternatif, user_top2_alternatif, user_top3_alternatif
- match_percentage (Kesamaan dengan pakar)
```

### **7. tbl_alternatives** - Alternatif Investasi (Static)
```
- alternatif_id (1-5)
- alternatif_nama (Kripto, Saham, SBN Ritel, Reksa Dana, Emas Digital)
- alternatif_deskripsi, alternatif_icon
```

### **8. tbl_criteria** - Kriteria Evaluasi (Static)
```
- kriteria_id (1-6)
- kriteria_kode (K1-K6)
- kriteria_nama (Return, Risk, Liquidity, Capital, Income, Access)
- kriteria_tipe ('benefit' atau 'cost')
- satuan (%, Rp, Skala 1-10)
```

### **9. tbl_audit_log** - Log Audit
```
- log_id (Primary Key)
- pakar_id, action, details
- ip_address, created_at
```

---

## 🔄 Workflow Sistem

### **Admin Workflow:**
1. **Buka Panel Admin** → http://localhost/.../05_WEBSITE_SPK/admin/
2. **Tambah Pakar** → Isi nama & deskripsi pakar
3. **Input Pairwise Matrix** → 15 perbandingan (Saaty scale 1-9)
4. **Input Decision Matrix** → 30 nilai (5 alternatif × 6 kriteria)
5. **Klik Hitung** → Sistem otomatis:
   - Hitung AHP (bobot w_k1 sampai w_k6)
   - Hitung TOPSIS (ranking 1-5 untuk setiap alternatif)
   - Simpan hasil ke database
6. **Lihat Hasil** → Display di results.php

### **User Workflow:**
1. **Buka Panel User** → http://localhost/.../05_WEBSITE_SPK/user/
2. **Belajar** → Pelajari 5 instrumen investasi
3. **Penilaian** → Jawab kuesioner (risk profile, return expectation, dll)
4. **Hasil** → Lihat ranking dari sistem
5. **Perbandingan** → Compare ranking user vs pakar

---

## 🛠️ Teknologi yang Digunakan

| Komponen | Teknologi |
|----------|-----------|
| Server | Apache (XAMPP) |
| Backend | PHP 7.4+ |
| Database | MySQL |
| Frontend | HTML5, CSS3, JavaScript |
| Metodologi | AHP + TOPSIS |

---

## 📊 Metodologi

### **AHP (Analytic Hierarchy Process)**
- Menggunakan Saaty's 9-point scale (1-9)
- Menghasilkan bobot (weight) untuk setiap kriteria
- Validasi Consistency Ratio (CR) harus < 10%

### **TOPSIS (Technique for Order Preference by Similarity to Ideal Solution)**
- Menghitung ideal positive & negative solution
- Menghasilkan preference score untuk setiap alternatif
- Ranking 1-5 berdasarkan preference score

---

## ⚠️ Catatan Penting

1. **Database Kosong:** Database akan dimulai kosong. Admin harus input data terlebih dahulu.
2. **1 Pakar Saja:** Sistem dirancang untuk 1 pakar (tidak multiple profiles).
3. **Data Pakar Edit:** Admin dapat mengubah & menghitung ulang kapan saja.
4. **Konsistensi:** Jika CR > 10%, sistem akan meminta admin untuk merevisi pairwise matrix.
5. **User Session:** Setiap user mendapat session_id unik untuk tracking penilaian.

---

## 🔐 Keamanan

- SQL Injection Prevention: `mysqli_prepare()` dengan parameter binding
- XSS Prevention: `htmlspecialchars()` pada semua output
- Session Management: PHP session untuk user tracking
- Audit Log: Semua aksi admin tercatat

---

## 📞 Support

Jika mengalami masalah:

1. **Database tidak terbuat?**
   - Pastikan MySQL sudah berjalan
   - Cek user root tidak punya password (default XAMPP)

2. **Halaman tidak terbuka?**
   - Pastikan Apache berjalan
   - Check folder path sudah benar di `C:\xampp\htdocs\`

3. **Error SQL?**
   - Setup.php sudah dijalankan?
   - Check file `config/database.php` sudah ada

---

## 📝 Next Steps

Setelah setup selesai, buat file-file berikut:

### **Admin Panel Pages (Tahap 2):**
- [ ] `admin/pages/pakar_form.php` - Form tambah/edit pakar
- [ ] `admin/pages/pairwise_form.php` - Form 15 perbandingan
- [ ] `admin/pages/decision_form.php` - Form 30 nilai
- [ ] `admin/pages/results.php` - Display hasil AHP-TOPSIS
- [ ] `admin/process/calculate.php` - Trigger kalkulasi

### **User Panel Pages (Tahap 3):**
- [ ] `user/pages/education.php` - Materi pembelajaran
- [ ] `user/pages/assessment.php` - Kuesioner user
- [ ] `user/pages/results.php` - Hasil & rekomendasi

### **JavaScript & Validation (Tahap 4):**
- [ ] `assets/js/form-validation.js` - Client-side validation
- [ ] `assets/js/charts.js` - Visualisasi hasil

---

**Dibuat dengan ❤️ untuk Gen Z Investment Decision Support System**
