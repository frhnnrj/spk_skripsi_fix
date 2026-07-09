<?php
require_once '../config/database.php';

$pakar = get_all_pakar();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - SPK AHP-TOPSIS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="main-container">
        <header>
            <div class="container">
                <h1>🔐 Panel Admin</h1>
                <p>Kelola Data Pakar & Perhitungan AHP-TOPSIS</p>
            </div>
        </header>

        <nav>
            <ul>
                <li><a href="index.php" class="active">🏠 Dashboard</a></li>
                <li><a href="pages/pakar_form.php">➕ Pakar</a></li>
                <li><a href="pages/pairwise_form.php">📊 Pairwise Matrix</a></li>
                <li><a href="pages/decision_form.php">📋 Decision Matrix</a></li>
                <li><a href="pages/results.php">📈 Hasil Kalkulasi</a></li>
                <li style="margin-left: auto;"><a href="../">🏠 Home</a></li>
            </ul>
        </nav>

        <main>
            <div class="container">
                <h2>📊 Dashboard Admin</h2>

                <?php if (!empty($_SESSION['message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                        <?php 
                        echo $_SESSION['message'];
                        unset($_SESSION['message']);
                        unset($_SESSION['message_type']);
                        ?>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">📈 Status Sistem</div>
                    <p><strong>Pakar Aktif:</strong> <?php echo count($pakar); ?> / 1</p>
                    
                    <?php if (count($pakar) === 0): ?>
                        <div class="box" style="margin: 1.5rem 0;">
                            <h3 style="color: var(--warning);">⚠️ Belum Ada Data Pakar</h3>
                            <p>Sistem membutuhkan data pakar untuk melakukan perhitungan. Klik tombol di bawah untuk menambahkan pakar baru.</p>
                            <a href="pages/pakar_form.php" class="btn btn-primary">➕ Tambah Pakar Baru</a>
                        </div>
                    <?php else: ?>
                        <div class="box" style="margin: 1.5rem 0; border-left-color: var(--success);">
                            <h3 style="color: var(--success);">✅ Data Pakar Tersedia</h3>
                            <p>Pakar: <strong><?php echo $pakar[0]['pakar_nama']; ?></strong></p>
                            <p style="font-size: 0.9rem; color: var(--gray); margin-top: 0.5rem;">
                                Dibuat: <?php echo date('d-m-Y H:i', strtotime($pakar[0]['created_at'])); ?>
                            </p>
                        </div>

                        <div class="grid" style="margin-top: 2rem;">
                            <div class="card">
                                <h3>📊 Pairwise Matrix</h3>
                                <p>Input 15 perbandingan berpasangan antar kriteria</p>
                                <a href="pages/pairwise_form.php" class="btn btn-primary" style="margin-top: 1rem;">Edit →</a>
                            </div>
                            <div class="card">
                                <h3>📋 Decision Matrix</h3>
                                <p>Input nilai 5 alternatif untuk 6 kriteria</p>
                                <a href="pages/decision_form.php" class="btn btn-primary" style="margin-top: 1rem;">Edit →</a>
                            </div>
                            <div class="card">
                                <h3>📈 Hasil Kalkulasi</h3>
                                <p>Lihat hasil AHP & TOPSIS ranking</p>
                                <a href="pages/results.php" class="btn btn-primary" style="margin-top: 1rem;">Lihat →</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="box">
                    <h3>📌 Catatan Penting</h3>
                    <ul style="margin-left: 1.5rem;">
                        <li>Sistem hanya mendukung <strong>1 pakar</strong> untuk sekaligus</li>
                        <li>Data pakar dapat diedit dan dihitung ulang kapan saja</li>
                        <li>Perhitungan akan dilakukan setelah semua data lengkap</li>
                        <li>Hasil akan otomatis tersimpan di database</li>
                    </ul>
                </div>
            </div>
        </main>

        <footer>
            <div class="container">
                <p>&copy; 2026 SPK AHP-TOPSIS | Panel Admin</p>
            </div>
        </footer>
    </div>
</body>
</html>
