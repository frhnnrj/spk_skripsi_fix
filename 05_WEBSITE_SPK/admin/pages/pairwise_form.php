<?php
require_once '../../config/database.php';

$pakar = get_all_pakar();
if (count($pakar) === 0) {
    redirect('../index.php', 'Belum ada pakar. Tambahkan pakar terlebih dahulu.', 'warning');
}

$pakar_id = $pakar[0]['pakar_id'];
$pakar_nama = $pakar[0]['pakar_nama'];
$pairwise = get_pairwise_matrix($pakar_id);

// Saaty's 9-point scale explanation
$saaty_scale = [
    1 => 'Sama penting / Equal',
    3 => 'Sedikit lebih penting / Slightly more important',
    5 => 'Jelas lebih penting / Clearly more important',
    7 => 'Jauh lebih penting / Far more important',
    9 => 'Mutlak lebih penting / Absolutely more important',
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Extract values from form
    $k1_vs_k2 = floatval($_POST['k1_vs_k2'] ?? 1);
    $k1_vs_k3 = floatval($_POST['k1_vs_k3'] ?? 1);
    $k1_vs_k4 = floatval($_POST['k1_vs_k4'] ?? 1);
    $k1_vs_k5 = floatval($_POST['k1_vs_k5'] ?? 1);
    $k1_vs_k6 = floatval($_POST['k1_vs_k6'] ?? 1);
    $k2_vs_k3 = floatval($_POST['k2_vs_k3'] ?? 1);
    $k2_vs_k4 = floatval($_POST['k2_vs_k4'] ?? 1);
    $k2_vs_k5 = floatval($_POST['k2_vs_k5'] ?? 1);
    $k2_vs_k6 = floatval($_POST['k2_vs_k6'] ?? 1);
    $k3_vs_k4 = floatval($_POST['k3_vs_k4'] ?? 1);
    $k3_vs_k5 = floatval($_POST['k3_vs_k5'] ?? 1);
    $k3_vs_k6 = floatval($_POST['k3_vs_k6'] ?? 1);
    $k4_vs_k5 = floatval($_POST['k4_vs_k5'] ?? 1);
    $k4_vs_k6 = floatval($_POST['k4_vs_k6'] ?? 1);
    $k5_vs_k6 = floatval($_POST['k5_vs_k6'] ?? 1);

    if ($pairwise) {
        // Update - simple direct query
        $query = "UPDATE tbl_pairwise_matrix SET 
                  k1_vs_k2=$k1_vs_k2, k1_vs_k3=$k1_vs_k3, k1_vs_k4=$k1_vs_k4, k1_vs_k5=$k1_vs_k5, k1_vs_k6=$k1_vs_k6, 
                  k2_vs_k3=$k2_vs_k3, k2_vs_k4=$k2_vs_k4, k2_vs_k5=$k2_vs_k5, k2_vs_k6=$k2_vs_k6, 
                  k3_vs_k4=$k3_vs_k4, k3_vs_k5=$k3_vs_k5, k3_vs_k6=$k3_vs_k6, 
                  k4_vs_k5=$k4_vs_k5, k4_vs_k6=$k4_vs_k6, k5_vs_k6=$k5_vs_k6, status='complete' 
                  WHERE pakar_id = $pakar_id";
        
        if ($conn->query($query)) {
            audit_log($pakar_id, 'UPDATE_PAIRWISE', 'Updated pairwise matrix');
            $_SESSION['message'] = '✅ Pairwise matrix berhasil disimpan!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = '❌ Error: ' . $conn->error;
            $_SESSION['message_type'] = 'danger';
        }
    } else {
        // Insert - simple direct query
        $query = "INSERT INTO tbl_pairwise_matrix 
                  (pakar_id, k1_vs_k2, k1_vs_k3, k1_vs_k4, k1_vs_k5, k1_vs_k6,
                   k2_vs_k3, k2_vs_k4, k2_vs_k5, k2_vs_k6,
                   k3_vs_k4, k3_vs_k5, k3_vs_k6,
                   k4_vs_k5, k4_vs_k6, k5_vs_k6, status) 
                  VALUES ($pakar_id, $k1_vs_k2, $k1_vs_k3, $k1_vs_k4, $k1_vs_k5, $k1_vs_k6,
                  $k2_vs_k3, $k2_vs_k4, $k2_vs_k5, $k2_vs_k6,
                  $k3_vs_k4, $k3_vs_k5, $k3_vs_k6,
                  $k4_vs_k5, $k4_vs_k6, $k5_vs_k6, 'complete')";
        
        if ($conn->query($query)) {
            audit_log($pakar_id, 'CREATE_PAIRWISE', 'Created pairwise matrix');
            $_SESSION['message'] = '✅ Pairwise matrix berhasil dibuat!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = '❌ Error: ' . $conn->error;
            $_SESSION['message_type'] = 'danger';
        }
    }
    
    // Refresh pairwise data
    $pairwise = get_pairwise_matrix($pakar_id);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pairwise Matrix - SPK AHP-TOPSIS</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .pairwise-group { background: var(--light); padding: 1.5rem; border-radius: 6px; margin-bottom: 1rem; border-left: 4px solid var(--primary); }
        .pairwise-label { font-weight: 600; margin-bottom: 0.75rem; color: var(--text); }
        .scale-info { font-size: 0.85rem; color: var(--gray); margin-top: 0.5rem; }
    </style>
</head>
<body>
    <div class="main-container">
        <header>
            <div class="container">
                <h1>🔐 Panel Admin</h1>
                <p>Input Pairwise Matrix (15 Perbandingan)</p>
            </div>
        </header>

        <nav>
            <ul>
                <li><a href="../index.php">🏠 Dashboard</a></li>
                <li><a href="pakar_form.php">➕ Pakar</a></li>
                <li><a href="pairwise_form.php" class="active">📊 Pairwise Matrix</a></li>
                <li><a href="decision_form.php">📋 Decision Matrix</a></li>
                <li><a href="results.php">📈 Hasil Kalkulasi</a></li>
                <li style="margin-left: auto;"><a href="../../">🏠 Home</a></li>
            </ul>
        </nav>

        <main>
            <div class="container">
                <h2>📊 Input Pairwise Matrix</h2>
                <p style="color: var(--gray);">Pakar: <strong><?php echo htmlspecialchars($pakar_nama); ?></strong></p>

                <?php if (!empty($_SESSION['message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                        <?php 
                        echo $_SESSION['message'];
                        unset($_SESSION['message']);
                        unset($_SESSION['message_type']);
                        ?>
                    </div>
                <?php endif; ?>

                <div class="box">
                    <h3>ℹ️ Petunjuk Penggunaan</h3>
                    <p>Gunakan skala Saaty (1-9) untuk membandingkan tingkat kepentingan antar kriteria:</p>
                    <table>
                        <thead>
                            <tr>
                                <th>Nilai</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($saaty_scale as $val => $desc): ?>
                            <tr>
                                <td><strong><?php echo $val; ?></strong></td>
                                <td><?php echo $desc; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Consistency Guide -->
                <div class="card" style="background: linear-gradient(135deg, rgba(59,130,246,0.1), rgba(139,92,246,0.1)); border-left: 4px solid var(--primary);">
                    <h3>🎯 Panduan Consistency Check</h3>
                    
                    <div style="margin-top: 1rem;">
                        <h4 style="color: var(--primary); margin-bottom: 0.5rem;">❓ Apa itu Consistency Ratio?</h4>
                        <p>Consistency Ratio (CR) mengukur seberapa logis/konsisten penilaian Anda dalam membandingkan kriteria. Nilai CR harus <strong>di bawah 10%</strong> untuk hasil yang valid.</p>
                    </div>

                    <div style="margin-top: 1.5rem;">
                        <h4 style="color: var(--primary); margin-bottom: 0.5rem;">✅ Cara Membuat Judgment yang Konsisten:</h4>
                        <ol style="margin-left: 2rem; line-height: 1.8;">
                            <li><strong>Tentukan Ranking Prioritas Dulu</strong><br>
                                Atur urutan kriteria dari paling penting ke kurang penting. Contoh: K1 > K2 > K3 > K4 > K5 > K6
                            </li>
                            <li><strong>Gunakan Rentang Nilai yang Konsisten</strong><br>
                                Jika A jauh lebih penting dari B (nilai 5), dan B lebih penting dari C (nilai 3), maka A vs C harus lebih besar dari 5, misalnya 7.
                            </li>
                            <li><strong>Hindari Pertentangan Logika</strong><br>
                                ❌ SALAH: K1 vs K2 = 5 (K1 jelas lebih penting), K2 vs K3 = 5 (K2 jelas lebih penting), tapi K3 vs K1 = 3 (K3 lebih penting?)<br>
                                ✅ BENAR: Jika K1 > K2 dan K2 > K3, maka K1 harus jelas > K3
                            </li>
                        </ol>
                    </div>

                    <div style="margin-top: 1.5rem;">
                        <h4 style="color: var(--primary); margin-bottom: 0.5rem;">📊 Contoh Data yang Konsisten:</h4>
                        <table style="font-size: 0.9rem; margin-top: 0.5rem;">
                            <thead>
                                <tr style="background: rgba(59,130,246,0.2);">
                                    <th>Perbandingan</th>
                                    <th>Nilai</th>
                                    <th>Penjelasan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>K1 vs K2</td>
                                    <td><strong>3</strong></td>
                                    <td>Return sedikit lebih penting dari Risk</td>
                                </tr>
                                <tr style="background: var(--light);">
                                    <td>K1 vs K3</td>
                                    <td><strong>5</strong></td>
                                    <td>Return jelas lebih penting dari Liquidity (lebih besar dari K1 vs K2)</td>
                                </tr>
                                <tr>
                                    <td>K2 vs K3</td>
                                    <td><strong>2</strong></td>
                                    <td>Risk sedikit lebih penting dari Liquidity (lebih kecil dari K1 vs K3)</td>
                                </tr>
                                <tr style="background: var(--light);">
                                    <td>K3 vs K4</td>
                                    <td><strong>3</strong></td>
                                    <td>Liquidity sedikit lebih penting dari Capital</td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="text-align: center; padding: 1rem; color: var(--success); font-weight: 600;">
                                        Data seperti ini akan menghasilkan CR &lt; 10% ✅
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div style="margin-top: 1.5rem; padding: 1rem; background: rgba(251,191,36,0.1); border-left: 3px solid var(--warning); border-radius: 4px;">
                        <strong>💡 Tips Praktis:</strong>
                        <ul style="margin-left: 1.5rem; margin-top: 0.5rem;">
                            <li>Mulai dari perbandingan yang mudah (Anda paling yakin)</li>
                            <li>Gunakan nilai ganjil lebih sering (1, 3, 5, 7, 9) daripada nilai genap</li>
                            <li>Hindari menggunakan nilai ekstrem (9) kecuali Anda sangat yakin</li>
                            <li>Setelah input, lihat hasil CR di halaman "Hasil Kalkulasi"</li>
                            <li>Jika CR > 10%, kembali ke sini dan sesuaikan penilaian Anda</li>
                        </ul>
                    </div>
                </div>

                <form method="POST" action="">
                    <div class="card">
                        <h3 style="margin-bottom: 1.5rem;">K1 (Return) vs Kriteria Lain</h3>
                        
                        <div class="pairwise-group">
                            <div class="pairwise-label">K1 (Return) vs K2 (Risk)</div>
                            <input type="range" name="k1_vs_k2" min="1" max="9" value="<?php echo $pairwise ? $pairwise['k1_vs_k2'] : 1; ?>" 
                                   class="form-control" style="width: 100%; cursor: pointer;" onchange="document.getElementById('k1_vs_k2_val').textContent = this.value;">
                            <p style="margin-top: 0.5rem; font-size: 1.1rem;">Nilai: <strong id="k1_vs_k2_val"><?php echo $pairwise ? $pairwise['k1_vs_k2'] : 1; ?></strong></p>
                        </div>

                        <div class="pairwise-group">
                            <div class="pairwise-label">K1 (Return) vs K3 (Liquidity)</div>
                            <input type="range" name="k1_vs_k3" min="1" max="9" value="<?php echo $pairwise ? $pairwise['k1_vs_k3'] : 1; ?>" 
                                   class="form-control" style="width: 100%; cursor: pointer;" onchange="document.getElementById('k1_vs_k3_val').textContent = this.value;">
                            <p style="margin-top: 0.5rem; font-size: 1.1rem;">Nilai: <strong id="k1_vs_k3_val"><?php echo $pairwise ? $pairwise['k1_vs_k3'] : 1; ?></strong></p>
                        </div>

                        <div class="pairwise-group">
                            <div class="pairwise-label">K1 (Return) vs K4 (Capital)</div>
                            <input type="range" name="k1_vs_k4" min="1" max="9" value="<?php echo $pairwise ? $pairwise['k1_vs_k4'] : 1; ?>" 
                                   class="form-control" style="width: 100%; cursor: pointer;" onchange="document.getElementById('k1_vs_k4_val').textContent = this.value;">
                            <p style="margin-top: 0.5rem; font-size: 1.1rem;">Nilai: <strong id="k1_vs_k4_val"><?php echo $pairwise ? $pairwise['k1_vs_k4'] : 1; ?></strong></p>
                        </div>

                        <div class="pairwise-group">
                            <div class="pairwise-label">K1 (Return) vs K5 (Income)</div>
                            <input type="range" name="k1_vs_k5" min="1" max="9" value="<?php echo $pairwise ? $pairwise['k1_vs_k5'] : 1; ?>" 
                                   class="form-control" style="width: 100%; cursor: pointer;" onchange="document.getElementById('k1_vs_k5_val').textContent = this.value;">
                            <p style="margin-top: 0.5rem; font-size: 1.1rem;">Nilai: <strong id="k1_vs_k5_val"><?php echo $pairwise ? $pairwise['k1_vs_k5'] : 1; ?></strong></p>
                        </div>

                        <div class="pairwise-group">
                            <div class="pairwise-label">K1 (Return) vs K6 (Access)</div>
                            <input type="range" name="k1_vs_k6" min="1" max="9" value="<?php echo $pairwise ? $pairwise['k1_vs_k6'] : 1; ?>" 
                                   class="form-control" style="width: 100%; cursor: pointer;" onchange="document.getElementById('k1_vs_k6_val').textContent = this.value;">
                            <p style="margin-top: 0.5rem; font-size: 1.1rem;">Nilai: <strong id="k1_vs_k6_val"><?php echo $pairwise ? $pairwise['k1_vs_k6'] : 1; ?></strong></p>
                        </div>
                    </div>

                    <div class="card">
                        <h3 style="margin-bottom: 1.5rem;">K2 (Risk) vs Kriteria Lain</h3>
                        
                        <div class="pairwise-group">
                            <div class="pairwise-label">K2 (Risk) vs K3 (Liquidity)</div>
                            <input type="range" name="k2_vs_k3" min="1" max="9" value="<?php echo $pairwise ? $pairwise['k2_vs_k3'] : 1; ?>" 
                                   class="form-control" style="width: 100%; cursor: pointer;" onchange="document.getElementById('k2_vs_k3_val').textContent = this.value;">
                            <p style="margin-top: 0.5rem; font-size: 1.1rem;">Nilai: <strong id="k2_vs_k3_val"><?php echo $pairwise ? $pairwise['k2_vs_k3'] : 1; ?></strong></p>
                        </div>

                        <div class="pairwise-group">
                            <div class="pairwise-label">K2 (Risk) vs K4 (Capital)</div>
                            <input type="range" name="k2_vs_k4" min="1" max="9" value="<?php echo $pairwise ? $pairwise['k2_vs_k4'] : 1; ?>" 
                                   class="form-control" style="width: 100%; cursor: pointer;" onchange="document.getElementById('k2_vs_k4_val').textContent = this.value;">
                            <p style="margin-top: 0.5rem; font-size: 1.1rem;">Nilai: <strong id="k2_vs_k4_val"><?php echo $pairwise ? $pairwise['k2_vs_k4'] : 1; ?></strong></p>
                        </div>

                        <div class="pairwise-group">
                            <div class="pairwise-label">K2 (Risk) vs K5 (Income)</div>
                            <input type="range" name="k2_vs_k5" min="1" max="9" value="<?php echo $pairwise ? $pairwise['k2_vs_k5'] : 1; ?>" 
                                   class="form-control" style="width: 100%; cursor: pointer;" onchange="document.getElementById('k2_vs_k5_val').textContent = this.value;">
                            <p style="margin-top: 0.5rem; font-size: 1.1rem;">Nilai: <strong id="k2_vs_k5_val"><?php echo $pairwise ? $pairwise['k2_vs_k5'] : 1; ?></strong></p>
                        </div>

                        <div class="pairwise-group">
                            <div class="pairwise-label">K2 (Risk) vs K6 (Access)</div>
                            <input type="range" name="k2_vs_k6" min="1" max="9" value="<?php echo $pairwise ? $pairwise['k2_vs_k6'] : 1; ?>" 
                                   class="form-control" style="width: 100%; cursor: pointer;" onchange="document.getElementById('k2_vs_k6_val').textContent = this.value;">
                            <p style="margin-top: 0.5rem; font-size: 1.1rem;">Nilai: <strong id="k2_vs_k6_val"><?php echo $pairwise ? $pairwise['k2_vs_k6'] : 1; ?></strong></p>
                        </div>
                    </div>

                    <div class="card">
                        <h3 style="margin-bottom: 1.5rem;">K3 (Liquidity) vs Kriteria Lain</h3>
                        
                        <div class="pairwise-group">
                            <div class="pairwise-label">K3 (Liquidity) vs K4 (Capital)</div>
                            <input type="range" name="k3_vs_k4" min="1" max="9" value="<?php echo $pairwise ? $pairwise['k3_vs_k4'] : 1; ?>" 
                                   class="form-control" style="width: 100%; cursor: pointer;" onchange="document.getElementById('k3_vs_k4_val').textContent = this.value;">
                            <p style="margin-top: 0.5rem; font-size: 1.1rem;">Nilai: <strong id="k3_vs_k4_val"><?php echo $pairwise ? $pairwise['k3_vs_k4'] : 1; ?></strong></p>
                        </div>

                        <div class="pairwise-group">
                            <div class="pairwise-label">K3 (Liquidity) vs K5 (Income)</div>
                            <input type="range" name="k3_vs_k5" min="1" max="9" value="<?php echo $pairwise ? $pairwise['k3_vs_k5'] : 1; ?>" 
                                   class="form-control" style="width: 100%; cursor: pointer;" onchange="document.getElementById('k3_vs_k5_val').textContent = this.value;">
                            <p style="margin-top: 0.5rem; font-size: 1.1rem;">Nilai: <strong id="k3_vs_k5_val"><?php echo $pairwise ? $pairwise['k3_vs_k5'] : 1; ?></strong></p>
                        </div>

                        <div class="pairwise-group">
                            <div class="pairwise-label">K3 (Liquidity) vs K6 (Access)</div>
                            <input type="range" name="k3_vs_k6" min="1" max="9" value="<?php echo $pairwise ? $pairwise['k3_vs_k6'] : 1; ?>" 
                                   class="form-control" style="width: 100%; cursor: pointer;" onchange="document.getElementById('k3_vs_k6_val').textContent = this.value;">
                            <p style="margin-top: 0.5rem; font-size: 1.1rem;">Nilai: <strong id="k3_vs_k6_val"><?php echo $pairwise ? $pairwise['k3_vs_k6'] : 1; ?></strong></p>
                        </div>
                    </div>

                    <div class="card">
                        <h3 style="margin-bottom: 1.5rem;">K4 (Capital) vs Kriteria Lain</h3>
                        
                        <div class="pairwise-group">
                            <div class="pairwise-label">K4 (Capital) vs K5 (Income)</div>
                            <input type="range" name="k4_vs_k5" min="1" max="9" value="<?php echo $pairwise ? $pairwise['k4_vs_k5'] : 1; ?>" 
                                   class="form-control" style="width: 100%; cursor: pointer;" onchange="document.getElementById('k4_vs_k5_val').textContent = this.value;">
                            <p style="margin-top: 0.5rem; font-size: 1.1rem;">Nilai: <strong id="k4_vs_k5_val"><?php echo $pairwise ? $pairwise['k4_vs_k5'] : 1; ?></strong></p>
                        </div>

                        <div class="pairwise-group">
                            <div class="pairwise-label">K4 (Capital) vs K6 (Access)</div>
                            <input type="range" name="k4_vs_k6" min="1" max="9" value="<?php echo $pairwise ? $pairwise['k4_vs_k6'] : 1; ?>" 
                                   class="form-control" style="width: 100%; cursor: pointer;" onchange="document.getElementById('k4_vs_k6_val').textContent = this.value;">
                            <p style="margin-top: 0.5rem; font-size: 1.1rem;">Nilai: <strong id="k4_vs_k6_val"><?php echo $pairwise ? $pairwise['k4_vs_k6'] : 1; ?></strong></p>
                        </div>
                    </div>

                    <div class="card">
                        <h3 style="margin-bottom: 1.5rem;">K5 (Income) vs Kriteria Lain</h3>
                        
                        <div class="pairwise-group">
                            <div class="pairwise-label">K5 (Income) vs K6 (Access)</div>
                            <input type="range" name="k5_vs_k6" min="1" max="9" value="<?php echo $pairwise ? $pairwise['k5_vs_k6'] : 1; ?>" 
                                   class="form-control" style="width: 100%; cursor: pointer;" onchange="document.getElementById('k5_vs_k6_val').textContent = this.value;">
                            <p style="margin-top: 0.5rem; font-size: 1.1rem;">Nilai: <strong id="k5_vs_k6_val"><?php echo $pairwise ? $pairwise['k5_vs_k6'] : 1; ?></strong></p>
                        </div>
                    </div>

                    <div class="btn-group" style="margin-top: 2rem;">
                        <button type="submit" class="btn btn-primary">💾 Simpan Pairwise Matrix</button>
                        <a href="../index.php" class="btn btn-secondary">↩️ Kembali</a>
                    </div>
                </form>

                <!-- Real-time CR Calculator -->
                <div class="card" style="margin-top: 2rem; background: linear-gradient(135deg, rgba(34,197,94,0.1), rgba(59,130,246,0.1));">
                    <h3>⚡ Real-time Consistency Checker</h3>
                    <p style="color: var(--gray); margin-bottom: 1.5rem;">Consistency Ratio diperbarui otomatis saat Anda mengubah nilai:</p>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div style="padding: 1rem; background: white; border-radius: 6px; border-left: 4px solid var(--primary);">
                            <p style="margin: 0; color: var(--gray); font-size: 0.9rem;">Lambda Max</p>
                            <p style="margin: 0.5rem 0 0 0; font-size: 1.5rem; font-weight: 600;" id="calc_lambda">6.0000</p>
                        </div>
                        <div style="padding: 1rem; background: white; border-radius: 6px; border-left: 4px solid var(--primary);">
                            <p style="margin: 0; color: var(--gray); font-size: 0.9rem;">Consistency Index (CI)</p>
                            <p style="margin: 0.5rem 0 0 0; font-size: 1.5rem; font-weight: 600;" id="calc_ci">0.0000</p>
                        </div>
                    </div>

                    <div style="padding: 1.5rem; background: white; border-radius: 6px; border: 2px solid #e5e7eb;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <span style="font-size: 1.1rem; font-weight: 600;">Consistency Ratio (CR):</span>
                            <span style="font-size: 1.8rem; font-weight: 700; color: var(--primary);" id="calc_cr">0.00%</span>
                        </div>
                        <div style="height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden; margin-bottom: 0.5rem;">
                            <div style="height: 100%; background: linear-gradient(90deg, var(--success), var(--warning), var(--danger)); width: 0%; transition: width 0.3s;" id="calc_cr_bar"></div>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.85rem; color: var(--gray);">
                            <span>0%</span>
                            <span>5%</span>
                            <span>10%</span>
                            <span>15%</span>
                            <span>20%+</span>
                        </div>
                    </div>

                    <div style="margin-top: 1rem; padding: 1rem; border-radius: 6px; background: rgba(34,197,94,0.1); border-left: 4px solid var(--success);">
                        <p style="margin: 0; color: var(--success); font-weight: 600;" id="calc_status">
                            ✅ Status: Valid (CR < 10%) - Data Konsisten!
                        </p>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            <div class="container">
                <p>&copy; 2026 SPK AHP-TOPSIS | Panel Admin</p>
            </div>
        </footer>
    </div>

    <script>
    // Real-time CR Calculator
    const RI_values = [0, 0, 0.58, 0.90, 1.12, 1.24, 1.32, 1.41]; // RI for n=1 to 8
    const n = 6; // 6 criteria

    function calculateCR() {
        // Get all values from sliders
        const values = {
            k1_vs_k2: parseFloat(document.querySelector('input[name="k1_vs_k2"]').value),
            k1_vs_k3: parseFloat(document.querySelector('input[name="k1_vs_k3"]').value),
            k1_vs_k4: parseFloat(document.querySelector('input[name="k1_vs_k4"]').value),
            k1_vs_k5: parseFloat(document.querySelector('input[name="k1_vs_k5"]').value),
            k1_vs_k6: parseFloat(document.querySelector('input[name="k1_vs_k6"]').value),
            k2_vs_k3: parseFloat(document.querySelector('input[name="k2_vs_k3"]').value),
            k2_vs_k4: parseFloat(document.querySelector('input[name="k2_vs_k4"]').value),
            k2_vs_k5: parseFloat(document.querySelector('input[name="k2_vs_k5"]').value),
            k2_vs_k6: parseFloat(document.querySelector('input[name="k2_vs_k6"]').value),
            k3_vs_k4: parseFloat(document.querySelector('input[name="k3_vs_k4"]').value),
            k3_vs_k5: parseFloat(document.querySelector('input[name="k3_vs_k5"]').value),
            k3_vs_k6: parseFloat(document.querySelector('input[name="k3_vs_k6"]').value),
            k4_vs_k5: parseFloat(document.querySelector('input[name="k4_vs_k5"]').value),
            k4_vs_k6: parseFloat(document.querySelector('input[name="k4_vs_k6"]').value),
            k5_vs_k6: parseFloat(document.querySelector('input[name="k5_vs_k6"]').value),
        };

        // Build pairwise matrix
        const matrix = [
            [1, values.k1_vs_k2, values.k1_vs_k3, values.k1_vs_k4, values.k1_vs_k5, values.k1_vs_k6],
            [1/values.k1_vs_k2, 1, values.k2_vs_k3, values.k2_vs_k4, values.k2_vs_k5, values.k2_vs_k6],
            [1/values.k1_vs_k3, 1/values.k2_vs_k3, 1, values.k3_vs_k4, values.k3_vs_k5, values.k3_vs_k6],
            [1/values.k1_vs_k4, 1/values.k2_vs_k4, 1/values.k3_vs_k4, 1, values.k4_vs_k5, values.k4_vs_k6],
            [1/values.k1_vs_k5, 1/values.k2_vs_k5, 1/values.k3_vs_k5, 1/values.k4_vs_k5, 1, values.k5_vs_k6],
            [1/values.k1_vs_k6, 1/values.k2_vs_k6, 1/values.k3_vs_k6, 1/values.k4_vs_k6, 1/values.k5_vs_k6, 1],
        ];

        // Normalize by column sums
        const col_sums = Array(6).fill(0);
        for (let i = 0; i < 6; i++) {
            for (let j = 0; j < 6; j++) {
                col_sums[j] += matrix[i][j];
            }
        }

        const normalized = [];
        for (let i = 0; i < 6; i++) {
            normalized[i] = [];
            for (let j = 0; j < 6; j++) {
                normalized[i][j] = matrix[i][j] / col_sums[j];
            }
        }

        // Calculate weights (row averages)
        const weights = [];
        for (let i = 0; i < 6; i++) {
            let row_sum = 0;
            for (let j = 0; j < 6; j++) {
                row_sum += normalized[i][j];
            }
            weights[i] = row_sum / 6;
        }

        // Calculate weighted sum vector
        const ws_vector = [];
        for (let i = 0; i < 6; i++) {
            let ws = 0;
            for (let j = 0; j < 6; j++) {
                ws += matrix[i][j] * weights[j];
            }
            ws_vector[i] = ws;
        }

        // Calculate lambda_max
        let lambda_parts = [];
        for (let i = 0; i < 6; i++) {
            lambda_parts[i] = ws_vector[i] / weights[i];
        }
        const lambda_max = lambda_parts.reduce((a, b) => a + b) / 6;

        // Calculate CI and CR
        const ci = (lambda_max - n) / (n - 1);
        const cr = ci / RI_values[n - 1]; // FIXED: Use n-1 for array index (0-indexed)
        const cr_percent = cr * 100;

        // Update display
        document.getElementById('calc_lambda').textContent = lambda_max.toFixed(4);
        document.getElementById('calc_ci').textContent = ci.toFixed(4);
        document.getElementById('calc_cr').textContent = cr_percent.toFixed(2) + '%';
        
        // Update progress bar
        const bar_width = Math.min(cr_percent, 100);
        document.getElementById('calc_cr_bar').style.width = bar_width + '%';

        // Update status
        const status_elem = document.getElementById('calc_status');
        if (cr < 0.10) {
            status_elem.innerHTML = '✅ <strong>Status: Valid (CR < 10%)</strong> - Data Anda Konsisten! Siap disimpan.';
            status_elem.parentElement.style.background = 'rgba(34,197,94,0.1)';
            status_elem.parentElement.style.borderLeft = '4px solid var(--success)';
            status_elem.style.color = 'var(--success)';
        } else if (cr < 0.15) {
            status_elem.innerHTML = '⚠️ <strong>Status: Perhatian (10% ≤ CR < 15%)</strong> - Agak tidak konsisten, pertimbangkan untuk disesuaikan.';
            status_elem.parentElement.style.background = 'rgba(251,191,36,0.1)';
            status_elem.parentElement.style.borderLeft = '4px solid var(--warning)';
            status_elem.style.color = 'var(--warning)';
        } else {
            status_elem.innerHTML = '❌ <strong>Status: Invalid (CR ≥ 15%)</strong> - Data Anda tidak konsisten. Sesuaikan judgment Anda.';
            status_elem.parentElement.style.background = 'rgba(239,68,68,0.1)';
            status_elem.parentElement.style.borderLeft = '4px solid var(--danger)';
            status_elem.style.color = 'var(--danger)';
        }
    }

    // Calculate CR on page load
    window.addEventListener('load', calculateCR);

    // Recalculate when any slider changes
    document.querySelectorAll('input[type="range"]').forEach(slider => {
        slider.addEventListener('input', calculateCR);
    });
    </script>
</body>
</html>
