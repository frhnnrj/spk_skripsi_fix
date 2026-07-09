<?php
require_once '../../config/database.php';

$pakar = get_all_pakar();
if (count($pakar) === 0) {
    redirect('../index.php', 'Belum ada pakar. Tambahkan pakar terlebih dahulu.', 'warning');
}

$pakar_id = $pakar[0]['pakar_id'];
$pakar_nama = $pakar[0]['pakar_nama'];
$pairwise = get_pairwise_matrix($pakar_id);
$decision = get_decision_matrix($pakar_id);
$ahp_results = get_ahp_results($pakar_id);
$topsis_results = get_topsis_results($pakar_id);
$alternatives = get_all_alternatives();

$data_complete = $pairwise && count($decision) === 5 && $ahp_results && $topsis_results;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Kalkulasi - SPK AHP-TOPSIS</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .bobot-table { margin: 1.5rem 0; }
        .bobot-bar { height: 30px; background: linear-gradient(90deg, #3b82f6, #8b5cf6); border-radius: 4px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; }
        .rank-medal { font-size: 2rem; }
        .rank-1 { color: #fbbf24; }
        .rank-2 { color: #9ca3af; }
        .rank-3 { color: #f97316; }
        .rank-other { color: #6b7280; }
    </style>
</head>
<body>
    <div class="main-container">
        <header>
            <div class="container">
                <h1>🔐 Panel Admin</h1>
                <p>Hasil Kalkulasi AHP-TOPSIS</p>
            </div>
        </header>

        <nav>
            <ul>
                <li><a href="../index.php">🏠 Dashboard</a></li>
                <li><a href="pakar_form.php">➕ Pakar</a></li>
                <li><a href="pairwise_form.php">📊 Pairwise Matrix</a></li>
                <li><a href="decision_form.php">📋 Decision Matrix</a></li>
                <li><a href="results.php" class="active">📈 Hasil Kalkulasi</a></li>
                <li style="margin-left: auto;"><a href="../../">🏠 Home</a></li>
            </ul>
        </nav>

        <main>
            <div class="container">
                <h2>📈 Hasil Kalkulasi</h2>
                <p style="color: var(--gray);">Pakar: <strong><?php echo htmlspecialchars($pakar_nama); ?></strong></p>

                <?php if (!$data_complete): ?>
                    <div class="alert alert-warning">
                        <strong>⚠️ Data Belum Lengkap</strong><br>
                        Mohon lengkapi data berikut terlebih dahulu:
                        <ul style="margin-left: 1.5rem; margin-top: 0.5rem;">
                            <?php if (!$pairwise): ?>
                                <li>❌ <a href="pairwise_form.php">Input Pairwise Matrix (15 perbandingan)</a></li>
                            <?php else: ?>
                                <li>✅ Pairwise Matrix sudah diinput</li>
                            <?php endif; ?>
                            
                            <?php if (count($decision) < 5): ?>
                                <li>❌ <a href="decision_form.php">Input Decision Matrix (semua 5 alternatif)</a></li>
                            <?php else: ?>
                                <li>✅ Decision Matrix sudah diinput</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                    <div class="box">
                        <h3>🔄 Next Steps</h3>
                        <ol>
                            <?php if (!$pairwise): ?><li><a href="pairwise_form.php">Lengkapi pairwise matrix</a></li><?php endif; ?>
                            <?php if (count($decision) < 5): ?><li><a href="decision_form.php">Lengkapi decision matrix</a></li><?php endif; ?>
                            <li>Klik tombol "🧮 Hitung AHP & TOPSIS" di bawah</li>
                        </ol>
                    </div>

                    <div class="btn-group" style="margin-top: 2rem;">
                        <form method="POST" action="../process/calculate.php" style="display: inline;">
                            <input type="hidden" name="pakar_id" value="<?php echo $pakar_id; ?>">
                            <button type="submit" class="btn btn-success" onclick="return confirm('Hitung AHP & TOPSIS sekarang?');">🧮 Hitung AHP & TOPSIS</button>
                        </form>
                        <a href="pairwise_form.php" class="btn btn-secondary">📊 Edit Pairwise</a>
                        <a href="decision_form.php" class="btn btn-secondary">📋 Edit Decision</a>
                        <a href="../index.php" class="btn btn-secondary">🏠 Kembali</a>
                    </div>

                <?php else: ?>

                    <div class="alert alert-success">
                        ✅ Data lengkap - Siap untuk kalkulasi
                    </div>

                    <!-- AHP Results -->
                    <div class="card">
                        <h3>📊 Hasil AHP (Analytic Hierarchy Process)</h3>
                        
                        <div class="box">
                            <h4>Bobot Kriteria (Weight)</h4>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Kriteria</th>
                                        <th>Bobot</th>
                                        <th>Visualisasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>K1 - Return</strong></td>
                                        <td><strong><?php echo number_format($ahp_results['w_k1'], 4); ?></strong> (<?php echo number_format($ahp_results['w_k1'] * 100, 1); ?>%)</td>
                                        <td><div class="bobot-bar" style="width: <?php echo min($ahp_results['w_k1'] * 400, 100); ?>%;"></div></td>
                                    </tr>
                                    <tr style="background: var(--light);">
                                        <td><strong>K2 - Risk</strong></td>
                                        <td><strong><?php echo number_format($ahp_results['w_k2'], 4); ?></strong> (<?php echo number_format($ahp_results['w_k2'] * 100, 1); ?>%)</td>
                                        <td><div class="bobot-bar" style="width: <?php echo min($ahp_results['w_k2'] * 400, 100); ?>%;"></div></td>
                                    </tr>
                                    <tr>
                                        <td><strong>K3 - Liquidity</strong></td>
                                        <td><strong><?php echo number_format($ahp_results['w_k3'], 4); ?></strong> (<?php echo number_format($ahp_results['w_k3'] * 100, 1); ?>%)</td>
                                        <td><div class="bobot-bar" style="width: <?php echo min($ahp_results['w_k3'] * 400, 100); ?>%;"></div></td>
                                    </tr>
                                    <tr style="background: var(--light);">
                                        <td><strong>K4 - Capital</strong></td>
                                        <td><strong><?php echo number_format($ahp_results['w_k4'], 4); ?></strong> (<?php echo number_format($ahp_results['w_k4'] * 100, 1); ?>%)</td>
                                        <td><div class="bobot-bar" style="width: <?php echo min($ahp_results['w_k4'] * 400, 100); ?>%;"></div></td>
                                    </tr>
                                    <tr>
                                        <td><strong>K5 - Income</strong></td>
                                        <td><strong><?php echo number_format($ahp_results['w_k5'], 4); ?></strong> (<?php echo number_format($ahp_results['w_k5'] * 100, 1); ?>%)</td>
                                        <td><div class="bobot-bar" style="width: <?php echo min($ahp_results['w_k5'] * 400, 100); ?>%;"></div></td>
                                    </tr>
                                    <tr style="background: var(--light);">
                                        <td><strong>K6 - Access</strong></td>
                                        <td><strong><?php echo number_format($ahp_results['w_k6'], 4); ?></strong> (<?php echo number_format($ahp_results['w_k6'] * 100, 1); ?>%)</td>
                                        <td><div class="bobot-bar" style="width: <?php echo min($ahp_results['w_k6'] * 400, 100); ?>%;"></div></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="box" style="margin-top: 1.5rem; border-left-color: <?php echo $ahp_results['is_consistent'] ? 'var(--success)' : 'var(--danger)'; ?>;">
                            <h4><?php echo $ahp_results['is_consistent'] ? '✅ Konsistensi Terjaga' : '❌ Konsistensi Kurang'; ?></h4>
                            <p>
                                <strong>Lambda Max:</strong> <?php echo number_format($ahp_results['lambda_max'], 4); ?><br>
                                <strong>Consistency Index (CI):</strong> <?php echo number_format($ahp_results['ci'], 4); ?><br>
                                <strong>Consistency Ratio (CR):</strong> <?php echo number_format($ahp_results['cr'] * 100, 2); ?>%
                                <?php if ($ahp_results['is_consistent']): ?>
                                    <span class="badge badge-success">Valid (CR < 10%)</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Invalid (CR ≥ 10%)</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>

                    <!-- TOPSIS Results -->
                    <div class="card">
                        <h3>🎯 Hasil TOPSIS (Ranking Alternatif)</h3>
                        
                        <table>
                            <thead>
                                <tr>
                                    <th>Ranking</th>
                                    <th>Alternatif</th>
                                    <th>Preference Score</th>
                                    <th>Status</th>
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
                                
                                usort($rankings, function($a, $b) {
                                    return $a['rank'] - $b['rank'];
                                });
                                
                                foreach ($rankings as $idx => $item):
                                    $medal_class = 'rank-' . ($item['rank'] <= 3 ? $item['rank'] : 'other');
                                    $medal = $item['rank'] == 1 ? '🥇' : ($item['rank'] == 2 ? '🥈' : ($item['rank'] == 3 ? '🥉' : ''));
                                ?>
                                <tr style="background: <?php echo $item['rank'] <= 3 ? 'rgba(59, 130, 246, 0.1)' : ''; ?>;">
                                    <td><strong class="rank-medal <?php echo $medal_class; ?>"><?php echo $medal ?: $item['rank']; ?></strong></td>
                                    <td><strong><?php echo $item['icon']; ?> <?php echo $item['alt']; ?></strong></td>
                                    <td><strong><?php echo number_format($item['score'], 4); ?></strong></td>
                                    <td>
                                        <?php if ($item['rank'] == 1): ?>
                                            <span class="badge badge-success">🏆 Terbaik</span>
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
                    </div>

                    <div class="btn-group" style="margin-top: 2rem;">
                        <form method="POST" action="../process/calculate.php" style="display: inline;">
                            <input type="hidden" name="pakar_id" value="<?php echo $pakar_id; ?>">
                            <button type="submit" class="btn btn-success" onclick="return confirm('Hitung ulang AHP & TOPSIS sekarang?');">🧮 Hitung Ulang</button>
                        </form>
                        <a href="pairwise_form.php" class="btn btn-secondary">📊 Edit Pairwise</a>
                        <a href="decision_form.php" class="btn btn-secondary">📋 Edit Decision</a>
                        <a href="../index.php" class="btn btn-primary">🏠 Kembali ke Dashboard</a>
                    </div>

                <?php endif; ?>
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
