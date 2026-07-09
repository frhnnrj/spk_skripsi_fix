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
                <div class="hero-panel">
                    <div class="hero-card">
                        <h3>SPK Investasi Gen Z Berbasis AHP-TOPSIS</h3>
                        <p>
                            Platform ini membantu Anda memilih instrumen investasi paling sesuai berdasarkan
                            pembobotan <strong>AHP</strong> dan pemeringkatan <strong>TOPSIS</strong>.
                        </p>
                    </div>
                    <div class="stat-card">
                        <p class="stat-label">Status Sistem</p>
                        <p class="stat-value"><?php echo $total_pakar; ?> Pakar Aktif</p>
                        <p class="text-muted mt-1">
                            <?php echo $total_pakar > 0 ? 'Sistem siap digunakan oleh user.' : 'Menunggu input data pakar oleh admin.'; ?>
                        </p>
                        <span class="badge <?php echo $total_pakar > 0 ? 'badge-success' : 'badge-warning'; ?>">
                            <?php echo $total_pakar > 0 ? 'Siap Operasional' : 'Belum Lengkap'; ?>
                        </span>
                    </div>
                </div>

                <div class="grid">
                    <div class="card">
                        <h3>👤 Panel User</h3>
                        <p>Lakukan penilaian profil investasi dan dapatkan rekomendasi yang paling sesuai untuk Anda.</p>
                        <p class="mt-2">
                            <span class="badge <?php echo $total_pakar > 0 ? 'badge-success' : 'badge-warning'; ?>">
                                <?php echo $total_pakar > 0 ? 'Dapat diakses' : 'Menunggu data pakar'; ?>
                            </span>
                        </p>
                        <a href="user/" class="btn btn-primary mt-2 <?php echo $total_pakar === 0 ? 'muted-card' : ''; ?>" <?php echo $total_pakar === 0 ? 'onclick="return false;"' : ''; ?>>
                            Get Started →
                        </a>
                    </div>
                </div>

                <div class="box">
                    <h3>📚 Alur Penggunaan</h3>
                    <ol>
                        <li><strong>Admin Input Data:</strong> Admin memasukkan penilaian pakar (pairwise matrix & decision matrix)</li>
                        <li><strong>Sistem Hitung:</strong> Sistem secara otomatis menghitung bobot AHP dan ranking TOPSIS</li>
                        <li><strong>User Assess:</strong> User melakukan penilaian risiko/return mereka sendiri</li>
                        <li><strong>Hasil & Rekomendasi:</strong> User menerima ranking dan rekomendasi investasi</li>
                        <li><strong>Perbandingan:</strong> User dapat membandingkan penilaian mereka dengan pakar</li>
                    </ol>
                </div>

                <div class="box">
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
