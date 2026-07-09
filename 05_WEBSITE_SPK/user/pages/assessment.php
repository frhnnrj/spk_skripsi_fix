<?php
require_once '../../config/database.php';

// Require login
require_login();

$pakar = get_all_pakar();
if (count($pakar) === 0) {
    redirect('../index.php', 'Sistem belum siap. Admin masih mempersiapkan data.', 'warning');
}

$pakar_id = $pakar[0]['pakar_id'];
$user_id = get_current_user_id();

// Get existing assessment if any
$existing_assessment = null;
$query = "SELECT * FROM tbl_user_assessment WHERE user_id = $user_id AND pakar_id = $pakar_id LIMIT 1";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    $existing_assessment = $result->fetch_assoc();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_nama = sanitize($_POST['name'] ?? 'Anonymous User');
    $user_usia = intval($_POST['age'] ?? 0);
    $experience = sanitize($_POST['experience'] ?? 'beginner');
    $risk_tolerance = intval($_POST['risk_tolerance'] ?? 5) * 10; // Convert 1-10 to 0-100
    $return_expectation = intval($_POST['return_expectation'] ?? 5) * 10;
    $liquidity_need = intval($_POST['liquidity_need'] ?? 5) * 10;
    $capital_available = intval($_POST['capital_available'] ?? 0);
    $user_tujuan_investasi = sanitize($_POST['investment_goal'] ?? 'growth');

    if ($existing_assessment) {
        $query = "UPDATE tbl_user_assessment SET 
                  user_nama='$user_nama', user_usia=$user_usia, risk_tolerance=$risk_tolerance,
                  return_expectation=$return_expectation, liquidity_need=$liquidity_need,
                  capital_available=$capital_available, user_tujuan_investasi='$user_tujuan_investasi'
                  WHERE user_id=$user_id AND pakar_id=$pakar_id";
        
        if ($conn->query($query)) {
            $_SESSION['message'] = '✅ Penilaian Anda berhasil diperbarui!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = '❌ Error: ' . $conn->error;
            $_SESSION['message_type'] = 'danger';
        }
    } else {
        $query = "INSERT INTO tbl_user_assessment 
                  (user_id, pakar_id, user_nama, user_usia, risk_tolerance, return_expectation, 
                   liquidity_need, capital_available, user_tujuan_investasi, created_at) 
                  VALUES 
                  ($user_id, $pakar_id, '$user_nama', $user_usia, $risk_tolerance, $return_expectation,
                   $liquidity_need, $capital_available, '$user_tujuan_investasi', NOW())";
        
        if ($conn->query($query)) {
            $_SESSION['message'] = '✅ Penilaian Anda berhasil disimpan!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = '❌ Error: ' . $conn->error;
            $_SESSION['message_type'] = 'danger';
        }
    }

    $query = "SELECT * FROM tbl_user_assessment WHERE user_id = $user_id AND pakar_id = $pakar_id LIMIT 1";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $existing_assessment = $result->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penilaian User - SPK AHP-TOPSIS</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .question-section { 
            background: white; 
            border-radius: 8px; 
            padding: 2rem; 
            margin-bottom: 2rem; 
            border-left: 4px solid var(--primary);
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .question-title { 
            font-size: 1.3rem; 
            font-weight: 600; 
            color: var(--primary); 
            margin-bottom: 0.5rem;
        }
        .question-subtitle { 
            font-size: 0.95rem; 
            color: var(--gray); 
            margin-bottom: 1.5rem; 
            line-height: 1.5;
        }
        .slider-container { 
            margin: 1.5rem 0;
        }
        .slider-input { 
            width: 100%; 
            cursor: pointer;
        }
        .slider-value-display { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-top: 1rem;
            padding: 1rem;
            background: #f9fafb;
            border-radius: 6px;
        }
        .slider-value-display strong { 
            font-size: 1.5rem; 
            color: var(--primary);
        }
        .slider-label { 
            display: flex; 
            justify-content: space-between; 
            font-size: 0.85rem; 
            color: var(--gray); 
            margin-top: 0.5rem;
        }
        .form-hint { 
            font-size: 0.9rem; 
            color: var(--gray); 
            margin-bottom: 1rem; 
            padding: 0.75rem;
            background: rgba(100,116,139,0.05);
            border-left: 3px solid var(--gray);
            border-radius: 4px;
            line-height: 1.6;
        }
        .step-indicator {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }
        .option-label { 
            padding: 1rem; 
            border: 2px solid #e5e7eb; 
            border-radius: 6px; 
            cursor: pointer; 
            transition: all 0.3s;
            text-align: center;
        }
        .option-label:hover { 
            border-color: var(--primary); 
            background: rgba(59,130,246,0.05);
        }
        .option-label.selected { 
            border-color: var(--primary); 
            background: rgba(59,130,246,0.1);
            font-weight: 600;
        }
        .option-grid { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 1rem; 
            margin: 1.5rem 0;
        }
        .form-control { 
            width: 100%; 
            padding: 0.75rem; 
            border: 1px solid #d1d5db; 
            border-radius: 6px; 
            font-size: 1rem; 
        }
        .form-control:focus { 
            outline: none; 
            border-color: var(--primary); 
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1); 
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <header>
            <div class="container">
                <h1>👤 Panel User</h1>
                <p>Penilaian Preferensi Investasi Anda</p>
            </div>
        </header>

        <nav>
            <ul>
                <li><a href="../index.php">🏠 Beranda</a></li>
                <li><a href="education.php">📚 Belajar</a></li>
                <li><a href="assessment.php" class="active">📝 Penilaian</a></li>
                <li><a href="results.php">📊 Hasil</a></li>
                <li style="margin-left: auto;"><a href="../../">🏠 Home</a></li>
            </ul>
        </nav>

        <main>
            <div class="container">
                <h2>📝 Kuesioner Penilaian Profil Investasi</h2>
                <p style="color: var(--gray); margin-bottom: 2rem;">
                    Jawab beberapa pertanyaan di bawah ini untuk kami pahami profil investasi Anda dengan lebih baik. 
                    Jawaban yang jujur akan membantu kami memberikan rekomendasi yang paling sesuai.
                </p>

                <?php if (!empty($_SESSION['message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                        <?php 
                        echo $_SESSION['message'];
                        unset($_SESSION['message']);
                        unset($_SESSION['message_type']);
                        ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <!-- Step 1: Identitas -->
                    <div class="question-section">
                        <span class="step-indicator">📋 Step 1 / 5</span>
                        <div class="question-title">👤 Siapa nama Anda?</div>
                        <div class="question-subtitle">Masukkan nama lengkap atau tetap anonim jika Anda mau</div>
                        
                        <div class="form-hint">
                            💡 Nama ini hanya untuk membuat rekomendasi lebih personal bagi Anda. Anda bisa mengosongkan jika ingin tetap anonim.
                        </div>

                        <input type="text" name="name" class="form-control" 
                               value="<?php echo $existing_assessment ? htmlspecialchars($existing_assessment['user_nama']) : ''; ?>"
                               placeholder="Contoh: Budi Santoso">
                    </div>

                    <!-- Step 2: Usia & Pengalaman -->
                    <div class="question-section">
                        <span class="step-indicator">📋 Step 2 / 5</span>
                        <div class="question-title">🎂 Usia & Pengalaman Investasi Anda</div>
                        <div class="question-subtitle">Informasi ini membantu kami memahami tahap kehidupan finansial Anda</div>

                        <div class="form-row">
                            <div>
                                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">
                                    Berapa usia Anda?
                                </label>
                                <input type="number" name="age" class="form-control" 
                                       value="<?php echo $existing_assessment ? $existing_assessment['user_usia'] : ''; ?>"
                                       placeholder="Contoh: 25" min="13" max="100">
                            </div>

                            <div>
                                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">
                                    Pengalaman Investasi?
                                </label>
                                <select name="experience" class="form-control">
                                    <option value="">-- Pilih salah satu --</option>
                                    <option value="beginner" <?php echo $existing_assessment && $existing_assessment['experience'] == 'beginner' ? 'selected' : ''; ?>>
                                        🟢 Pemula (Belum pernah investasi)
                                    </option>
                                    <option value="intermediate" <?php echo $existing_assessment && $existing_assessment['experience'] == 'intermediate' ? 'selected' : ''; ?>>
                                        🟡 Menengah (1-3 tahun pengalaman)
                                    </option>
                                    <option value="advanced" <?php echo $existing_assessment && $existing_assessment['experience'] == 'advanced' ? 'selected' : ''; ?>>
                                        🔴 Berpengalaman (3+ tahun)
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Toleransi Risiko -->
                    <div class="question-section">
                        <span class="step-indicator">📋 Step 3 / 5</span>
                        <div class="question-title">⚖️ Berapa toleransi risiko Anda?</div>
                        <div class="question-subtitle">Seberapa nyaman Anda menerima fluktuasi nilai investasi?</div>

                        <div class="form-hint">
                            🔴 <strong>Konservatif:</strong> Lebih menyukai investasi aman dengan return stabil, meskipun pertumbuhan lambat<br>
                            🟡 <strong>Sedang:</strong> Mau balance antara keamanan dan pertumbuhan, siap risiko kecil untuk return lebih baik<br>
                            🟢 <strong>Agresif:</strong> Bersedia ambil risiko tinggi untuk mendapatkan return yang lebih besar
                        </div>

                        <div class="slider-container">
                            <input type="range" name="risk_tolerance" class="slider-input" min="1" max="10" 
                                   value="<?php echo $existing_assessment ? intval($existing_assessment['risk_tolerance'] / 10) : '5'; ?>"
                                   onchange="updateRiskDisplay(this.value)">
                            
                            <div class="slider-value-display">
                                <span id="risk_profile">Sedang</span>
                                <strong id="risk_value"><?php echo $existing_assessment ? intval($existing_assessment['risk_tolerance'] / 10) : '5'; ?></strong>
                                <span style="color: var(--gray);">/10</span>
                            </div>

                            <div class="slider-label">
                                <span>1 = Sangat Konservatif</span>
                                <span>10 = Sangat Agresif</span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Return & Modal -->
                    <div class="question-section">
                        <span class="step-indicator">📋 Step 4 / 5</span>
                        <div class="question-title">💰 Return yang diharapkan & Modal tersedia</div>
                        <div class="question-subtitle">Return dan modal akan menentukan pilihan instrumen yang cocok</div>

                        <div class="form-row">
                            <div>
                                <label style="display: block; font-weight: 600; margin-bottom: 1rem;">
                                    🎯 Ekspektasi Return Tahunan Anda?
                                </label>
                                <div class="slider-container" style="margin: 0;">
                                    <input type="range" name="return_expectation" class="slider-input" min="1" max="10"
                                           value="<?php echo $existing_assessment ? intval($existing_assessment['return_expectation'] / 10) : '5'; ?>"
                                           onchange="updateReturnDisplay(this.value)">
                                    
                                    <div class="slider-value-display">
                                        <span id="return_label">Sedang (5-10%)</span>
                                        <strong id="return_value"><?php echo $existing_assessment ? intval($existing_assessment['return_expectation'] / 10) : '5'; ?></strong>
                                        <span style="color: var(--gray);">/10</span>
                                    </div>

                                    <div class="slider-label">
                                        <span>1 = Rendah (2-5%)</span>
                                        <span>10 = Tinggi (15%+)</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">
                                    💵 Modal yang Anda Miliki?
                                </label>
                                <div class="form-hint">
                                    Berapa Rp yang siap Anda investasikan pada awal periode?
                                </div>
                                <input type="number" name="capital_available" class="form-control"
                                       value="<?php echo $existing_assessment ? $existing_assessment['capital_available'] : ''; ?>"
                                       placeholder="Cth: 5000000 (Rp)" min="0">
                            </div>
                        </div>
                    </div>

                    <!-- Step 5: Likuiditas & Tujuan -->
                    <div class="question-section">
                        <span class="step-indicator">📋 Step 5 / 5</span>
                        <div class="question-title">🎯 Kebutuhan Likuiditas & Tujuan Investasi</div>
                        <div class="question-subtitle">Pertanyaan terakhir untuk menentukan instrumen yang paling cocok</div>

                        <div style="margin-bottom: 2rem;">
                            <label style="display: block; font-weight: 600; margin-bottom: 1rem;">
                                🏦 Berapa sering Anda butuh akses dana investasi?
                            </label>
                            <div class="slider-container" style="margin: 0;">
                                <input type="range" name="liquidity_need" class="slider-input" min="1" max="10"
                                       value="<?php echo $existing_assessment ? intval($existing_assessment['liquidity_need'] / 10) : '5'; ?>"
                                       onchange="updateLiquidityDisplay(this.value)">
                                
                                <div class="slider-value-display">
                                    <span id="liquidity_label">Sedang</span>
                                    <strong id="liquidity_value"><?php echo $existing_assessment ? intval($existing_assessment['liquidity_need'] / 10) : '5'; ?></strong>
                                    <span style="color: var(--gray);">/10</span>
                                </div>

                                <div class="slider-label">
                                    <span>1 = Jangka Panjang (Tidak Cair)</span>
                                    <span>10 = Sangat Sering (Cair Cepat)</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 1rem;">
                                🎯 Tujuan Utama Investasi Anda?
                            </label>
                            
                            <div class="option-grid">
                                <label class="option-label <?php echo $existing_assessment && $existing_assessment['user_tujuan_investasi'] == 'growth' ? 'selected' : ''; ?>">
                                    <input type="radio" name="investment_goal" value="growth" 
                                           <?php echo $existing_assessment && $existing_assessment['user_tujuan_investasi'] == 'growth' ? 'checked' : ''; ?>>
                                    <div style="font-size: 1.5rem; margin: 0.5rem 0;">📈</div>
                                    <div style="font-weight: 600;">Growth</div>
                                    <small>Pertumbuhan Modal Jangka Panjang</small>
                                </label>

                                <label class="option-label <?php echo $existing_assessment && $existing_assessment['user_tujuan_investasi'] == 'income' ? 'selected' : ''; ?>">
                                    <input type="radio" name="investment_goal" value="income"
                                           <?php echo $existing_assessment && $existing_assessment['user_tujuan_investasi'] == 'income' ? 'checked' : ''; ?>>
                                    <div style="font-size: 1.5rem; margin: 0.5rem 0;">💰</div>
                                    <div style="font-weight: 600;">Income</div>
                                    <small>Passive Income Reguler</small>
                                </label>

                                <label class="option-label <?php echo $existing_assessment && $existing_assessment['user_tujuan_investasi'] == 'preservation' ? 'selected' : ''; ?>">
                                    <input type="radio" name="investment_goal" value="preservation"
                                           <?php echo $existing_assessment && $existing_assessment['user_tujuan_investasi'] == 'preservation' ? 'checked' : ''; ?>>
                                    <div style="font-size: 1.5rem; margin: 0.5rem 0;">🛡️</div>
                                    <div style="font-weight: 600;">Preservation</div>
                                    <small>Menjaga Nilai Aset</small>
                                </label>

                                <label class="option-label <?php echo $existing_assessment && $existing_assessment['user_tujuan_investasi'] == 'mixed' ? 'selected' : ''; ?>">
                                    <input type="radio" name="investment_goal" value="mixed"
                                           <?php echo $existing_assessment && $existing_assessment['user_tujuan_investasi'] == 'mixed' ? 'checked' : ''; ?>>
                                    <div style="font-size: 1.5rem; margin: 0.5rem 0;">⚖️</div>
                                    <div style="font-weight: 600;">Mixed</div>
                                    <small>Kombinasi Growth & Income</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; gap: 1rem; margin-top: 2.5rem; margin-bottom: 2rem;">
                        <button type="submit" class="btn btn-primary" style="flex: 1; padding: 1rem; font-size: 1.1rem;">
                            ✅ Simpan & Lihat Rekomendasi
                        </button>
                        <a href="../index.php" class="btn btn-secondary">↩️ Kembali</a>
                    </div>
                </form>

                <?php if ($existing_assessment): ?>
                <div class="box" style="background: rgba(34,197,94,0.1); border-left-color: var(--success);">
                    <h3>✅ Penilaian Anda Tersimpan!</h3>
                    <p>Terima kasih telah mengisi kuesioner. Kami sudah menganalisis profil investasi Anda.</p>
                    <a href="results.php" class="btn btn-primary" style="margin-top: 1rem;">📊 Lihat Rekomendasi Investasi →</a>
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

    <script>
        function updateRiskDisplay(value) {
            const profiles = ['Sangat Konservatif', 'Sangat Konservatif', 'Konservatif', 'Konservatif', 
                            'Sedang', 'Sedang', 'Agresif', 'Agresif', 'Sangat Agresif', 'Sangat Agresif'];
            document.getElementById('risk_profile').textContent = profiles[value - 1];
            document.getElementById('risk_value').textContent = value;
        }

        function updateReturnDisplay(value) {
            const ranges = ['2-5%', '2-5%', '3-6%', '4-7%', '5-10%', '5-10%', '8-12%', '10-15%', '12-18%', '15%+'];
            const labels = ['Rendah', 'Rendah', 'Rendah', 'Sedang', 'Sedang', 'Sedang', 'Tinggi', 'Tinggi', 'Sangat Tinggi', 'Sangat Tinggi'];
            document.getElementById('return_label').textContent = labels[value - 1] + ' (' + ranges[value - 1] + ')';
            document.getElementById('return_value').textContent = value;
        }

        function updateLiquidityDisplay(value) {
            const labels = ['Sangat Jangka Panjang', 'Sangat Jangka Panjang', 'Jangka Panjang', 'Jangka Panjang', 
                          'Sedang', 'Sedang', 'Jangka Pendek', 'Jangka Pendek', 'Sangat Cepat', 'Sangat Cepat'];
            document.getElementById('liquidity_label').textContent = labels[value - 1];
            document.getElementById('liquidity_value').textContent = value;
        }

        // Make option labels clickable
        document.querySelectorAll('.option-label').forEach(label => {
            label.addEventListener('click', function() {
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
                
                document.querySelectorAll('.option-label').forEach(l => l.classList.remove('selected'));
                this.classList.add('selected');
            });
        });

        // Initialize on page load
        window.addEventListener('load', function() {
            const riskVal = document.querySelector('input[name="risk_tolerance"]').value;
            const returnVal = document.querySelector('input[name="return_expectation"]').value;
            const liquidityVal = document.querySelector('input[name="liquidity_need"]').value;
            
            updateRiskDisplay(riskVal);
            updateReturnDisplay(returnVal);
            updateLiquidityDisplay(liquidityVal);

            document.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
                radio.closest('.option-label').classList.add('selected');
            });
        });
    </script>
</body>
</html>
