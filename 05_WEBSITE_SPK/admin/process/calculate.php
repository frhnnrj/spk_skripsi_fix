<?php
/**
 * Admin Process: Calculate AHP & TOPSIS
 * File: admin/process/calculate.php
 */

require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('❌ Invalid request');
}

$pakar_id = intval($_POST['pakar_id'] ?? 0);

if (!$pakar_id) {
    die('❌ Pakar ID tidak valid');
}

// Get data
$pairwise = get_pairwise_matrix($pakar_id);
$decision = get_decision_matrix($pakar_id);
$alternatives = get_all_alternatives();

if (!$pairwise || count($decision) < 5) {
    die('❌ Data belum lengkap');
}

// ============================================================================
// STEP 1: BUILD PAIRWISE COMPARISON MATRIX
// ============================================================================

$pairwise_matrix = [
    [1, $pairwise['k1_vs_k2'], $pairwise['k1_vs_k3'], $pairwise['k1_vs_k4'], $pairwise['k1_vs_k5'], $pairwise['k1_vs_k6']],
    [1/$pairwise['k1_vs_k2'], 1, $pairwise['k2_vs_k3'], $pairwise['k2_vs_k4'], $pairwise['k2_vs_k5'], $pairwise['k2_vs_k6']],
    [1/$pairwise['k1_vs_k3'], 1/$pairwise['k2_vs_k3'], 1, $pairwise['k3_vs_k4'], $pairwise['k3_vs_k5'], $pairwise['k3_vs_k6']],
    [1/$pairwise['k1_vs_k4'], 1/$pairwise['k2_vs_k4'], 1/$pairwise['k3_vs_k4'], 1, $pairwise['k4_vs_k5'], $pairwise['k4_vs_k6']],
    [1/$pairwise['k1_vs_k5'], 1/$pairwise['k2_vs_k5'], 1/$pairwise['k3_vs_k5'], 1/$pairwise['k4_vs_k5'], 1, $pairwise['k5_vs_k6']],
    [1/$pairwise['k1_vs_k6'], 1/$pairwise['k2_vs_k6'], 1/$pairwise['k3_vs_k6'], 1/$pairwise['k4_vs_k6'], 1/$pairwise['k5_vs_k6'], 1],
];

// ============================================================================
// STEP 2: NORMALIZE PAIRWISE MATRIX (Column Sums)
// ============================================================================

$column_sums = array_fill(0, 6, 0);
for ($i = 0; $i < 6; $i++) {
    for ($j = 0; $j < 6; $j++) {
        $column_sums[$j] += $pairwise_matrix[$i][$j];
    }
}

$normalized_matrix = [];
for ($i = 0; $i < 6; $i++) {
    for ($j = 0; $j < 6; $j++) {
        $normalized_matrix[$i][$j] = $pairwise_matrix[$i][$j] / $column_sums[$j];
    }
}

// ============================================================================
// STEP 3: CALCULATE WEIGHTS (Row Averages)
// ============================================================================

$weights = [];
for ($i = 0; $i < 6; $i++) {
    $row_sum = 0;
    for ($j = 0; $j < 6; $j++) {
        $row_sum += $normalized_matrix[$i][$j];
    }
    $weights[$i] = $row_sum / 6; // w = rata-rata baris
}

// ============================================================================
// STEP 4: CALCULATE CONSISTENCY
// ============================================================================

// Weighted sum vector
$ws_vector = [];
for ($i = 0; $i < 6; $i++) {
    $ws = 0;
    for ($j = 0; $j < 6; $j++) {
        $ws += $pairwise_matrix[$i][$j] * $weights[$j];
    }
    $ws_vector[$i] = $ws;
}

// Lambda Max
$lambda_parts = [];
for ($i = 0; $i < 6; $i++) {
    $lambda_parts[$i] = $ws_vector[$i] / $weights[$i];
}
$lambda_max = array_sum($lambda_parts) / 6;

// Consistency Index
$n = 6;
$ri_values = [0, 0, 0.58, 0.90, 1.12, 1.24, 1.32, 1.41]; // RI for n=1 to 8
$ci = ($lambda_max - $n) / ($n - 1);
$cr = $ci / $ri_values[$n - 1]; // Array index is n-1 (0-indexed)
$is_consistent = $cr < 0.10 ? 1 : 0;

// ============================================================================
// STEP 5: BUILD DECISION MATRIX & NORMALIZE
// ============================================================================

$decision_matrix_full = [];
$decision_by_alt = [];
foreach ($decision as $d) {
    $decision_by_alt[$d['alternatif_id']] = $d;
}

for ($alt = 1; $alt <= 5; $alt++) {
    if (!isset($decision_by_alt[$alt])) {
        die("❌ Data tidak lengkap untuk alternatif $alt");
    }
    $d = $decision_by_alt[$alt];
    $decision_matrix_full[$alt] = [
        $d['k1_return'],
        $d['k2_risk'],
        $d['k3_liquidity'],
        $d['k4_capital'],
        $d['k5_income'],
        $d['k6_access']
    ];
}

// Normalize decision matrix
$criteria_types = ['benefit', 'cost', 'benefit', 'cost', 'benefit', 'benefit'];
$normalized_decision = [];

for ($k = 0; $k < 6; $k++) {
    $sum_sq = 0;
    for ($alt = 1; $alt <= 5; $alt++) {
        $sum_sq += pow($decision_matrix_full[$alt][$k], 2);
    }
    $sqrt_sum = sqrt($sum_sq);
    
    for ($alt = 1; $alt <= 5; $alt++) {
        $normalized_decision[$alt][$k] = $decision_matrix_full[$alt][$k] / $sqrt_sum;
    }
}

// Weighted normalized decision
$weighted_decision = [];
for ($alt = 1; $alt <= 5; $alt++) {
    for ($k = 0; $k < 6; $k++) {
        $weighted_decision[$alt][$k] = $weights[$k] * $normalized_decision[$alt][$k];
    }
}

// ============================================================================
// STEP 6: CALCULATE IDEAL SOLUTIONS
// ============================================================================

$ideal_positive = [];
$ideal_negative = [];

for ($k = 0; $k < 6; $k++) {
    $values = [];
    for ($alt = 1; $alt <= 5; $alt++) {
        $values[] = $weighted_decision[$alt][$k];
    }
    
    if ($criteria_types[$k] === 'benefit') {
        $ideal_positive[$k] = max($values);
        $ideal_negative[$k] = min($values);
    } else {
        $ideal_positive[$k] = min($values);
        $ideal_negative[$k] = max($values);
    }
}

// ============================================================================
// STEP 7: CALCULATE DISTANCES & PREFERENCE SCORES
// ============================================================================

$preferences = [];
for ($alt = 1; $alt <= 5; $alt++) {
    $d_plus = 0;
    $d_minus = 0;
    
    for ($k = 0; $k < 6; $k++) {
        $d_plus += pow($weighted_decision[$alt][$k] - $ideal_positive[$k], 2);
        $d_minus += pow($weighted_decision[$alt][$k] - $ideal_negative[$k], 2);
    }
    
    $d_plus = sqrt($d_plus);
    $d_minus = sqrt($d_minus);
    
    $preference = $d_minus / ($d_plus + $d_minus);
    $preferences[$alt] = $preference;
}

// ============================================================================
// STEP 8: CALCULATE RANKINGS
// ============================================================================

arsort($preferences);
$rankings = [];
$rank = 1;
foreach ($preferences as $alt => $pref) {
    $rankings[$alt] = ['preference' => $preferences[$alt], 'rank' => $rank];
    $rank++;
}

// ============================================================================
// STEP 9: SAVE RESULTS TO DATABASE
// ============================================================================

// Save AHP Results
$ahp_exists = get_ahp_results($pakar_id);
if ($ahp_exists) {
    $query = "UPDATE tbl_ahp_results SET w_k1=?, w_k2=?, w_k3=?, w_k4=?, w_k5=?, w_k6=?, lambda_max=?, ci=?, cr=?, is_consistent=? WHERE pakar_id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("dddddddddii", $weights[0], $weights[1], $weights[2], $weights[3], $weights[4], $weights[5], $lambda_max, $ci, $cr, $is_consistent, $pakar_id);
} else {
    $query = "INSERT INTO tbl_ahp_results (pakar_id, w_k1, w_k2, w_k3, w_k4, w_k5, w_k6, lambda_max, ci, cr, is_consistent) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iddddddddii", $pakar_id, $weights[0], $weights[1], $weights[2], $weights[3], $weights[4], $weights[5], $lambda_max, $ci, $cr, $is_consistent);
}
$stmt->execute();
$stmt->close();

// Save TOPSIS Results
$alt_names = [1 => 'kripto', 2 => 'saham', 3 => 'sbn', 4 => 'reksadana', 5 => 'emasdigital'];
$topsis_exists = get_topsis_results($pakar_id);

if ($topsis_exists) {
    $update_cols = [];
    $update_vals = [];
    foreach ([1, 2, 3, 4, 5] as $alt) {
        $name = $alt_names[$alt];
        $update_cols[] = "${name}_preference=?, ${name}_rank=?";
        $update_vals[] = $rankings[$alt]['preference'];
        $update_vals[] = $rankings[$alt]['rank'];
    }
    $update_vals[] = $pakar_id;
    
    $query = "UPDATE tbl_topsis_results SET " . implode(", ", $update_cols) . " WHERE pakar_id=?";
    $stmt = $conn->prepare($query);
    $types = str_repeat('di', 5) . 'i';
    $stmt->bind_param($types, ...$update_vals);
} else {
    $cols = [];
    $placeholders = [];
    $vals = [$pakar_id];
    $types = 'i';
    
    foreach ([1, 2, 3, 4, 5] as $alt) {
        $name = $alt_names[$alt];
        $cols[] = "${name}_preference";
        $cols[] = "${name}_rank";
        $placeholders[] = '?';
        $placeholders[] = '?';
        $vals[] = $rankings[$alt]['preference'];
        $vals[] = $rankings[$alt]['rank'];
        $types .= 'di';
    }
    
    $query = "INSERT INTO tbl_topsis_results (pakar_id, " . implode(", ", $cols) . ") VALUES (?, " . implode(", ", $placeholders) . ")";
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$vals);
}
$stmt->execute();
$stmt->close();

// Log
$top_alt = $alt_names[array_key_first($rankings)];
audit_log($pakar_id, 'CALCULATE_AHP_TOPSIS', "Calculated: CR=$cr, Top1=$top_alt");

echo "✅ Kalkulasi berhasil!";
?>
