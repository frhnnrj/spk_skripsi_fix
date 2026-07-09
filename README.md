# QUICK START GUIDE - SPK INVESTASI GEN Z
## Panduan Cepat untuk Mulai Implementasi

---

## 📋 OVERVIEW DOKUMENTASI

Anda menerima **3 dokumen lengkap** yang saling terintegrasi:

| Dokumen | Isi | Untuk Siapa | Waktu Baca |
|---------|-----|-------------|-----------|
| **01_FRAMEWORK** | Siapa pakar, pertanyaan apa, data apa | Designer/PM | 30 menit |
| **02_PERHITUNGAN** | Cara hitung manual, kode Python, verifikasi | Developer/Analyst | 60 menit |
| **03_IMPLEMENTASI** | Roadmap 5 fase, template, checklist | Project Manager/You | 45 menit |

**Total dokumentasi: ~25,000 kata | ~80 halaman | Production-ready**

---

## 🚀 LANGKAH-LANGKAH PRAKTIS

### LANGKAH 1: Pahami Teori (Minggu 1)
```
□ Baca BAB 2 skripsi Anda
□ Baca Dokumen 01 (Framework Pengambilan Data)
□ Pahami 3 tipe pakar yang dibutuhkan
□ Siapkan template interview
```

**Waktu: 2-3 jam**

### LANGKAH 2: Kumpulkan Data Pakar (Minggu 1-2)
```
□ Interview Pakar 1 (Financial Advisor) → Matriks 6×6
□ Interview Pakar 2 (SME) → Data performa 5×6
□ Feedback Pakar 3 (Gen Z) → Validasi kualitatif
□ Simpan ke Excel/JSON
```

**Waktu: 1-2 minggu**

### LANGKAH 3: Validasi dengan Kode Python (Minggu 2)
```
□ Siapkan Python 3.8+
□ Copy contoh kode dari Dokumen 02
□ Ubah data hardcoded dengan data dari pakar
□ Jalankan ahp_calculator.py & topsis_calculator.py
□ Bandingkan dengan perhitungan manual
```

**Waktu: 2-3 jam**

### LANGKAH 4: Dokumentasi BAB 4 (Minggu 3-4)
```
□ Copy template dari Dokumen 03
□ Jelaskan metodologi implementasi
□ Tampilkan hasil perhitungan (tabel & grafik)
□ Tulis verifikasi manual vs sistem
```

**Waktu: 1 minggu**

### LANGKAH 5: Persiapan Presentasi (Minggu 4-5)
```
□ Buat slide dari template Dokumen 03
□ Siapkan demo aplikasi web (atau screenshot)
□ Latihan presentasi (minimal 2 kali)
□ Persiapkan jawaban FAQ
```

**Waktu: 5-7 hari**

---

## 📊 FILE STRUKTUR YANG HARUS ADA

Setelah selesai, folder Anda akan terlihat seperti:

```
SKRIPSI_BRAYYY/
├── METOPEN_SAYA.pdf                    [BAB 1-3 sudah ada ✓]
├── 01_FRAMEWORK_PENGAMBILAN_DATA_PAKAR.md
├── 02_CONTOH_DATA_PERHITUNGAN_MANUAL_KODE.md
├── 03_PANDUAN_IMPLEMENTASI_PRESENTASI.md
│
├── DATA_INPUT/
│   ├── pakar_1_matriks_ahp.xlsx        [Data pakar 1]
│   ├── pakar_2_performa.xlsx           [Data pakar 2]
│   └── pakar_3_feedback.docx           [Feedback pakar 3]
│
├── PYTHON_SCRIPTS/
│   ├── ahp_calculator.py               [Bisa dijalankan ✓]
│   ├── topsis_calculator.py            [Bisa dijalankan ✓]
│   └── run_both.sh                     [Script untuk run semuanya]
│
├── HASIL_PERHITUNGAN/
│   ├── hasil_ahp.txt                   [Output AHP]
│   ├── hasil_topsis.txt                [Output TOPSIS]
│   └── verifikasi_manual_vs_kode.xlsx  [Perbandingan]
│
├── APPLICATION_WEB/
│   ├── index.php
│   ├── css/style.css
│   ├── js/script.js
│   └── database/schema.sql
│
├── BAB_4_SKRIPSI/
│   ├── 4.1_Metodologi_Implementasi.docx
│   ├── 4.2_Hasil_Implementasi.docx
│   └── 4.3_Verifikasi_Sistem.docx
│
├── PRESENTASI/
│   ├── Slide_SPK_Investasi.pptx
│   ├── Screenshot_Demo.png
│   └── Video_Demo.mp4
│
└── README.md                           [File ini]
```

---

## 🔧 QUICK START: JALANKAN KODE PYTHON

### Instalasi:

**1. Instal Python**
```bash
# Windows
# Download dari: https://www.python.org/downloads/
# Pilih versi 3.8 atau lebih baru
# Centang "Add to PATH" saat install

# Linux/Mac
sudo apt-get install python3 python3-pip
```

**2. Instal numpy**
```bash
pip install numpy
```

**3. Verifikasi instalasi**
```bash
python --version
pip list | grep numpy
```

### Menjalankan Kode:

**Opsi 1: Jalankan langsung (Linux/Mac/PowerShell)**
```bash
# Copy script dari Dokumen 02 ke file: ahp_calculator.py
# Jalankan:
python ahp_calculator.py

# Copy script dari Dokumen 02 ke file: topsis_calculator.py
# Jalankan:
python topsis_calculator.py
```

**Opsi 2: Jalankan dari Interactive Python**
```bash
# Masuk Python shell
python

# Salin kode dari ahp_calculator.py ke console
# Tekan Enter untuk eksekusi
```

**Expected Output dari ahp_calculator.py:**
```
============================================================
               HASIL PERHITUNGAN AHP
============================================================

Jumlah Kriteria: 6

Bobot Prioritas:
----------------------------------------
  K1: 0.3067  (30.67%)
  K2: 0.2377  (23.77%)
  K3: 0.1377  (13.77%)
  K4: 0.1055  (10.55%)
  K5: 0.1308  (13.08%)
  K6: 0.0817  (8.17%)

Pengujian Konsistensi:
  Lambda Max (λmax): 6.2187
  Consistency Index (CI): 0.0438
  Consistency Ratio (CR): 0.0353 (3.53%)
  Status: VALID ✓

  ✓ CR < 0.1, matriks KONSISTEN dan dapat digunakan
```

---

## ✅ VERIFICATION CHECKLIST

Setelah menjalankan kode, pastikan:

```
HASIL AHP:
□ λmax ≈ 6.22 (±0.05)
□ CI ≈ 0.044 (±0.005)
□ CR ≈ 0.035 (±0.005)
□ CR < 0.1 → Status VALID ✓

HASIL TOPSIS:
□ Ranking 1: Kripto (preferensi ≥ 0.50)
□ Ranking 2: Saham (preferensi ≈ 0.37)
□ Ranking 3: Reksa Dana (preferensi ≈ 0.33)
□ Ranking 4: Emas Digital (preferensi ≈ 0.28)
□ Ranking 5: SBN Ritel (preferensi ≥ 0.20)

VERIFIKASI LOGIKA:
□ Hasil masuk akal? (Kripto memang high return + high risk)
□ Bobot konsisten? (Return 30% > Risiko 23%)
□ Pakar agree? (Minimal 60% consensus dari Gen Z)
```

---

## 📖 MANA YANG DIBACA KAPAN?

**Jika Anda LIMITED TIME (3 hari):**
```
Hari 1:
- Baca: 03_PANDUAN_IMPLEMENTASI_PRESENTASI.md (bagian Roadmap)
- Action: Siapkan template interview

Hari 2:
- Baca: 02_CONTOH_DATA_PERHITUNGAN_MANUAL... (bagian Python)
- Action: Jalankan kode Python (verifikasi bahwa sistemnya kerja)

Hari 3:
- Baca: 01_FRAMEWORK_PENGAMBILAN_DATA_PAKAR.md
- Action: Mulai interview pakar (atau tunjukkan template ke dosen)
```

**Jika Anda NORMAL TIME (2 minggu):**
```
Minggu 1:
- Baca semua 3 dokumen (lengkap)
- Pahami teori & framework
- Siapkan template interview lengkap

Minggu 2:
- Interview pakar 1-3
- Jalankan kode Python dengan data real
- Mulai draft BAB 4
- Persiapan presentasi
```

**Jika Anda BANYAK TIME (1 bulan):**
```
Minggu 1-2:
- Deep dive ke 3 dokumen
- Belajar dari A-Z (teori → praktek)
- Interview semua pakar dengan detail

Minggu 3:
- Validasi kode Python dengan data real
- Implementasi aplikasi web (jika belum ada)
- Testing lengkap

Minggu 4:
- Dokumentasi BAB 4-5 lengkap
- Presentasi ke dosen
- Refinement berdasarkan feedback
```

---

## 🎯 KEY MESSAGES UNTUK DOSEN

**Ketika bertemu dosen, sampaikan:**

1. **Struktur Penelitian:**
   > "Saya mengikuti framework terstruktur: Pengambilan data pakar → Perhitungan manual → Kode Python validasi → Implementasi web → Presentasi hasil"

2. **Verifikasi Metodologi:**
   > "Setiap tahap perhitungan sudah saya validasi: Manual calculation vs Python code hasilnya identik, CR < 0.1 (valid), ranking masuk akal"

3. **Data Pakar:**
   > "Saya sudah interview [X] pakar dari [institusi], menggunakan template terstruktur dari Saaty's AHP methodology"

4. **Readiness untuk Presentasi:**
   > "Saya siap presentasi dengan demo langsung: Input data → Kode hitung → Output ranking lengkap dengan interpretasi"

---

## ❓ FAQ CEPAT

**Q: Saya tidak punya data pakar, bisa langsung presentasi?**
> A: Tidak ideal, tapi bisa. Gunakan data contoh dulu untuk demo sistem logic. Jelaskan ke dosen bahwa data pakar sedang dikumpulkan. Setelah dapat pakar, update sistem.

**Q: Hasil ranking saya berbeda dengan contoh dokumen (Kripto rank 1)?**
> A: Normal! Jika data pakar Anda berbeda, ranking bisa berbeda. Yang penting:
> 1. Metodologi AHP-TOPSIS benar
> 2. Perhitungan valid (CR < 0.1)
> 3. Hasil masuk akal untuk konteks Anda

**Q: Harus buat web application?**
> A: Tidak wajib untuk presentasi basic. Kode Python sudah cukup untuk:
> - Demonstrasikan metodologi
> - Tunjukkan hasil perhitungan
> - Verifikasi validasi sistem

> Tapi aplikasi web itu **NILAI PLUS** untuk BAB 4.

**Q: Berapa lama interview tiap pakar?**
> A: Pakar 1 (AHP): 60-90 menit
> Pakar 2 (Performa): 30-45 menit per expertise
> Pakar 3 (Feedback): 20-30 menit per person

**Q: Data pakar harus dari mana?**
> A: 
> - Pakar 1: Financial advisor bersertifikat (CFP) atau akademisi finance
> - Pakar 2: Analyst pasar modal, fund manager, broker officer
> - Pakar 3: Gen Z yang aktif/tertarik investasi

> Bisa dari: Instansi finansial, universitas, FinTech startup

---

## 📌 POIN-POIN PENTING

✓ **AHP untuk mendapatkan BOBOT kriteria** (seberapa penting setiap kriteria)

✓ **TOPSIS untuk mendapatkan RANKING alternatif** (instrumen mana yang terbaik)

✓ **Yang membuat valid:** Pakar berkualitas + Perhitungan akurat + Verifikasi menyeluruh

✓ **Yang buat profesional:** Dokumentasi lengkap + Code terverifikasi + Presentasi confident

✓ **Tujuan skripsi:** Membantu Gen Z membuat keputusan investasi INFORMED & RATIONAL (bukan emosional)

---

## 🔗 ALUR BACA DOKUMEN YANG DIREKOMENDASIKAN

```
UNTUK PERTAMA KALI:
1. Baca file INI (README.md) dulu → 15 menit
↓
2. Baca Dokumen 01 (Framework) → 30 menit
   (Pahami: siapa pakar, apa pertanyaannya)
↓
3. Baca Dokumen 02 (Perhitungan Manual) → 60 menit
   (Pahami: bagaimana rumus AHP & TOPSIS bekerja)
↓
4. Baca Dokumen 03 (Implementasi) → 45 menit
   (Pahami: roadmap 5 fase & checklist presentasi)

TOTAL: ~150 menit (2.5 jam) untuk pemahaman lengkap

SETELAH ITU:
- Mulai interview pakar
- Jalankan kode Python
- Tulis BAB 4
- Persiapan presentasi
```

---

## 📞 JIKA PERLU BANTUAN

**Problem dengan AHP perhitungan?**
- Baca ulang Dokumen 02 bagian "PERHITUNGAN MANUAL AHP"
- Cek: Matriks simetris? Normalisasi benar? λmax formula?

**Problem dengan TOPSIS perhitungan?**
- Baca ulang Dokumen 02 bagian "PERHITUNGAN MANUAL TOPSIS"
- Cek: Benefit/Cost flags benar? Jarak euclidean formula?

**Problem dengan kode Python?**
- Pastikan numpy installed: `pip install numpy`
- Copy kode dari Dokumen 02 dengan teliti (spacing penting!)
- Run: `python ahp_calculator.py`

**Problem dengan data pakar?**
- Gunakan template di Dokumen 03
- Jika pakar sibuk, gunakan data example dari Dokumen 02 dulu

**Problem dengan BAB 4?**
- Template sudah ada di Dokumen 03
- Copy-paste & customize sesuai hasil Anda

---

## 🎓 FINAL CHECKLIST BEFORE PRESENTATION

```
SEBULAN SEBELUM:
□ Kumpulkan semua data pakar
□ Jalankan kode Python & validasi hasil
□ Draft BAB 4-5
□ Prepare slides

DUA MINGGU SEBELUM:
□ Finalisasi BAB 4-5
□ Complete slides (20 slides)
□ Siapkan demo/screenshot
□ Latihan presentasi 1 kali

SEMINGGU SEBELUM:
□ Updated slides versi final
□ Backup semua file (USB + Cloud)
□ Latihan presentasi 2 kali
□ Siapkan jawaban FAQ

SEHARI SEBELUM:
□ Cek laptop & tools
□ Refresh memory presentasi
□ Sleep well!

SAAT PRESENTASI:
□ Professional attire
□ Confident & clear voice
□ Demo smooth & prepared
□ Ready untuk Q&A
```

---

## 🌟 SUCCESS CRITERIA

Presentasi Anda akan **LOLOS & BAGUS** jika:

✓ Metodologi AHP-TOPSIS dijelaskan dengan benar (BAB 2 understanding)

✓ Data pakar valid & terdokumentasi dengan baik

✓ Perhitungan terintegrasi: AHP → TOPSIS flow jelas

✓ Verifikasi ada: Manual calculation vs System hasil identik

✓ Hasil interpretasi dengan logic: Ranking masuk akal

✓ Dokumentasi lengkap: BAB 4 & 5 terstruktur rapi

✓ Presentasi confident: Tahu apa yang disampaikan & siap Q&A

✓ Demo working: Minimal screenshot/video (jika tidak live)

---

## 📚 REFERENSI CEPAT

| Topik | Baca Di | Halaman |
|-------|---------|---------|
| Framework Pakar | Dokumen 01 | Bagian 1-2 |
| Pertanyaan Pakar | Dokumen 01 | Bagian 2 |
| Data Contoh | Dokumen 02 | Bagian I |
| Manual AHP | Dokumen 02 | Bagian II |
| Manual TOPSIS | Dokumen 02 | Bagian III |
| Kode Python | Dokumen 02 | Bagian IV |
| Roadmap Implementation | Dokumen 03 | Bagian 1 |
| Template Interview | Dokumen 03 | Bagian 2 |
| Checklist Presentasi | Dokumen 03 | Bagian 3 |
| FAQ Untuk Dosen | Dokumen 03 | Bagian 4 |

---

## 🎯 LANGKAH TERAKHIR: SETELAH PRESENTASI

**Jika LOLOS:**
- Lakukan revisi sedang dari feedback dosen
- Finalisasi skripsi
- Submit untuk ujian sidang
- Celebrate! 🎉

**Jika Ada Revisi:**
- Catat semua pertanyaan/masukan
- Update BAB sesuai feedback (3-5 hari)
- Submit revised version
- Siap untuk sidang ulang (jika perlu)

**Next Steps Penelitian:**
- Lihat Dokumen 03 Bagian 4 (Roadmap Lanjutan)
- Implementasi real-time data integration
- Develop mobile app
- Publish makalah ke jurnal

---

## 📄 DOCUMENT INFO

| Aspek | Detail |
|-------|--------|
| **Total Dokumentasi** | ~25,000 kata |
| **File Count** | 3 dokumen markdown |
| **Code Examples** | 2 script Python lengkap |
| **Template** | 3 form interview ready-to-use |
| **Status** | Production ready |
| **Version** | 1.0 - Final |
| **Last Updated** | 27 April 2026 |

---

## ✨ YANG PALING PENTING

> **Sistem ini BUKAN tentang membuat Gen Z kaya.** 
> 
> Ini tentang memberikan FRAMEWORK untuk membuat keputusan **INFORMED, RATIONAL, dan TERSTRUKTUR**.
> 
> Jika aplikasi ini membantu satu Gen Z menghindari investasi berisiko tinggi atau justru berani masuk market karena tahu apa yang dilakukan, MAKA PENELITIAN INI BERHASIL.

---

**SELAMAT MEMULAI! Anda sudah punya semuanya untuk sukses presentasi! 💪**

**Good luck! 🚀**

---

END OF QUICK START GUIDE
