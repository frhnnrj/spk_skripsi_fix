<?php
require_once '../../config/database.php';

// Require login
require_login();

// Prevent browser caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$pakar = get_all_pakar();
if (count($pakar) === 0) {
    redirect('../index.php', 'Sistem belum siap. Admin masih mempersiapkan data.', 'warning');
}

// Get user ID from session
$user_id = get_current_user_id();

// Get pakar results
$pakar_id = $pakar[0]['pakar_id'];
$ahp_results = get_ahp_results($pakar_id);
$topsis_results = get_topsis_results($pakar_id);

// Get user assessment - ALWAYS fetch fresh data
$user_assessment = null;
$query = "SELECT * FROM tbl_user_assessment WHERE user_id = $user_id AND pakar_id = $pakar_id LIMIT 1";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    $user_assessment = $result->fetch_assoc();
}

// Function: Calculate hybrid ranking based on user preferences
function calculateHybridRanking($alternatif_name, $topsis_score, $user_assessment) {
    // Base dari TOPSIS pakar
    $pakar_weight = 0.6; // 60% dari pakar
    $user_weight = 0.4;  // 40% dari user preference
    
    // Risk tolerance (convert from 0-100 to 0-1)
    $user_risk = $user_assessment['risk_tolerance'] / 100;
    
    // Return expectation (convert from 0-100 to 0-1)
    $user_return = $user_assessment['return_expectation'] / 100;
    
    // Liquidity need (convert from 0-100 to 0-1)
    $user_liquidity = $user_assessment['liquidity_need'] / 100;
    
    // Capital available
    $user_capital = $user_assessment['capital_available'];
    
    // Instrumen characteristics (normalized 0-1)
    $instrumen_profile = [
        'Kripto' => [
            'risk' => 0.90,           // Very high risk
            'return' => 0.85,         // High return
            'liquidity' => 0.85,      // Very liquid
            'min_capital' => 10000,   // Very low minimum
            'income_potential' => 0.40, // Low passive income
        ],
        'Saham' => [
            'risk' => 0.65,           // Medium-high risk
            'return' => 0.70,         // Good return
            'liquidity' => 0.75,      // Liquid (next day)
            'min_capital' => 100000,  // Low minimum
            'income_potential' => 0.50, // Medium dividend
        ],
        'SBN Ritel' => [
            'risk' => 0.10,           // Very low risk
            'return' => 0.35,         // Low return
            'liquidity' => 0.30,      // Low liquidity (hold to maturity)
            'min_capital' => 1000000, // High minimum
            'income_potential' => 0.80, // High coupon
        ],
        'Reksa Dana' => [
            'risk' => 0.50,           // Medium risk
            'return' => 0.60,         // Medium return
            'liquidity' => 0.70,      // Good liquidity (T+3)
            'min_capital' => 100000,  // Low minimum
            'income_potential' => 0.45, // Some dividend
        ],
        'Emas Digital' => [
            'risk' => 0.40,           // Medium-low risk
            'return' => 0.45,         // Medium return
            'liquidity' => 0.90,      // Very liquid
            'min_capital' => 10000,   // Very low minimum
            'income_potential' => 0.20, // Very low income
        ],
    ];
    
    $profile = $instrumen_profile[$alternatif_name];
    
    // Calculate user fit score for this instrument
    $fit_score = 0;
    
    // 1. Risk alignment: if user is aggressive, prefer high-risk; if conservative, prefer low-risk
    if ($user_risk > 0.7) {
        // Aggressive user prefers high risk
        $fit_score += $profile['risk'] * 1.0;
    } elseif ($user_risk < 0.3) {
        // Conservative user prefers low risk
        $fit_score += (1 - $profile['risk']) * 1.0;
    } else {
        // Moderate user likes medium risk
        $risk_diff = abs($profile['risk'] - 0.5);
        $fit_score += (1 - $risk_diff) * 0.8;
    }
    
    // 2. Return expectation alignment
    if ($user_return > 0.7) {
        // Want high return - prefer high return instruments
        $fit_score += $profile['return'] * 0.8;
    } elseif ($user_return < 0.3) {
        // Want low return (conservative) - prefer stable, low return
        $fit_score += (1 - $profile['return']) * 0.6;
    } else {
        // Medium return - prefer medium return
        $return_diff = abs($profile['return'] - 0.5);
        $fit_score += (1 - $return_diff) * 0.6;
    }
    
    // 3. Liquidity need
    $liquidity_diff = abs($profile['liquidity'] - $user_liquidity);
    $fit_score += (1 - $liquidity_diff) * 0.6;
    
    // 4. Capital affordability
    if ($profile['min_capital'] <= $user_capital || $user_capital == 0) {
        // User can afford this instrument
        $fit_score += 1.0 * 0.4;
    } else {
        // User cannot afford
        $fit_score += 0.0;
    }
    
    // 5. Investment goal alignment
    $goal = $user_assessment['user_tujuan_investasi'];
    if ($goal == 'income' && $profile['income_potential'] > 0.6) {
        $fit_score += 1.0 * 0.5;
    } elseif ($goal == 'growth' && $profile['return'] > 0.6) {
        $fit_score += 1.0 * 0.5;
    } elseif ($goal == 'preservation' && $profile['risk'] < 0.4) {
        $fit_score += 1.0 * 0.5;
    } elseif ($goal == 'mixed') {
        // Mixed goal likes balanced instruments
        $fit_score += 0.7 * 0.5;
    }
    
    // Normalize fit score to 0-1 range
    $fit_score = min(1.0, max(0, $fit_score / 6)); // Divide by number of criteria
    
    // Calculate hybrid score: 60% dari pakar + 40% dari user fit
    $hybrid_score = ($pakar_weight * $topsis_score) + ($user_weight * $fit_score);
    
    return [
        'hybrid_score' => $hybrid_score,
        'user_fit' => $fit_score,
        'pakar_score' => $topsis_score,
    ];
}

// Calculate hybrid rankings for all alternatives
$hybrid_rankings = [];
if ($user_assessment && $topsis_results) {
    $alternatives = [
        ['name' => 'Kripto', 'icon' => '🪙', 'score' => $topsis_results['kripto_preference']],
        ['name' => 'Saham', 'icon' => '📈', 'score' => $topsis_results['saham_preference']],
        ['name' => 'SBN Ritel', 'icon' => '🏛️', 'score' => $topsis_results['sbn_preference']],
        ['name' => 'Reksa Dana', 'icon' => '💼', 'score' => $topsis_results['reksadana_preference']],
        ['name' => 'Emas Digital', 'icon' => '🔘', 'score' => $topsis_results['emasdigital_preference']],
    ];
    
    foreach ($alternatives as $alt) {
        $hybrid = calculateHybridRanking($alt['name'], $alt['score'], $user_assessment);
        $hybrid_rankings[] = [
            'name' => $alt['name'],
            'icon' => $alt['icon'],
            'hybrid_score' => $hybrid['hybrid_score'],
            'user_fit' => $hybrid['user_fit'],
            'pakar_score' => $hybrid['pakar_score'],
        ];
    }
    
    // Sort by hybrid score (descending)
    usort($hybrid_rankings, function($a, $b) {
        return $b['hybrid_score'] <=> $a['hybrid_score'];
    });
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Rekomendasi - SPK AHP-TOPSIS</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .comparison-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin: 2rem 0; }
        .comparison-card { background: white; border-radius: 8px; padding: 1.5rem; border: 2px solid #e5e7eb; }
        .comparison-card h4 { margin-top: 0; color: var(--primary); }
        .rank-item { display: flex; align-items: center; padding: 1rem; background: #f9fafb; border-radius: 6px; margin-bottom: 0.5rem; }
        .rank-medal { font-size: 1.5rem; margin-right: 1rem; min-width: 40px; text-align: center; }
        .rank-info { flex: 1; }
        .rank-name { font-weight: 600; }
        .rank-score { color: var(--gray); font-size: 0.9rem; }
        .match-meter { height: 40px; background: #e5e7eb; border-radius: 4px; overflow: hidden; display: flex; align-items: center; margin: 1rem 0; }
        .match-fill { background: linear-gradient(90deg, var(--success), var(--primary)); height: 100%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; }
    </style>
</head>
<body>
    <div class="main-container">
        <header>
            <div class="container">
                <h1>👤 Panel User</h1>
                <p>Hasil Rekomendasi Investasi</p>
            </div>
        </header>

        <nav>
            <ul>
                <li><a href="../index.php">🏠 Beranda</a></li>
                <li><a href="education.php">📚 Belajar</a></li>
                <li><a href="assessment.php">📝 Penilaian</a></li>
                <li><a href="results.php" class="active">📊 Hasil</a></li>
                <li style="margin-left: auto;"><a href="../../">🏠 Home</a></li>
            </ul>
        </nav>

        <main>
            <div class="container">
                <h2>📊 Hasil Rekomendasi Investasi</h2>

                <?php if (!$user_assessment): ?>
                    <div class="alert alert-warning">
                        <strong>⚠️ Belum Ada Penilaian</strong><br>
                        Silakan <a href="assessment.php">isi form penilaian</a> terlebih dahulu untuk menerima rekomendasi yang dipersonalisasi.
                    </div>

                    <div class="box" style="margin-top: 1rem;">
                        <h3>ℹ️ Informasi</h3>
                        <p>
                            Sistem ini akan membandingkan penilaian Anda dengan penilaian pakar untuk memberikan rekomendasi investasi yang paling sesuai dengan profil risiko dan preferensi Anda.
                        </p>
                        <a href="assessment.php" class="btn btn-primary" style="margin-top: 1rem;">Mulai Penilaian →</a>
                    </div>
                <?php else: ?>

                    <!-- Info Box -->
                    <div class="box" style="background: linear-gradient(135deg, rgba(59,130,246,0.1), rgba(139,92,246,0.1)); border-left-color: var(--primary); margin-bottom: 2rem;">
                        <p style="margin: 0; color: var(--text);">
                            💡 <strong>Tip:</strong> Setelah Anda mengubah penilaian di halaman <a href="assessment.php">📝 Penilaian</a>, 
                            gunakan tombol <strong>🔄 Refresh Data</strong> di bawah untuk melihat rekomendasi terbaru!
                        </p>
                    </div>

                    <!-- Hybrid Ranking Results (Personalized for User) -->
                    <div class="card">
                        <h3>🎯 Ranking untuk Profil Anda (Hybrid Analysis)</h3>
                        <p style="color: var(--gray); margin-bottom: 1.5rem;">
                            Ranking disesuaikan dengan preferensi dan profil investasi Anda (60% Analisis Pakar + 40% Profil Anda)
                        </p>

                        <div style="padding: 1rem; background: rgba(100,116,139,0.05); border-left: 3px solid var(--gray); border-radius: 4px; margin-bottom: 1.5rem;">
                            <p style="margin: 0; font-size: 0.9rem; color: var(--gray);">
                                ✨ <strong>Ranking ini DINAMIS!</strong> Setiap kali Anda mengubah preferensi investasi Anda, ranking berubah untuk mencerminkan pilihan terbaik untuk profil Anda. 
                                Coba ubah penilaian Anda untuk melihat perubahan! 
                            </p>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            <?php 
                            if (!empty($hybrid_rankings)):
                                foreach ($hybrid_rankings as $idx => $item):
                                    $rank = $idx + 1;
                                    $medal = $rank == 1 ? '🥇' : ($rank == 2 ? '🥈' : ($rank == 3 ? '🥉' : ''));
                                    $bg_color = $rank <= 3 ? 'rgba(34,197,94,0.1)' : '#f9fafb';
                            ?>
                            <div class="rank-item" style="background: <?php echo $bg_color; ?>; flex-wrap: wrap;">
                                <div class="rank-medal"><?php echo $medal ?: '#' . $rank; ?></div>
                                <div class="rank-info" style="flex: 1; min-width: 150px;">
                                    <div class="rank-name"><?php echo $item['icon']; ?> <?php echo $item['name']; ?></div>
                                    <div class="rank-score" style="font-size: 0.85rem; margin-top: 0.25rem;">
                                        Score: <strong><?php echo number_format($item['hybrid_score'], 4); ?></strong>
                                        <br>
                                        <small style="color: #888;">Fit dengan profil Anda: <strong><?php echo round($item['user_fit'] * 100); ?>%</strong></small>
                                    </div>
                                </div>
                                <div style="text-align: right; min-width: 100px;">
                                    <?php if ($rank == 1): ?>
                                        <span class="badge badge-success">💡 Terbaik Untuk Anda</span>
                                    <?php elseif ($rank == 2): ?>
                                        <span class="badge badge-info">Pilihan Kedua</span>
                                    <?php elseif ($rank == 3): ?>
                                        <span class="badge badge-warning">Alternatif</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php 
                                endforeach;
                            else:
                            ?>
                            <p style="color: var(--gray); text-align: center; padding: 2rem;">
                                Perbarui penilaian Anda untuk melihat ranking yang dipersonalisasi
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- User Assessment Summary -->
                    <div class="card" style="margin-top: 2rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                            <h3 style="margin: 0;">👤 Profil Penilaian Anda</h3>
                            <button onclick="location.reload();" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                                🔄 Refresh Data
                            </button>
                        </div>
                        <p style="color: var(--gray); margin-bottom: 1.5rem;">
                            Ringkasan dari penilaian Anda
                        </p>

                        <table style="width: 100%;">
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <td style="padding: 1rem;"><strong>Nama:</strong></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($user_assessment['user_nama']); ?></td>
                            </tr>
                            <tr style="border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                                <td style="padding: 1rem;"><strong>Usia:</strong></td>
                                <td style="padding: 1rem;"><?php echo $user_assessment['user_usia']; ?> tahun</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <td style="padding: 1rem;"><strong>Toleransi Risiko:</strong></td>
                                <td style="padding: 1rem;">
                                    <strong><?php echo intval($user_assessment['risk_tolerance'] / 10); ?>/10</strong>
                                    (<?php echo $user_assessment['risk_tolerance'] <= 30 ? 'Konservatif' : ($user_assessment['risk_tolerance'] <= 60 ? 'Sedang' : 'Agresif'); ?>)
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                                <td style="padding: 1rem;"><strong>Ekspektasi Return:</strong></td>
                                <td style="padding: 1rem;">
                                    <strong><?php echo intval($user_assessment['return_expectation'] / 10); ?>/10</strong>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <td style="padding: 1rem;"><strong>Kebutuhan Likuiditas:</strong></td>
                                <td style="padding: 1rem;">
                                    <strong><?php echo intval($user_assessment['liquidity_need'] / 10); ?>/10</strong>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                                <td style="padding: 1rem;"><strong>Modal Tersedia:</strong></td>
                                <td style="padding: 1rem;">Rp <?php echo number_format($user_assessment['capital_available'], 0, ',', '.'); ?></td>
                            </tr>
                            <tr style="background: #f9fafb;">
                                <td style="padding: 1rem;"><strong>Tujuan Investasi:</strong></td>
                                <td style="padding: 1rem;">
                                    <?php 
                                    $goal_labels = ['growth' => 'Growth', 'income' => 'Income', 'preservation' => 'Preservation', 'mixed' => 'Mixed'];
                                    echo $goal_labels[$user_assessment['user_tujuan_investasi']] ?? 'N/A';
                                    ?>
                                </td>
                            </tr>
                            <tr style="border-top: 2px solid #e5e7eb;">
                                <td style="padding: 1rem;"><strong>⏰ Terakhir Diupdate:</strong></td>
                                <td style="padding: 1rem; color: var(--gray); font-size: 0.9rem;">
                                    <?php 
                                    if ($user_assessment['created_at']) {
                                        $date = new DateTime($user_assessment['created_at']);
                                        echo $date->format('d M Y, H:i');
                                    } else {
                                        echo 'Baru saja';
                                    }
                                    ?>
                                </td>
                            </tr>
                        </table>

                        <div style="margin-top: 1.5rem;">
                            <a href="assessment.php" class="btn btn-secondary">✏️ Ubah Penilaian</a>
                        </div>
                    </div>

                    <!-- Recommendations based on profile -->
                    <div class="card" style="margin-top: 2rem;">
                        <h3>🎯 Rekomendasi untuk Anda</h3>
                        
                        <?php 
                        // Simple recommendation logic based on profile
                        // Values are 0-100
                        $risk_score = $user_assessment['risk_tolerance'];
                        $return_score = $user_assessment['return_expectation'];
                        $liquidity_score = $user_assessment['liquidity_need'];

                        $recommendation = "";
                        $explanation = "";

                        if ($risk_score <= 30) {
                            // Conservative investor
                            $recommendation = "🏛️ SBN Ritel + 🔘 Emas Digital";
                            $explanation = "Anda adalah investor konservatif. Kombinasi SBN Ritel (return pasti) dan Emas Digital (hedge inflation) adalah pilihan terbaik untuk menjaga modal Anda dengan risiko minimal.";
                        } elseif ($risk_score <= 60) {
                            // Moderate investor
                            if ($liquidity_score >= 70) {
                                $recommendation = "💼 Reksa Dana + 🔘 Emas Digital";
                                $explanation = "Dengan kebutuhan likuiditas tinggi, kombinasi Reksa Dana (diversifikasi, withdrawal T+3) dan Emas Digital (high liquidity) sangat cocok untuk profil Anda.";
                            } else {
                                $recommendation = "💼 Reksa Dana + 📈 Saham";
                                $explanation = "Dengan tolerance risiko sedang, diversifikasi antara Reksa Dana (untuk diversifikasi otomatis) dan Saham (untuk growth potential) sangat cocok untuk profil Anda.";
                            }
                        } else {
                            // Aggressive investor
                            if ($liquidity_score >= 70) {
                                $recommendation = "🪙 Kripto + 📈 Saham";
                                $explanation = "Anda adalah investor agresif dengan kebutuhan likuiditas tinggi. Kombinasi Kripto (return tinggi, likuid) dan Saham (growth) akan memaksimalkan return Anda.";
                            } else {
                                $recommendation = "📈 Saham + 🪙 Kripto";
                                $explanation = "Sebagai investor agresif dengan jangka waktu panjang, portofolio yang berfokus pada Saham dan Kripto akan memberikan return maksimal sesuai profil risiko Anda.";
                            }
                        }
                        ?>

                        <div style="padding: 1.5rem; background: linear-gradient(135deg, rgba(59,130,246,0.1), rgba(139,92,246,0.1)); border-radius: 8px; border-left: 4px solid var(--primary); margin-bottom: 1.5rem;">
                            <h4 style="margin-top: 0;">Rekomendasi Utama</h4>
                            <p style="font-size: 1.2rem; font-weight: 600; color: var(--primary); margin: 1rem 0;">
                                <?php echo $recommendation; ?>
                            </p>
                            <p style="margin: 0; color: var(--text);">
                                <?php echo $explanation; ?>
                            </p>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                            <div style="padding: 1rem; background: rgba(34,197,94,0.1); border-radius: 6px; border-left: 3px solid var(--success);">
                                <p style="margin: 0; color: var(--gray); font-size: 0.9rem;">Profil Investor</p>
                                <p style="margin: 0.5rem 0 0 0; font-size: 1.1rem; font-weight: 600;">
                                    <?php 
                                    if ($risk_score <= 30) echo "🟢 Konservatif";
                                    elseif ($risk_score <= 60) echo "🟡 Moderat";
                                    else echo "🔴 Agresif";
                                    ?>
                                </p>
                            </div>
                            <div style="padding: 1rem; background: rgba(59,130,246,0.1); border-radius: 6px; border-left: 3px solid var(--primary);">
                                <p style="margin: 0; color: var(--gray); font-size: 0.9rem;">Divorsifikasi Disarankan</p>
                                <p style="margin: 0.5rem 0 0 0; font-size: 1.1rem; font-weight: 600;">
                                    <?php echo $risk_score <= 30 ? "2-3 Instrumen" : ($risk_score <= 60 ? "3-4 Instrumen" : "4-5 Instrumen"); ?>
                                </p>
                            </div>
                        </div>

                        <div style="padding: 1rem; background: rgba(100,116,139,0.1); border-left: 3px solid var(--gray); border-radius: 6px;">
                            <p style="margin: 0; font-size: 0.9rem; color: var(--gray);">
                                💡 <strong>Catatan:</strong> Rekomendasi ini didasarkan pada profil penilaian Anda dan analisis pakar. Selalu lakukan riset mendalam dan konsultasi dengan advisor keuangan sebelum mengambil keputusan investasi.
                            </p>
                        </div>
                    </div>

                    <!-- Summary Card -->
                    <div class="box" style="margin-top: 2rem; background: linear-gradient(135deg, rgba(34,197,94,0.1), rgba(59,130,246,0.1)); border-left-color: var(--success);">
                        <h3>✅ Apa Selanjutnya?</h3>
                        <p>
                            Gunakan rekomendasi di atas sebagai panduan untuk membuat keputusan investasi Anda. 
                            Pastikan untuk:
                        </p>
                        <ul>
                            <li>Riset lebih dalam tentang instrumen yang direkomendasikan</li>
                            <li>Memahami risiko dan return setiap instrumen</li>
                            <li>Membuat portfolio yang sesuai dengan kapabilitas modal Anda</li>
                            <li>Melakukan diversifikasi untuk meminimalkan risiko</li>
                            <li>Melakukan review berkala dan rebalancing</li>
                        </ul>
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
