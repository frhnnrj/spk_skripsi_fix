<?php
require_once '../../config/database.php';

$pakar = get_all_pakar();
if (count($pakar) === 0) {
    redirect('../index.php', 'Belum ada pakar. Tambahkan pakar terlebih dahulu.', 'warning');
}

$pakar_id = $pakar[0]['pakar_id'];
$pakar_nama = $pakar[0]['pakar_nama'];
$alternatives = get_all_alternatives();
$criteria = get_all_criteria();
$decision_matrix = get_decision_matrix($pakar_id);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $success_count = 0;
    $error_count = 0;

    foreach ($alternatives as $alt) {
        $alt_id = $alt['alternatif_id'];
        $k1 = floatval($_POST["k1_{$alt_id}"] ?? 0);
        $k2 = floatval($_POST["k2_{$alt_id}"] ?? 0);
        $k3 = floatval($_POST["k3_{$alt_id}"] ?? 0);
        $k4 = intval($_POST["k4_{$alt_id}"] ?? 0);
        $k5 = floatval($_POST["k5_{$alt_id}"] ?? 0);
        $k6 = floatval($_POST["k6_{$alt_id}"] ?? 0);

        // Check if record exists
        $exist = false;
        foreach ($decision_matrix as $dm) {
            if ($dm['alternatif_id'] == $alt_id) {
                $exist = true;
                break;
            }
        }

        if ($exist) {
            // Update - direct query with extracted values
            $query = "UPDATE tbl_decision_matrix SET k1_return=$k1, k2_risk=$k2, k3_liquidity=$k3, k4_capital=$k4, k5_income=$k5, k6_access=$k6, status='complete' WHERE pakar_id=$pakar_id AND alternatif_id=$alt_id";
            if ($conn->query($query)) {
                $success_count++;
            } else {
                $error_count++;
            }
        } else {
            // Insert - direct query with extracted values
            $query = "INSERT INTO tbl_decision_matrix (pakar_id, alternatif_id, k1_return, k2_risk, k3_liquidity, k4_capital, k5_income, k6_access, status) VALUES ($pakar_id, $alt_id, $k1, $k2, $k3, $k4, $k5, $k6, 'complete')";
            if ($conn->query($query)) {
                $success_count++;
            } else {
                $error_count++;
            }
        }
    }

    audit_log($pakar_id, 'UPDATE_DECISION_MATRIX', "Updated decision matrix - $success_count items");
    
    if ($error_count === 0) {
        $_SESSION['message'] = "✅ Decision matrix berhasil disimpan ($success_count alternatif)!";
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = "⚠️ Disimpan: $success_count, Error: $error_count";
        $_SESSION['message_type'] = 'warning';
    }

    // Refresh data
    $decision_matrix = get_decision_matrix($pakar_id);
}

// Get current values
$dm_values = [];
foreach ($decision_matrix as $dm) {
    $dm_values[$dm['alternatif_id']] = $dm;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Decision Matrix - SPK AHP-TOPSIS</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .criteria-col { background: linear-gradient(135deg, #e0e7ff 0%, #f3e8ff 100%); }
        
        /* Card-based layout untuk Decision Matrix */
        .decision-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        
        .decision-card {
            background: #f9fafb;
            border: 2px solid #e0e7ff;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        
        .decision-card:hover {
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.1);
        }
        
        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            padding-bottom: 0.8rem;
            border-bottom: 2px solid #e0e7ff;
            color: var(--primary);
        }
        
        .criteria-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        
        .form-field {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }
        
        .form-field label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .form-field label span {
            font-size: 0.75rem;
            color: var(--gray);
            font-weight: 400;
        }
        
        .input-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .input-group input {
            flex: 1;
            padding: 0.7rem;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            transition: all 0.2s;
        }
        
        .input-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        
        .input-group span {
            font-size: 0.85rem;
            color: var(--gray);
            font-weight: 500;
            min-width: 40px;
        }
        
        @media (max-width: 768px) {
            .decision-cards {
                grid-template-columns: 1fr;
            }
            
            .criteria-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <header>
            <div class="container">
                <h1>🔐 Panel Admin</h1>
                <p>Input Decision Matrix (30 Nilai)</p>
            </div>
        </header>

        <nav>
            <ul>
                <li><a href="../index.php">🏠 Dashboard</a></li>
                <li><a href="pakar_form.php">➕ Pakar</a></li>
                <li><a href="pairwise_form.php">📊 Pairwise Matrix</a></li>
                <li><a href="decision_form.php" class="active">📋 Decision Matrix</a></li>
                <li><a href="results.php">📈 Hasil Kalkulasi</a></li>
                <li style="margin-left: auto;"><a href="../../">🏠 Home</a></li>
            </ul>
        </nav>

        <main>
            <div class="container">
                <h2>📋 Input Decision Matrix</h2>
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
                    <p>Isi nilai untuk setiap alternatif pada 6 kriteria berikut:</p>
                    <ul>
                        <li><strong>K1 (Return):</strong> Potensi keuntungan tahunan (%) - Benefit</li>
                        <li><strong>K2 (Risk):</strong> Tingkat risiko/volatilitas (%) - Cost (semakin rendah semakin baik)</li>
                        <li><strong>K3 (Liquidity):</strong> Kemudahan mencairkan (1-10) - Benefit</li>
                        <li><strong>K4 (Capital):</strong> Modal minimum (Rp) - Cost (semakin rendah semakin baik)</li>
                        <li><strong>K5 (Income):</strong> Passive income/dividend (%) - Benefit</li>
                        <li><strong>K6 (Access):</strong> Kemudahan akses (1-10) - Benefit</li>
                    </ul>
                </div>

                <form method="POST" action="">
                    <div class="decision-cards">
                        <?php foreach ($alternatives as $alt): 
                            $alt_id = $alt['alternatif_id'];
                            $dm = $dm_values[$alt_id] ?? null;
                        ?>
                        <div class="decision-card">
                            <div class="card-title">
                                <?php echo $alt['alternatif_icon']; ?> <?php echo htmlspecialchars($alt['alternatif_nama']); ?>
                            </div>
                            
                            <div class="criteria-grid">
                                <!-- K1: Return -->
                                <div class="form-field">
                                    <label>
                                        K1: Return <span>(%)</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" name="k1_<?php echo $alt_id; ?>" step="0.01" min="0"
                                               value="<?php echo $dm ? $dm['k1_return'] : ''; ?>"
                                               placeholder="0" required>
                                        <span>%</span>
                                    </div>
                                </div>
                                
                                <!-- K2: Risk -->
                                <div class="form-field">
                                    <label>
                                        K2: Risk <span>(%)</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" name="k2_<?php echo $alt_id; ?>" step="0.01" min="0"
                                               value="<?php echo $dm ? $dm['k2_risk'] : ''; ?>"
                                               placeholder="0" required>
                                        <span>%</span>
                                    </div>
                                </div>
                                
                                <!-- K3: Liquidity -->
                                <div class="form-field">
                                    <label>
                                        K3: Liquidity <span>(1-10)</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" name="k3_<?php echo $alt_id; ?>" step="0.01" min="1" max="10"
                                               value="<?php echo $dm ? $dm['k3_liquidity'] : ''; ?>"
                                               placeholder="1-10" required>
                                        <span>/10</span>
                                    </div>
                                </div>
                                
                                <!-- K4: Capital -->
                                <div class="form-field">
                                    <label>
                                        K4: Capital <span>(Rp)</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" name="k4_<?php echo $alt_id; ?>" min="0"
                                               value="<?php echo $dm ? $dm['k4_capital'] : ''; ?>"
                                               placeholder="0" required>
                                        <span>Rp</span>
                                    </div>
                                </div>
                                
                                <!-- K5: Income -->
                                <div class="form-field">
                                    <label>
                                        K5: Income <span>(%)</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" name="k5_<?php echo $alt_id; ?>" step="0.01" min="0"
                                               value="<?php echo $dm ? $dm['k5_income'] : ''; ?>"
                                               placeholder="0" required>
                                        <span>%</span>
                                    </div>
                                </div>
                                
                                <!-- K6: Access -->
                                <div class="form-field">
                                    <label>
                                        K6: Access <span>(1-10)</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" name="k6_<?php echo $alt_id; ?>" step="0.01" min="1" max="10"
                                               value="<?php echo $dm ? $dm['k6_access'] : ''; ?>"
                                               placeholder="1-10" required>
                                        <span>/10</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="btn-group" style="margin-top: 2rem;">
                        <button type="submit" class="btn btn-primary">💾 Simpan Decision Matrix</button>
                        <a href="../index.php" class="btn btn-secondary">↩️ Kembali</a>
                    </div>
                </form>

                <div class="box" style="margin-top: 2rem;">
                    <h3>📊 Contoh Data (Referensi)</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Alternatif</th>
                                <th>K1 Return</th>
                                <th>K2 Risk</th>
                                <th>K3 Liquidity</th>
                                <th>K4 Capital</th>
                                <th>K5 Income</th>
                                <th>K6 Access</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="background: var(--light);">
                                <td>🪙 Kripto</td>
                                <td>45%</td>
                                <td>65%</td>
                                <td>9</td>
                                <td>1.000.000</td>
                                <td>2%</td>
                                <td>8</td>
                            </tr>
                            <tr>
                                <td>📈 Saham</td>
                                <td>20%</td>
                                <td>40%</td>
                                <td>8</td>
                                <td>100.000</td>
                                <td>3%</td>
                                <td>7</td>
                            </tr>
                            <tr style="background: var(--light);">
                                <td>🏛️ SBN Ritel</td>
                                <td>6%</td>
                                <td>5%</td>
                                <td>6</td>
                                <td>1.000.000</td>
                                <td>6%</td>
                                <td>5</td>
                            </tr>
                            <tr>
                                <td>💼 Reksa Dana</td>
                                <td>12%</td>
                                <td>25%</td>
                                <td>7</td>
                                <td>10.000</td>
                                <td>4%</td>
                                <td>8</td>
                            </tr>
                            <tr style="background: var(--light);">
                                <td>🔘 Emas Digital</td>
                                <td>8%</td>
                                <td>20%</td>
                                <td>8</td>
                                <td>5.000</td>
                                <td>0%</td>
                                <td>9</td>
                            </tr>
                        </tbody>
                    </table>
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
