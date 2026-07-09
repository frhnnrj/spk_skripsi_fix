<?php
/**
 * PERHITUNGAN AHP (ANALYTIC HIERARCHY PROCESS)
 * 
 * File: 01_perhitungan_ahp.php
 * Tujuan: Menampilkan semua tahapan perhitungan AHP secara detail dan step-by-step
 * 
 * Output yang ditampilkan:
 * 1. Pairwise Comparison Matrix (Input)
 * 2. Column Sums (Jumlah Kolom)
 * 3. Normalized Matrix (Matrix Ternormalisasi)
 * 4. Eigenvector/Weights (Bobot Prioritas)
 * 5. Lambda Max (Eigenvalue Terbesar)
 * 6. Consistency Index (CI)
 * 7. Consistency Ratio (CR)
 * 8. Validasi Consistency
 */

// ================================================================
// INPUT DATA - PAKAR 2 (RISK-AVERSE)
// ================================================================
$pairwise = array(
    array(1,     3,   1,     2,     3,     4),
    array(1/3,     1,     1/2,     2,     1/2,     2),
    array(1,   2,   1,     3,     1/2,   2),
    array(1/2,   1/2,   1/3,     1,     1/2,   3),
    array(1/3,     2,   2,     2,     1,     3),
    array(1/4,   1/2,   1/2,   1/3,   1/3,   1)
);

$criteria = array('K1: Return', 'K2: Risk', 'K3: Liquidity', 'K4: Capital', 'K5: Income', 'K6: Access');

// ================================================================
// HEADER
// ================================================================
echo "\n";
echo "╔═══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                 PERHITUNGAN AHP (STEP BY STEP)                               ║\n";
echo "║        Analytic Hierarchy Process - Bobot Prioritas Kriteria                  ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

// ================================================================
// STEP 1: Pairwise Comparison Matrix
// ================================================================
echo "STEP 1: PAIRWISE COMPARISON MATRIX\n";
echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

echo "Matriks perbandingan berpasangan antara kriteria:\n\n";
printf("%-15s", "");
for ($i = 0; $i < count($criteria); $i++) {
    printf("%-12s", "K" . ($i+1));
}
echo "\n" . str_repeat("─", 95) . "\n";

for ($i = 0; $i < count($pairwise); $i++) {
    printf("%-15s", "K" . ($i+1));
    for ($j = 0; $j < count($pairwise[$i]); $j++) {
        printf("%-12.4f", $pairwise[$i][$j]);
    }
    echo "\n";
}
echo "\n";

// ================================================================
// STEP 2: Column Sums
// ================================================================
echo "STEP 2: JUMLAH KOLOM (COLUMN SUMS)\n";
echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

$column_sums = array();
for ($j = 0; $j < count($pairwise[0]); $j++) {
    $sum = 0;
    for ($i = 0; $i < count($pairwise); $i++) {
        $sum += $pairwise[$i][$j];
    }
    $column_sums[$j] = $sum;
}

echo "Perhitungan:\n";
for ($j = 0; $j < count($column_sums); $j++) {
    $calc = "Sum K" . ($j+1) . " = ";
    for ($i = 0; $i < count($pairwise); $i++) {
        $calc .= number_format($pairwise[$i][$j], 4);
        if ($i < count($pairwise) - 1) $calc .= " + ";
    }
    echo "  " . $calc . " = " . number_format($column_sums[$j], 4) . "\n";
}
echo "\n";

// ================================================================
// STEP 3: Normalisasi Matrix
// ================================================================
echo "STEP 3: NORMALISASI MATRIX (r_ij = a_ij / Sum Kolom j)\n";
echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

$normalized = array();
printf("%-15s", "");
for ($i = 0; $i < count($criteria); $i++) {
    printf("%-12s", "K" . ($i+1));
}
echo "\n" . str_repeat("─", 95) . "\n";

for ($i = 0; $i < count($pairwise); $i++) {
    printf("%-15s", "K" . ($i+1));
    $normalized[$i] = array();
    for ($j = 0; $j < count($pairwise[$i]); $j++) {
        $normalized[$i][$j] = $pairwise[$i][$j] / $column_sums[$j];
        printf("%-12.4f", $normalized[$i][$j]);
    }
    echo "\n";
}
echo "\n";

// ================================================================
// STEP 4: Eigenvector (Weights/Bobot)
// ================================================================
echo "STEP 4: EIGENVECTOR / BOBOT PRIORITAS\n";
echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

$eigenvector = array();
echo "Perhitungan rata-rata setiap baris dari matrix ternormalisasi:\n\n";

for ($i = 0; $i < count($normalized); $i++) {
    $sum = 0;
    $calc_str = "w_" . ($i+1) . " = (";
    for ($j = 0; $j < count($normalized[$i]); $j++) {
        $sum += $normalized[$i][$j];
        $calc_str .= number_format($normalized[$i][$j], 4);
        if ($j < count($normalized[$i]) - 1) $calc_str .= " + ";
    }
    $eigenvector[$i] = $sum / count($normalized[$i]);
    $calc_str .= ") / " . count($normalized[$i]) . " = " . number_format($eigenvector[$i], 4);
    echo "  " . $calc_str . "\n";
}
echo "\n";

echo "Ringkasan Eigenvector (Bobot Prioritas):\n";
for ($i = 0; $i < count($eigenvector); $i++) {
    $pct = $eigenvector[$i] * 100;
    echo sprintf("  w_%d (%-20s) = %.4f  (%.2f%%)\n", ($i+1), "K" . ($i+1), $eigenvector[$i], $pct);
}
echo "\n";

// ================================================================
// STEP 5: Lambda Max
// ================================================================
echo "STEP 5: LAMBDA MAX (EIGENVALUE TERBESAR)\n";
echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

$lambda_values = array();
echo "Perhitungan λ_i untuk setiap baris:\n\n";
echo "Rumus: λ_i = (a_i1 * w_1 + a_i2 * w_2 + ... + a_in * w_n) / w_i\n\n";

for ($i = 0; $i < count($pairwise); $i++) {
    $sum_products = 0;
    for ($j = 0; $j < count($pairwise[$i]); $j++) {
        $sum_products += $pairwise[$i][$j] * $eigenvector[$j];
    }
    $lambda_values[$i] = $sum_products / $eigenvector[$i];
    
    echo "  λ_" . ($i+1) . " = (";
    for ($j = 0; $j < count($pairwise[$i]); $j++) {
        echo number_format($pairwise[$i][$j], 2) . "*" . number_format($eigenvector[$j], 4);
        if ($j < count($pairwise[$i]) - 1) echo " + ";
    }
    echo ") / " . number_format($eigenvector[$i], 4) . " = " . number_format($lambda_values[$i], 4) . "\n";
}
echo "\n";

$lambda_max = array_sum($lambda_values) / count($lambda_values);
echo "Lambda Max (Rata-rata λ_i):\n";
echo "  λ_max = (";
for ($i = 0; $i < count($lambda_values); $i++) {
    echo number_format($lambda_values[$i], 4);
    if ($i < count($lambda_values) - 1) echo " + ";
}
echo ") / " . count($lambda_values) . "\n";
echo "  λ_max = " . number_format(array_sum($lambda_values), 4) . " / " . count($lambda_values) . "\n";
echo "  λ_max = " . number_format($lambda_max, 4) . "\n\n";

// ================================================================
// STEP 6: Consistency Index (CI)
// ================================================================
echo "STEP 6: CONSISTENCY INDEX (CI)\n";
echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

$n = count($pairwise);
$ci = ($lambda_max - $n) / ($n - 1);

echo "Rumus: CI = (λ_max - n) / (n - 1)\n\n";
echo "  Dimana:\n";
echo "    λ_max = " . number_format($lambda_max, 4) . "\n";
echo "    n     = " . $n . "\n";
echo "    n - 1 = " . ($n - 1) . "\n\n";
echo "  CI = (" . number_format($lambda_max, 4) . " - " . $n . ") / (" . $n . " - 1)\n";
echo "  CI = " . number_format($lambda_max - $n, 4) . " / " . ($n - 1) . "\n";
echo "  CI = " . number_format($ci, 4) . "\n\n";

// ================================================================
// STEP 7: Consistency Ratio (CR)
// ================================================================
echo "STEP 7: CONSISTENCY RATIO (CR)\n";
echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

$ri_table = array(
    1 => 0.00, 2 => 0.00, 3 => 0.58, 4 => 0.90, 5 => 1.12,
    6 => 1.24, 7 => 1.32, 8 => 1.41, 9 => 1.45, 10 => 1.49
);

$ri = $ri_table[$n];

echo "Tabel Random Index (RI):\n\n";
echo "  n  │  RI\n";
echo "  ───┼─────\n";
for ($i = 1; $i <= 10; $i++) {
    echo sprintf("  %2d │ %.2f\n", $i, $ri_table[$i]);
}
echo "\n";

echo "Untuk n = " . $n . ", RI = " . $ri . "\n\n";

$cr = $ci / $ri;
echo "Rumus: CR = CI / RI\n\n";
echo "  CR = " . number_format($ci, 4) . " / " . $ri . "\n";
echo "  CR = " . number_format($cr, 4) . "\n";
echo "  CR = " . number_format($cr * 100, 2) . "%\n\n";

// ================================================================
// STEP 8: Validasi Consistency
// ================================================================
echo "STEP 8: VALIDASI CONSISTENCY\n";
echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

$threshold = 0.10;
$is_valid = $cr <= $threshold;
$status = $is_valid ? "✓ VALID" : "✗ TIDAK VALID";

echo "Kriteria Konsistensi: CR ≤ 10% (0.10)\n\n";
echo "  CR = " . number_format($cr * 100, 2) . "%\n";
echo "  Threshold = " . ($threshold * 100) . "%\n";
echo "  Status: " . $status . "\n\n";

if ($is_valid) {
    echo "  ✓ Matriks pairwise comparison KONSISTEN\n";
    echo "  ✓ Bobot yang dihasilkan dapat DITERIMA\n";
} else {
    echo "  ✗ Matriks pairwise comparison TIDAK KONSISTEN\n";
    echo "  ✗ Perlu dilakukan perbaikan pada pairwise comparison\n";
}
echo "\n";

// ================================================================
// RINGKASAN HASIL
// ================================================================
echo "╔═══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                         RINGKASAN HASIL AKHIR                                ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════════════╝\n\n";

echo "BOBOT PRIORITAS (EIGENVECTOR):\n";
echo str_repeat("─", 80) . "\n";
for ($i = 0; $i < count($eigenvector); $i++) {
    printf("%-25s = %8.4f  (%6.2f%%)\n", "w_" . ($i+1) . " " . $criteria[$i], $eigenvector[$i], $eigenvector[$i] * 100);
}
echo "\n";

echo "KONSISTENSI:\n";
echo str_repeat("─", 80) . "\n";
printf("%-30s = %.4f\n", "λ_max (Lambda Max)", $lambda_max);
printf("%-30s = %.4f\n", "CI (Consistency Index)", $ci);
printf("%-30s = %.4f\n", "RI (Random Index n=" . $n . ")", $ri);
printf("%-30s = %.4f  (%.2f%%)\n", "CR (Consistency Ratio)", $cr, $cr * 100);
printf("%-30s %s\n", "STATUS", $status);
echo "\n";

echo "╔═══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║  Perhitungan AHP selesai! Bandingkan dengan perhitungan manual Anda.          ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════════════╝\n\n";
?>
