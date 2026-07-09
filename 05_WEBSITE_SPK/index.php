<?php
require_once 'config/database.php';

$pakar = get_all_pakar();
$total_pakar = count($pakar);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK AHP-TOPSIS - Investasi Gen Z Indonesia</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="main-container">
        <header>
            <div class="container">
                <h1>🎯 SPK AHP-TOPSIS Investasi</h1>
                <p>Sistem Pendukung Keputusan untuk Generasi Z Indonesia</p>
            </div>
        </header>

        <main>
            <div class="container">
                <h2>Selamat Datang!</h2>
                
                <div class="box">
                    <h3>📌 Tentang Sistem</h3>
                    <p>
                        Sistem ini dirancang untuk membantu Anda membuat keputusan investasi yang lebih baik 
                        dengan menganalisis berbagai instrumen investasi menggunakan metodologi <strong>AHP (Analytic Hierarchy Process)</strong> 
                        dan <strong>TOPSIS (Technique for Order Preference by Similarity to Ideal Solution)</strong>.
                    </p>
                </div>

                <div class="grid">
                    <div class="card">
                        <h3>🔐 Panel Admin</h3>
                        <p>Kelola data pakar dan lakukan perhitungan AHP-TOPSIS</p>
                        <p style="margin-top: 1rem; font-size: 0.9rem; color: var(--gray);">
                            <strong>Status:</strong> 
                            <span class="badge <?php echo $total_pakar > 0 ? 'badge-success' : 'badge-danger'; ?>">
                                <?php echo $total_pakar > 0 ? '✅ Pakar tersedia' : '❌ Belum ada pakar'; ?>
                            </span>
                        </p>
                        <a href="admin/" class="btn btn-primary" style="margin-top: 1rem;">Akses Admin →</a>
                    </div>

                    <div class="card">
                        <h3>👤 Panel User</h3>
                        <p>Lakukan penilaian investasi dan dapatkan rekomendasi</p>
                        <p style="margin-top: 1rem; font-size: 0.9rem; color: var(--gray);">
                            <strong>Syarat:</strong>
                            <span class="badge <?php echo $total_pakar > 0 ? 'badge-success' : 'badge-warning'; ?>">
                                <?php echo $total_pakar > 0 ? '✅ Dapat diakses' : '⏳ Menunggu data pakar'; ?>
                            </span>
                        </p>
                        <a href="user/" class="btn btn-primary" style="margin-top: 1rem; <?php echo $total_pakar === 0 ? 'opacity: 0.5; cursor: not-allowed;' : ''; ?>" <?php echo $total_pakar === 0 ? 'onclick="return false;"' : ''; ?>>
                            Akses User →
                        </a>
                    </div>
                </div>

                <div class="box">
                    <h3>📚 Bagaimana Cara Kerja?</h3>
                    <ol>
                        <li><strong>Admin Input Data:</strong> Admin memasukkan penilaian pakar (pairwise matrix & decision matrix)</li>
                        <li><strong>Sistem Hitung:</strong> Sistem secara otomatis menghitung bobot AHP dan ranking TOPSIS</li>
                        <li><strong>User Assess:</strong> User melakukan penilaian risiko/return mereka sendiri</li>
                        <li><strong>Hasil & Rekomendasi:</strong> User menerima ranking dan rekomendasi investasi</li>
                        <li><strong>Perbandingan:</strong> User dapat membandingkan penilaian mereka dengan pakar</li>
                    </ol>
                </div>

                <div class="box" style="border-left-color: var(--info);">
                    <h3>ℹ️ Informasi Teknis</h3>
                    <p>
                        <strong>5 Alternatif Investasi:</strong> Kripto, Saham, SBN Ritel, Reksa Dana, Emas Digital<br>
                        <strong>6 Kriteria Evaluasi:</strong> Return, Risk, Liquidity, Capital, Income, Access<br>
                        <strong>Metode:</strong> AHP untuk pembobotan + TOPSIS untuk ranking<br>
                        <strong>Pakar Aktif:</strong> <span class="badge badge-info"><?php echo $total_pakar; ?></span>
                    </p>
                </div>
            </div>
        </main>

        <footer>
            <div class="container">
                <p>&copy; 2026 SPK AHP-TOPSIS | Skripsi Gen Z Investment Decision Support System</p>
            </div>
        </footer>
    </div>
</body>
</html>
