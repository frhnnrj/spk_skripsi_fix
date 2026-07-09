<?php
/**
 * Setup Database - Buat dan Initialize Database Kosong
 * Jalankan: http://localhost/SKRIPSI%20BRAYYY/05_WEBSITE_SPK/setup.php
 */

$servername = "localhost";
$username = "root";
$password = "";

// Connect without database
$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

echo "🔄 Setting up database...\n\n";

// ============================================================================
// STEP 1: Create Database
// ============================================================================

$sql = "CREATE DATABASE IF NOT EXISTS spk_investasi";
if ($conn->query($sql) === TRUE) {
    echo "✅ Database created/exists\n";
} else {
    die("❌ Error creating database: " . $conn->error);
}

// Select database
$conn->select_db("spk_investasi");

// ============================================================================
// STEP 2: Create Tables
// ============================================================================

$tables = [
    // TABLE 1: Pakar
    "CREATE TABLE IF NOT EXISTS tbl_pakar (
        pakar_id INT AUTO_INCREMENT PRIMARY KEY,
        pakar_nama VARCHAR(100) NOT NULL,
        pakar_deskripsi TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )" => "tbl_pakar",
    
    // TABLE 2: Pairwise Matrix
    "CREATE TABLE IF NOT EXISTS tbl_pairwise_matrix (
        pairwise_id INT AUTO_INCREMENT PRIMARY KEY,
        pakar_id INT NOT NULL,
        k1_vs_k2 DECIMAL(10, 4),
        k1_vs_k3 DECIMAL(10, 4),
        k1_vs_k4 DECIMAL(10, 4),
        k1_vs_k5 DECIMAL(10, 4),
        k1_vs_k6 DECIMAL(10, 4),
        k2_vs_k3 DECIMAL(10, 4),
        k2_vs_k4 DECIMAL(10, 4),
        k2_vs_k5 DECIMAL(10, 4),
        k2_vs_k6 DECIMAL(10, 4),
        k3_vs_k4 DECIMAL(10, 4),
        k3_vs_k5 DECIMAL(10, 4),
        k3_vs_k6 DECIMAL(10, 4),
        k4_vs_k5 DECIMAL(10, 4),
        k4_vs_k6 DECIMAL(10, 4),
        k5_vs_k6 DECIMAL(10, 4),
        status VARCHAR(20) DEFAULT 'draft',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (pakar_id) REFERENCES tbl_pakar(pakar_id) ON DELETE CASCADE,
        UNIQUE KEY unique_pakar (pakar_id)
    )" => "tbl_pairwise_matrix",
    
    // TABLE 3: Decision Matrix
    "CREATE TABLE IF NOT EXISTS tbl_decision_matrix (
        decision_id INT AUTO_INCREMENT PRIMARY KEY,
        pakar_id INT NOT NULL,
        alternatif_id INT NOT NULL,
        k1_return DECIMAL(10, 2),
        k2_risk DECIMAL(10, 2),
        k3_liquidity DECIMAL(10, 2),
        k4_capital INT,
        k5_income DECIMAL(10, 2),
        k6_access DECIMAL(10, 2),
        status VARCHAR(20) DEFAULT 'draft',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (pakar_id) REFERENCES tbl_pakar(pakar_id) ON DELETE CASCADE,
        UNIQUE KEY unique_pakar_alt (pakar_id, alternatif_id)
    )" => "tbl_decision_matrix",
    
    // TABLE 4: AHP Results
    "CREATE TABLE IF NOT EXISTS tbl_ahp_results (
        ahp_id INT AUTO_INCREMENT PRIMARY KEY,
        pakar_id INT NOT NULL,
        w_k1 DECIMAL(10, 4),
        w_k2 DECIMAL(10, 4),
        w_k3 DECIMAL(10, 4),
        w_k4 DECIMAL(10, 4),
        w_k5 DECIMAL(10, 4),
        w_k6 DECIMAL(10, 4),
        lambda_max DECIMAL(10, 4),
        ci DECIMAL(10, 4),
        cr DECIMAL(10, 4),
        is_consistent BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (pakar_id) REFERENCES tbl_pakar(pakar_id) ON DELETE CASCADE,
        UNIQUE KEY unique_pakar (pakar_id)
    )" => "tbl_ahp_results",
    
    // TABLE 5: TOPSIS Results
    "CREATE TABLE IF NOT EXISTS tbl_topsis_results (
        topsis_id INT AUTO_INCREMENT PRIMARY KEY,
        pakar_id INT NOT NULL,
        kripto_preference DECIMAL(10, 4),
        kripto_rank INT,
        saham_preference DECIMAL(10, 4),
        saham_rank INT,
        sbn_preference DECIMAL(10, 4),
        sbn_rank INT,
        reksadana_preference DECIMAL(10, 4),
        reksadana_rank INT,
        emasdigital_preference DECIMAL(10, 4),
        emasdigital_rank INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (pakar_id) REFERENCES tbl_pakar(pakar_id) ON DELETE CASCADE,
        UNIQUE KEY unique_pakar (pakar_id)
    )" => "tbl_topsis_results",
    
    // TABLE 6: User Assessment
    "CREATE TABLE IF NOT EXISTS tbl_user_assessment (
        assessment_id INT AUTO_INCREMENT PRIMARY KEY,
        pakar_id INT NOT NULL,
        session_id VARCHAR(100) NOT NULL,
        user_nama VARCHAR(100),
        user_usia INT,
        user_tujuan_investasi VARCHAR(200),
        risk_tolerance INT,
        return_expectation INT,
        liquidity_need INT,
        capital_available INT,
        user_top1_alternatif VARCHAR(50),
        user_top2_alternatif VARCHAR(50),
        user_top3_alternatif VARCHAR(50),
        match_percentage DECIMAL(5, 2),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (pakar_id) REFERENCES tbl_pakar(pakar_id) ON DELETE CASCADE,
        INDEX idx_session (session_id),
        INDEX idx_created (created_at)
    )" => "tbl_user_assessment",
    
    // TABLE 7: Alternatives (Static)
    "CREATE TABLE IF NOT EXISTS tbl_alternatives (
        alternatif_id INT AUTO_INCREMENT PRIMARY KEY,
        alternatif_nama VARCHAR(50) NOT NULL,
        alternatif_deskripsi TEXT,
        alternatif_icon VARCHAR(10),
        urutan INT
    )" => "tbl_alternatives",
    
    // TABLE 8: Criteria (Static)
    "CREATE TABLE IF NOT EXISTS tbl_criteria (
        kriteria_id INT AUTO_INCREMENT PRIMARY KEY,
        kriteria_kode VARCHAR(10) NOT NULL,
        kriteria_nama VARCHAR(100) NOT NULL,
        kriteria_deskripsi TEXT,
        kriteria_tipe ENUM('benefit', 'cost') DEFAULT 'benefit',
        satuan VARCHAR(50),
        urutan INT
    )" => "tbl_criteria",
    
    // TABLE 9: Audit Log
    "CREATE TABLE IF NOT EXISTS tbl_audit_log (
        log_id INT AUTO_INCREMENT PRIMARY KEY,
        pakar_id INT,
        action VARCHAR(100) NOT NULL,
        details TEXT,
        ip_address VARCHAR(45),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (pakar_id) REFERENCES tbl_pakar(pakar_id) ON DELETE SET NULL,
        INDEX idx_created (created_at)
    )" => "tbl_audit_log"
];

foreach ($tables as $sql => $table_name) {
    if ($conn->query($sql) === TRUE) {
        echo "✅ Table $table_name created\n";
    } else {
        echo "⚠️  Table $table_name: " . $conn->error . "\n";
    }
}

// ============================================================================
// STEP 3: Insert Static Data (Alternatives & Criteria)
// ============================================================================

echo "\n📊 Inserting static data...\n";

$alternatives = [
    [1, 'Kripto', 'Cryptocurrency / Digital Currency (Bitcoin, Ethereum, dll)', '🪙', 1],
    [2, 'Saham', 'Equity / Stock Market', '📈', 2],
    [3, 'SBN Ritel', 'Surat Berharga Negara Ritel', '🏛️', 3],
    [4, 'Reksa Dana', 'Mutual Fund', '💼', 4],
    [5, 'Emas Digital', 'Digital Gold / E-Gold', '🔘', 5]
];

$criteria = [
    [1, 'K1', 'Return', 'Potensi keuntungan investasi', 'benefit', '%', 1],
    [2, 'K2', 'Risk', 'Tingkat risiko/volatilitas', 'cost', '%', 2],
    [3, 'K3', 'Liquidity', 'Kemudahan mencairkan dana', 'benefit', 'Skala 1-10', 3],
    [4, 'K4', 'Capital', 'Modal minimum yang dibutuhkan', 'cost', 'Rp', 4],
    [5, 'K5', 'Income', 'Passive income/dividend', 'benefit', '%', 5],
    [6, 'K6', 'Access', 'Kemudahan akses platform', 'benefit', 'Skala 1-10', 6]
];

// Insert alternatives
foreach ($alternatives as $alt) {
    $sql = "INSERT IGNORE INTO tbl_alternatives (alternatif_id, alternatif_nama, alternatif_deskripsi, alternatif_icon, urutan) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssi", $alt[0], $alt[1], $alt[2], $alt[3], $alt[4]);
    $stmt->execute();
    $stmt->close();
}
echo "✅ Alternatives inserted (5 items)\n";

// Insert criteria
foreach ($criteria as $crit) {
    $sql = "INSERT IGNORE INTO tbl_criteria (kriteria_id, kriteria_kode, kriteria_nama, kriteria_deskripsi, kriteria_tipe, satuan, urutan) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssi", $crit[0], $crit[1], $crit[2], $crit[3], $crit[4], $crit[5], $crit[6]);
    $stmt->execute();
    $stmt->close();
}
echo "✅ Criteria inserted (6 items)\n";

echo "\n✅ Database setup complete!\n\n";
echo "📍 Next steps:\n";
echo "   1. Go to Admin: http://localhost/SKRIPSI%20BRAYYY/05_WEBSITE_SPK/admin/\n";
echo "   2. Add pakar data\n";
echo "   3. Add pairwise & decision matrix\n";
echo "   4. Calculate AHP & TOPSIS\n";
echo "   5. Users can access: http://localhost/SKRIPSI%20BRAYYY/05_WEBSITE_SPK/user/\n";

$conn->close();
?>
