<?php
require_once '../config/database.php';

$pakar = get_all_pakar();
$current_user = get_current_user();
$is_logged_in = is_logged_in();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel User - SPK AHP-TOPSIS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="main-container">
        <header>
            <div class="container">
                <h1>👤 Panel User</h1>
                <p>Dapatkan Rekomendasi Investasi Terbaik untuk Anda</p>
            </div>
        </header>

        <nav>
            <ul>
                <li><a href="index.php" class="active">🏠 Beranda</a></li>
                <li><a href="pages/education.php">📚 Belajar</a></li>
                <?php if ($is_logged_in): ?>
                    <li><a href="pages/assessment.php">📝 Penilaian</a></li>
                    <li><a href="pages/results.php">📊 Hasil</a></li>
                <?php endif; ?>
                <li class="user-menu">
                    <?php if ($is_logged_in): ?>
                        <span class="text-muted">👤 <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
                        <a href="logout.php">🚪 Logout</a>
                    <?php else: ?>
                        <a href="login.php">🔐 Login</a>
                        <span class="text-muted">/</span>
                        <a href="signup.php">✨ Daftar</a>
                    <?php endif; ?>
                </li>
                <li><a href="../">🏠 Home</a></li>
            </ul>
        </nav>

        <main>
            <div class="container">
                <h2>Selamat Datang di Panel User!</h2>

                <?php if (count($pakar) === 0): ?>
                    <div class="alert alert-warning">
                        <strong>⚠️ Sistem Belum Siap</strong><br>
                        Admin masih mempersiapkan data pakar. Silakan kembali lagi nanti.
                    </div>
                <?php else: ?>
                    <div class="hero-panel">
                        <div class="hero-card">
                            <h3>👋 Halo Investor Gen Z!</h3>
                            <p>
                                Dapatkan rekomendasi investasi yang lebih objektif berdasarkan profil Anda dan
                                hasil perhitungan AHP-TOPSIS.
                            </p>
                            <div class="quick-actions">
                                <a href="pages/education.php" class="btn btn-secondary">Mulai Belajar</a>
                                <?php if ($is_logged_in): ?>
                                    <a href="pages/assessment.php" class="btn btn-secondary">Isi Penilaian</a>
                                <?php else: ?>
                                    <a href="login.php" class="btn btn-secondary">Login untuk Mulai</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="stat-card">
                            <p class="stat-label">Data Pakar</p>
                            <p class="stat-value"><?php echo count($pakar); ?> / 1</p>
                            <p class="text-muted mt-1">Status data pakar aktif untuk referensi rekomendasi.</p>
                            <span class="badge badge-success">Siap Digunakan</span>
                        </div>
                    </div>

                    <?php if (!$is_logged_in): ?>
                        <div class="lock-card mb-3">
                            <h3>🔐 Akses Terbatas - Silakan Login</h3>
                            <p>
                                Anda saat ini mengakses sebagai pengunjung. Untuk mendapatkan penilaian dan rekomendasi investasi yang dipersonalisasi, 
                                silakan login atau buat akun baru.
                            </p>
                            <div class="quick-actions">
                                <a href="login.php" class="btn btn-primary">🔐 Login</a>
                                <a href="signup.php" class="btn btn-success">✨ Daftar Akun Baru</a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="grid">
                        <div class="card">
                            <h3>📚 Belajar Dulu</h3>
                            <p>Pahami dasar-dasar investasi dan 5 instrumen yang tersedia</p>
                            <a href="pages/education.php" class="btn btn-primary mt-2">Mulai Belajar →</a>
                        </div>
                        <?php if ($is_logged_in): ?>
                            <div class="card">
                                <h3>📝 Lakukan Penilaian</h3>
                                <p>Jawab pertanyaan tentang profil risiko dan preferensi investasi Anda</p>
                                <a href="pages/assessment.php" class="btn btn-primary mt-2">Mulai Penilaian →</a>
                            </div>
                            <div class="card">
                                <h3>📊 Lihat Hasil</h3>
                                <p>Dapatkan ranking dan rekomendasi investasi yang dipersonalisasi</p>
                                <a href="pages/results.php" class="btn btn-primary mt-2">Lihat Hasil →</a>
                            </div>
                        <?php else: ?>
                            <div class="card muted-card">
                                <h3>📝 Lakukan Penilaian</h3>
                                <p>Silakan login untuk mengakses fitur penilaian</p>
                                <a href="login.php" class="btn btn-secondary mt-2">Penilaian (Login Diperlukan)</a>
                            </div>
                            <div class="card muted-card">
                                <h3>📊 Lihat Hasil</h3>
                                <p>Silakan login untuk mengakses fitur hasil</p>
                                <a href="login.php" class="btn btn-secondary mt-2">Hasil (Login Diperlukan)</a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Expert Assessment Results (Real-time) -->
                    <?php 
                        if (count($pakar) > 0) {
                            $pakar_id = $pakar[0]['pakar_id'];
                            $pakar_nama = $pakar[0]['pakar_nama'];
                            $ahp_results = get_ahp_results($pakar_id);
                            $topsis_results = get_topsis_results($pakar_id);
                            
                            if ($ahp_results && $topsis_results):
                    ?>
                    <div class="card" style="background: linear-gradient(135deg, rgba(139,92,246,0.1), rgba(59,130,246,0.1)); border-left: 4px solid var(--primary); margin-top: 2rem;">
                        <h3>🔍 Hasil Penilaian Pakar (Real-time)</h3>
                        <p style="color: var(--gray); margin-bottom: 1.5rem;">
                            Pakar: <strong><?php echo htmlspecialchars($pakar_nama); ?></strong> | 
                            Perbarui otomatis saat admin mengubah data
                        </p>

                        <!-- AHP Weights Section -->
                        <div style="background: white; padding: 1.5rem; border-radius: 6px; margin-bottom: 1.5rem;">
                            <h4 style="margin-bottom: 1rem; color: var(--primary);">📊 Bobot Kriteria (AHP Weights)</h4>
                            
                            <table style="width: 100%; margin-bottom: 1rem;">
                                <thead>
                                    <tr style="background: rgba(59,130,246,0.1);">
                                        <th style="text-align: left; padding: 0.75rem;">Kriteria</th>
                                        <th style="text-align: right; padding: 0.75rem;">Bobot</th>
                                        <th style="text-align: right; padding: 0.75rem;">Persentase</th>
                                        <th style="padding: 0.75rem;">Visualisasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $criteria_info = [
                                        ['name' => 'K1 - Return', 'key' => 'w_k1', 'icon' => '💰', 'color' => '#3b82f6'],
                                        ['name' => 'K2 - Risk', 'key' => 'w_k2', 'icon' => '📉', 'color' => '#ef4444'],
                                        ['name' => 'K3 - Liquidity', 'key' => 'w_k3', 'icon' => '💧', 'color' => '#06b6d4'],
                                        ['name' => 'K4 - Capital', 'key' => 'w_k4', 'icon' => '💵', 'color' => '#8b5cf6'],
                                        ['name' => 'K5 - Income', 'key' => 'w_k5', 'icon' => '📈', 'color' => '#10b981'],
                                        ['name' => 'K6 - Access', 'key' => 'w_k6', 'icon' => '🔑', 'color' => '#f59e0b'],
                                    ];
                                    foreach ($criteria_info as $crit): 
                                        $weight = $ahp_results[$crit['key']];
                                        $percentage = $weight * 100;
                                    ?>
                                    <tr style="border-bottom: 1px solid #e5e7eb;">
                                        <td style="padding: 0.75rem;"><strong><?php echo $crit['icon']; ?> <?php echo $crit['name']; ?></strong></td>
                                        <td style="text-align: right; padding: 0.75rem; font-weight: 600;"><?php echo number_format($weight, 4); ?></td>
                                        <td style="text-align: right; padding: 0.75rem; font-weight: 600;"><?php echo number_format($percentage, 1); ?>%</td>
                                        <td style="padding: 0.75rem;">
                                            <div style="height: 24px; background: #e5e7eb; border-radius: 4px; overflow: hidden; display: flex; align-items: center;">
                                                <div style="height: 100%; background: <?php echo $crit['color']; ?>; width: <?php echo min($percentage * 3, 100); ?>%; transition: width 0.3s;"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
                                <div style="padding: 1rem; background: rgba(59,130,246,0.1); border-radius: 4px;">
                                    <p style="margin: 0; color: var(--gray); font-size: 0.9rem;">Lambda Max</p>
                                    <p style="margin: 0.5rem 0 0 0; font-size: 1.3rem; font-weight: 600; color: var(--primary);">
                                        <?php echo number_format($ahp_results['lambda_max'], 4); ?>
                                    </p>
                                </div>
                                <div style="padding: 1rem; background: rgba(59,130,246,0.1); border-radius: 4px;">
                                    <p style="margin: 0; color: var(--gray); font-size: 0.9rem;">Consistency Ratio (CR)</p>
                                    <p style="margin: 0.5rem 0 0 0; font-size: 1.3rem; font-weight: 600; color: <?php echo $ahp_results['is_consistent'] ? 'var(--success)' : 'var(--danger)'; ?>;">
                                        <?php echo number_format($ahp_results['cr'] * 100, 2); ?>%
                                        <span style="font-size: 0.8rem; margin-left: 0.5rem;">
                                            <?php echo $ahp_results['is_consistent'] ? '✅ Valid' : '❌ Invalid'; ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- TOPSIS Rankings Section -->
                        <div style="background: white; padding: 1.5rem; border-radius: 6px;">
                            <h4 style="margin-bottom: 1rem; color: var(--primary);">🏆 Ranking Alternatif Investasi (TOPSIS)</h4>
                            
                            <table style="width: 100%;">
                                <thead>
                                    <tr style="background: rgba(59,130,246,0.1);">
                                        <th style="text-align: center; padding: 0.75rem;">Rank</th>
                                        <th style="text-align: left; padding: 0.75rem;">Instrumen</th>
                                        <th style="text-align: right; padding: 0.75rem;">Preference Score</th>
                                        <th style="text-align: center; padding: 0.75rem;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $rankings = [
                                        ['alt' => 'Kripto', 'icon' => '🪙', 'score' => $topsis_results['kripto_preference'], 'rank' => $topsis_results['kripto_rank']],
                                        ['alt' => 'Saham', 'icon' => '📈', 'score' => $topsis_results['saham_preference'], 'rank' => $topsis_results['saham_rank']],
                                        ['alt' => 'SBN Ritel', 'icon' => '🏛️', 'score' => $topsis_results['sbn_preference'], 'rank' => $topsis_results['sbn_rank']],
                                        ['alt' => 'Reksa Dana', 'icon' => '💼', 'score' => $topsis_results['reksadana_preference'], 'rank' => $topsis_results['reksadana_rank']],
                                        ['alt' => 'Emas Digital', 'icon' => '🔘', 'score' => $topsis_results['emasdigital_preference'], 'rank' => $topsis_results['emasdigital_rank']],
                                    ];
                                    
                                    usort($rankings, function($a, $b) { return $a['rank'] - $b['rank']; });
                                    
                                    foreach ($rankings as $item):
                                        $medal = $item['rank'] == 1 ? '🥇' : ($item['rank'] == 2 ? '🥈' : ($item['rank'] == 3 ? '🥉' : ''));
                                        $bg_color = $item['rank'] <= 3 ? 'rgba(34,197,94,0.1)' : '';
                                    ?>
                                    <tr style="border-bottom: 1px solid #e5e7eb; background: <?php echo $bg_color; ?>;">
                                        <td style="text-align: center; padding: 0.75rem; font-weight: 600; font-size: 1.2rem;">
                                            <?php echo $medal ?: '#' . $item['rank']; ?>
                                        </td>
                                        <td style="text-align: left; padding: 0.75rem;"><strong><?php echo $item['icon']; ?> <?php echo $item['alt']; ?></strong></td>
                                        <td style="text-align: right; padding: 0.75rem; font-weight: 600; color: var(--primary);">
                                            <?php echo number_format($item['score'], 4); ?>
                                        </td>
                                        <td style="text-align: center; padding: 0.75rem;">
                                            <?php if ($item['rank'] == 1): ?>
                                                <span class="badge badge-success">Terbaik</span>
                                            <?php elseif ($item['rank'] == 2): ?>
                                                <span class="badge badge-info">Kedua</span>
                                            <?php elseif ($item['rank'] == 3): ?>
                                                <span class="badge badge-warning">Ketiga</span>
                                            <?php else: ?>
                                                <span class="badge">Rank <?php echo $item['rank']; ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <div style="margin-top: 1rem; padding: 1rem; background: rgba(34,197,94,0.1); border-left: 3px solid var(--success); border-radius: 4px;">
                                <p style="margin: 0; color: var(--success); font-size: 0.9rem;">
                                    ✅ <strong>Rekomendasi Pakar:</strong> <?php echo htmlspecialchars($rankings[0]['alt']); ?> adalah pilihan investasi terbaik menurut penilaian pakar berdasarkan analisis AHP-TOPSIS.
                                </p>
                            </div>
                        </div>

                        <div style="margin-top: 1.5rem; padding: 1rem; background: rgba(100,116,139,0.1); border-left: 3px solid var(--gray); border-radius: 4px;">
                            <p style="margin: 0; color: var(--gray); font-size: 0.9rem;">
                                🔄 <strong>Data Real-time:</strong> Hasil ini diperbarui otomatis setiap kali admin mengubah penilaian pakar dan menjalankan perhitungan AHP-TOPSIS.
                            </p>
                        </div>
                    </div>
                    <?php 
                            else:
                    ?>
                    <div class="alert alert-info" style="margin-top: 2rem;">
                        <strong>⏳ Menunggu Hasil Penilaian</strong><br>
                        Admin masih mempersiapkan penilaian pakar. Hasil akan ditampilkan di sini setelah perhitungan selesai.
                    </div>
                    <?php 
                            endif;
                        }
                    ?>

                    

                    <div class="box" style="border-left-color: var(--info);">
                        <h3>ℹ️ Bagaimana Cara Kerjanya?</h3>
                        <ol>
                            <li><strong>Belajar:</strong> Pelajari tentang 5 instrumen investasi tersedia</li>
                            <li><strong>Penilaian:</strong> Jawab pertanyaan tentang preferensi investasi Anda</li>
                            <li><strong>Hasil:</strong> Dapatkan ranking dari sistem berdasarkan algoritma AHP-TOPSIS</li>
                            <li><strong>Perbandingan:</strong> Lihat bagaimana pilihan Anda dibanding dengan penilaian pakar</li>
                        </ol>
                    </div>

                    <div class="box">
                        <h3>🎯 5 Instrumen Investasi</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Instrumen</th>
                                    <th>Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>🪙 Kripto</td>
                                    <td>Cryptocurrency / Digital Currency (Bitcoin, Ethereum, dll)</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>📈 Saham</td>
                                    <td>Equity / Stock Market</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>🏛️ SBN Ritel</td>
                                    <td>Surat Berharga Negara Ritel</td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>💼 Reksa Dana</td>
                                    <td>Mutual Fund</td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>🔘 Emas Digital</td>
                                    <td>Digital Gold / E-Gold</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </main>

        <footer>
            <div class="container">
                <p>&copy; 2026 SPK AHP-TOPSIS | Panel User</p>
            </div>
        </footer>
    </div>
</body>
</html>
