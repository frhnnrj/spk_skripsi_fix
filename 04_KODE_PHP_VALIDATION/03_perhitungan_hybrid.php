<?php
/**
 * PERHITUNGAN AHP + TOPSIS (COMPLETE FLOW)
 * 
 * File: 03_perhitungan_hybrid.php
 * Tujuan: Menampilkan perhitungan lengkap AHP + TOPSIS secara detail
 * 
 * Flow:
 * 1. Hitung AHP (dapatkan expert weights dari pairwise matrix)
 * 2. Hitung TOPSIS dengan weights dari AHP
 * 3. Tampilkan ranking akhir
 */

// ================================================================
// STEP 0: INPUT DATA
// ================================================================

// Pairwise Matrix (untuk AHP) - ARRAY TERBARU DARI USER
$pairwise = array(
    array(1,     3,   1,     2,     3,     4),
    array(1/3,   1,     1/2,   2,     1/2,   2),
    array(1,     2,   1,     3,     1/2,   2),
    array(1/2,   1/2,   1/3,   1,     1/2,   3),
    array(1/3,   2,   2,     2,     1,     3),
    array(1/4,   1/2,   1/2,   1/3,   1/3,   1)
);

// Decision Matrix (untuk TOPSIS) - DATA ASLI (BUKAN NORMALIZED)
// Instrumen Investasi    K1 Return%  K2 Risk(%)  K3 Liquid  K4 Modal(Rp)  K5 Income%  K6 Access(1-10)
$decision_matrix_raw = array(
    array(35,    78,    9,    5000,      0,    7),      // Kripto
    array(14,    42,    8,    50000,     3.0,  8),      // Saham
    array(7,     8,     7,    1000000,   6.8,  6),      // SBN Ritel
    array(11,    28,    6,    50000,     2.5,  8),      // Reksa Dana
    array(5,     18,    9,    10000,     0,    7)       // Emas Digital
);

$alternatives = array('Kripto', 'Saham', 'SBN Ritel', 'Reksa Dana', 'Emas Digital');
$criteria_names = array('K1: Return', 'K2: Risk', 'K3: Liquidity', 'K4: Capital', 'K5: Income', 'K6: Access');

// Criteria Type: benefit (lebih tinggi lebih baik) atau cost (lebih rendah lebih baik)
$criteria_type = array('benefit', 'cost', 'benefit', 'cost', 'benefit', 'benefit');

// ================================================================
// HEADER
// ================================================================
echo "\n";
echo "╔═══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║              PERHITUNGAN AHP + TOPSIS (FLOW LENGKAP)                         ║\n";
echo "║                        Dari Expert Weights ke Ranking                         ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

// ================================================================
// BAGIAN 1: HITUNG AHP (EXPERT WEIGHTS)
// ================================================================
echo "═══════════════════════════════════════════════════════════════════════════════\n";
echo "BAGIAN 1: PERHITUNGAN AHP (MENDAPATKAN EXPERT WEIGHTS)\n";
echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

// Calculate column sums
$column_sums = array();
for ($j = 0; $j < count($pairwise[0]); $j++) {
    $sum = 0;
    for ($i = 0; $i < count($pairwise); $i++) {
        $sum += $pairwise[$i][$j];
    }
    $column_sums[$j] = $sum;
}

echo "1.1 JUMLAH KOLOM PAIRWISE MATRIX:\n";
for ($j = 0; $j < count($column_sums); $j++) {
    printf("  Sum K%d = %.4f\n", ($j+1), $column_sums[$j]);
}
echo "\n";

// Normalize matrix
$normalized_ahp = array();
for ($i = 0; $i < count($pairwise); $i++) {
    $normalized_ahp[$i] = array();
    for ($j = 0; $j < count($pairwise[$i]); $j++) {
        $normalized_ahp[$i][$j] = $pairwise[$i][$j] / $column_sums[$j];
    }
}

// Calculate eigenvector (weights)
$expert_weights = array();
for ($i = 0; $i < count($normalized_ahp); $i++) {
    $sum = 0;
    for ($j = 0; $j < count($normalized_ahp[$i]); $j++) {
        $sum += $normalized_ahp[$i][$j];
    }
    $expert_weights[$i] = $sum / count($normalized_ahp[$i]);
}

echo "1.2 EXPERT WEIGHTS (BOBOT PRIORITAS):\n";
for ($i = 0; $i < count($expert_weights); $i++) {
    printf("  w_%d (%-20s) = %.4f  (%.2f%%)\n", ($i+1), "K" . ($i+1), $expert_weights[$i], $expert_weights[$i] * 100);
}
echo "\n";

// Calculate lambda max and consistency
$lambda_values = array();
for ($i = 0; $i < count($pairwise); $i++) {
    $sum_products = 0;
    for ($j = 0; $j < count($pairwise[$i]); $j++) {
        $sum_products += $pairwise[$i][$j] * $expert_weights[$j];
    }
    $lambda_values[$i] = $sum_products / $expert_weights[$i];
}
$lambda_max = array_sum($lambda_values) / count($lambda_values);

$n = count($pairwise);
$ci = ($lambda_max - $n) / ($n - 1);

$ri_table = array(1 => 0.00, 2 => 0.00, 3 => 0.58, 4 => 0.90, 5 => 1.12, 
                  6 => 1.24, 7 => 1.32, 8 => 1.41, 9 => 1.45, 10 => 1.49);
$ri = $ri_table[$n];
$cr = $ci / $ri;

echo "1.3 CONSISTENCY CHECK:\n";
printf("  λ_max = %.4f\n", $lambda_max);
printf("  CI = %.4f\n", $ci);
printf("  CR = %.4f (%.2f%%)\n", $cr, $cr * 100);
if ($cr <= 0.10) {
    echo "  Status: ✓ VALID (CR ≤ 10%)\n";
} else {
    echo "  Status: ✗ TIDAK VALID (CR > 10%)\n";
}
echo "\n";

// ================================================================
// BAGIAN 2: NORMALISASI DECISION MATRIX (RAW DATA)
// ================================================================
echo "═══════════════════════════════════════════════════════════════════════════════\n";
echo "BAGIAN 2: NORMALISASI DECISION MATRIX\n";
echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

echo "2.1 DECISION MATRIX (RAW DATA):\n";
printf("%-20s", "Alternatif");
for ($j = 0; $j < count($criteria_names); $j++) {
    printf("%-15s", "K" . ($j+1));
}
echo "\n" . str_repeat("─", 110) . "\n";

for ($i = 0; $i < count($alternatives); $i++) {
    printf("%-20s", $alternatives[$i]);
    for ($j = 0; $j < count($decision_matrix_raw[$i]); $j++) {
        printf("%-15s", number_format($decision_matrix_raw[$i][$j], 2));
    }
    echo "\n";
}
echo "\n";

// Normalize decision matrix
echo "2.2 NORMALISASI: r_ij = x_ij / √(Σ x_ij²)\n\n";

$norm_denom = array();
for ($j = 0; $j < count($decision_matrix_raw[0]); $j++) {
    $sum_squares = 0;
    for ($i = 0; $i < count($decision_matrix_raw); $i++) {
        $sum_squares += $decision_matrix_raw[$i][$j] * $decision_matrix_raw[$i][$j];
    }
    $norm_denom[$j] = sqrt($sum_squares);
}

echo "Penyebut normalisasi (√(Σ x²)):\n";
for ($j = 0; $j < count($norm_denom); $j++) {
    printf("  K%d: %.4f\n", ($j+1), $norm_denom[$j]);
}
echo "\n";

// Normalized matrix
$normalized = array();
printf("%-20s", "Alternatif");
for ($j = 0; $j < count($criteria_names); $j++) {
    printf("%-15s", "K" . ($j+1));
}
echo "\n" . str_repeat("─", 110) . "\n";

for ($i = 0; $i < count($decision_matrix_raw); $i++) {
    printf("%-20s", $alternatives[$i]);
    $normalized[$i] = array();
    for ($j = 0; $j < count($decision_matrix_raw[$i]); $j++) {
        $normalized[$i][$j] = $decision_matrix_raw[$i][$j] / $norm_denom[$j];
        printf("%-15.4f", $normalized[$i][$j]);
    }
    echo "\n";
}
echo "\n";

// ================================================================
// BAGIAN 3: WEIGHTED NORMALIZED MATRIX
// ================================================================
echo "═══════════════════════════════════════════════════════════════════════════════\n";
echo "BAGIAN 3: WEIGHTED NORMALIZED MATRIX (v_ij = w_j × r_ij)\n";
echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

$weighted = array();
printf("%-20s", "Alternatif");
for ($j = 0; $j < count($criteria_names); $j++) {
    printf("%-15s", "K" . ($j+1));
}
echo "\n" . str_repeat("─", 110) . "\n";

for ($i = 0; $i < count($normalized); $i++) {
    printf("%-20s", $alternatives[$i]);
    $weighted[$i] = array();
    for ($j = 0; $j < count($normalized[$i]); $j++) {
        $weighted[$i][$j] = $expert_weights[$j] * $normalized[$i][$j];
        printf("%-15.4f", $weighted[$i][$j]);
    }
    echo "\n";
}
echo "\n";

// ================================================================
// BAGIAN 4: IDEAL SOLUTIONS (A+ dan A-)
// ================================================================
echo "═══════════════════════════════════════════════════════════════════════════════\n";
echo "BAGIAN 4: IDEAL SOLUTIONS (A+ DAN A-)\n";
echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

$ideal_positive = array();  // A+
$ideal_negative = array();  // A-

echo "4.1 IDEAL POSITIVE SOLUTION (A+):\n";
for ($j = 0; $j < count($weighted[0]); $j++) {
    $values = array();
    for ($i = 0; $i < count($weighted); $i++) {
        $values[] = $weighted[$i][$j];
    }
    
    if ($criteria_type[$j] === 'benefit') {
        $ideal_positive[$j] = max($values);
    } else {
        $ideal_positive[$j] = min($values);
    }
    printf("  A+_%d (K%d, %s) = %.4f\n", ($j+1), ($j+1), $criteria_type[$j], $ideal_positive[$j]);
}
echo "\n";

echo "4.2 IDEAL NEGATIVE SOLUTION (A-):\n";
for ($j = 0; $j < count($weighted[0]); $j++) {
    $values = array();
    for ($i = 0; $i < count($weighted); $i++) {
        $values[] = $weighted[$i][$j];
    }
    
    if ($criteria_type[$j] === 'benefit') {
        $ideal_negative[$j] = min($values);
    } else {
        $ideal_negative[$j] = max($values);
    }
    printf("  A-_%d (K%d, %s) = %.4f\n", ($j+1), ($j+1), $criteria_type[$j], $ideal_negative[$j]);
}
echo "\n";

// ================================================================
// BAGIAN 5: EUCLIDEAN DISTANCES
// ================================================================
echo "═══════════════════════════════════════════════════════════════════════════════\n";
echo "BAGIAN 5: EUCLIDEAN DISTANCES (D+ DAN D-)\n";
echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

$distances_positive = array();  // D+
$distances_negative = array();  // D-

for ($i = 0; $i < count($weighted); $i++) {
    $sum_plus = 0;
    $sum_minus = 0;
    
    for ($j = 0; $j < count($weighted[$i]); $j++) {
        $sum_plus += pow($weighted[$i][$j] - $ideal_positive[$j], 2);
        $sum_minus += pow($weighted[$i][$j] - $ideal_negative[$j], 2);
    }
    
    $distances_positive[$i] = sqrt($sum_plus);
    $distances_negative[$i] = sqrt($sum_minus);
}

printf("%-20s %-20s %-20s\n", "Alternatif", "D+ (ideal+)", "D- (ideal-)");
echo str_repeat("─", 80) . "\n";
for ($i = 0; $i < count($alternatives); $i++) {
    printf("%-20s %-20.6f %-20.6f\n", $alternatives[$i], $distances_positive[$i], $distances_negative[$i]);
}
echo "\n";

// ================================================================
// BAGIAN 6: PREFERENCE SCORES
// ================================================================
echo "═══════════════════════════════════════════════════════════════════════════════\n";
echo "BAGIAN 6: PREFERENCE SCORES (C_i = D- / (D+ + D-))\n";
echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

$preference_scores = array();
printf("%-20s %-20s %-15s\n", "Alternatif", "D-/(D++D-)", "Score");
echo str_repeat("─", 85) . "\n";

for ($i = 0; $i < count($alternatives); $i++) {
    $denominator = $distances_positive[$i] + $distances_negative[$i];
    $preference_scores[$i] = $distances_negative[$i] / $denominator;
    
    printf("%-20s %-20s %-15.4f\n", 
        $alternatives[$i],
        number_format($distances_negative[$i], 4) . " / " . number_format($denominator, 4),
        $preference_scores[$i]
    );
}
echo "\n";

// ================================================================
// BAGIAN 7: RANKING AKHIR
// ================================================================
echo "═══════════════════════════════════════════════════════════════════════════════\n";
echo "BAGIAN 7: RANKING AKHIR\n";
echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

// Buat array untuk ranking
$ranking = array();
for ($i = 0; $i < count($alternatives); $i++) {
    $ranking[] = array(
        'rank' => 0,
        'alternative' => $alternatives[$i],
        'score' => $preference_scores[$i],
        'D+' => $distances_positive[$i],
        'D-' => $distances_negative[$i]
    );
}

// Sort by score descending
usort($ranking, function($a, $b) {
    return $b['score'] <=> $a['score'];
});

// Assign rank
for ($i = 0; $i < count($ranking); $i++) {
    $ranking[$i]['rank'] = $i + 1;
}

echo "URUTAN RANKING (TERBAIK KE TERBURUK):\n";
echo str_repeat("─", 80) . "\n";
printf("%-6s %-20s %-15s %-18s %-18s\n", "Rank", "Alternatif", "Score", "D+", "D-");
echo str_repeat("─", 80) . "\n";

for ($i = 0; $i < count($ranking); $i++) {
    printf("%-6d %-20s %-15.4f %-18.6f %-18.6f\n",
        $ranking[$i]['rank'],
        $ranking[$i]['alternative'],
        $ranking[$i]['score'],
        $ranking[$i]['D+'],
        $ranking[$i]['D-']
    );
}
echo "\n";

// ================================================================
// RINGKASAN FINAL
// ================================================================
echo "╔═══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                         RINGKASAN HASIL AKHIR                                ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════════════╝\n\n";

echo "AHP EXPERT WEIGHTS:\n";
echo str_repeat("─", 80) . "\n";
for ($i = 0; $i < count($expert_weights); $i++) {
    printf("%-25s = %8.4f  (%6.2f%%)\n", "w_" . ($i+1) . " " . $criteria_names[$i], $expert_weights[$i], $expert_weights[$i] * 100);
}
echo "\n";

echo "AHP CONSISTENCY:\n";
echo str_repeat("─", 80) . "\n";
printf("%-30s = %.4f\n", "λ_max (Lambda Max)", $lambda_max);
printf("%-30s = %.4f\n", "CI (Consistency Index)", $ci);
printf("%-30s = %.4f  (%.2f%%)\n", "CR (Consistency Ratio)", $cr, $cr * 100);
printf("%-30s %s\n", "STATUS", ($cr <= 0.10 ? "✓ VALID" : "✗ TIDAK VALID"));
echo "\n";

echo "TOPSIS RANKING FINAL:\n";
echo str_repeat("─", 80) . "\n";
for ($i = 0; $i < count($ranking); $i++) {
    printf("%-6d %-25s Score: %.4f\n", $ranking[$i]['rank'], $ranking[$i]['alternative'], $ranking[$i]['score']);
}
echo "\n";

echo "╔═══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║  Perhitungan AHP + TOPSIS selesai! Bandingkan dengan perhitungan manual Anda. ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════════════╝\n\n";
?>
