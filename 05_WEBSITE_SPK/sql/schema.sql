-- ============================================================================
-- DATABASE SCHEMA - SPK AHP-TOPSIS INVESTASI
-- Kosong (Tanpa Data) - Siap untuk input dari website
-- ============================================================================

CREATE DATABASE IF NOT EXISTS spk_investasi;
USE spk_investasi;

-- ============================================================================
-- TABLE 0: USERS (User Registration & Login)
-- ============================================================================
CREATE TABLE IF NOT EXISTS tbl_users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_username (username),
    INDEX idx_email (email)
);

-- ============================================================================
-- TABLE 1: PAKAR (Expert) - HANYA 1 PAKAR
-- ============================================================================
CREATE TABLE IF NOT EXISTS tbl_pakar (
    pakar_id INT AUTO_INCREMENT PRIMARY KEY,
    pakar_nama VARCHAR(100) NOT NULL,
    pakar_deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================================================
-- TABLE 2: PAIRWISE MATRIX (Perbandingan Berpasangan AHP)
-- Data diisi oleh Admin via form
-- ============================================================================
CREATE TABLE IF NOT EXISTS tbl_pairwise_matrix (
    pairwise_id INT AUTO_INCREMENT PRIMARY KEY,
    pakar_id INT NOT NULL,
    
    -- K1 (Return %) vs others
    k1_vs_k2 DECIMAL(10, 4),
    k1_vs_k3 DECIMAL(10, 4),
    k1_vs_k4 DECIMAL(10, 4),
    k1_vs_k5 DECIMAL(10, 4),
    k1_vs_k6 DECIMAL(10, 4),
    
    -- K2 (Risk %) vs others
    k2_vs_k3 DECIMAL(10, 4),
    k2_vs_k4 DECIMAL(10, 4),
    k2_vs_k5 DECIMAL(10, 4),
    k2_vs_k6 DECIMAL(10, 4),
    
    -- K3 (Liquidity) vs others
    k3_vs_k4 DECIMAL(10, 4),
    k3_vs_k5 DECIMAL(10, 4),
    k3_vs_k6 DECIMAL(10, 4),
    
    -- K4 (Capital Rp) vs others
    k4_vs_k5 DECIMAL(10, 4),
    k4_vs_k6 DECIMAL(10, 4),
    
    -- K5 (Income %) vs others
    k5_vs_k6 DECIMAL(10, 4),
    
    status VARCHAR(20) DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (pakar_id) REFERENCES tbl_pakar(pakar_id) ON DELETE CASCADE,
    UNIQUE KEY unique_pakar (pakar_id)
);

-- ============================================================================
-- TABLE 3: DECISION MATRIX (Nilai Alternatif per Kriteria)
-- Data diisi oleh Admin via form
-- 5 Alternatif × 6 Kriteria = 30 rows
-- ============================================================================
CREATE TABLE IF NOT EXISTS tbl_decision_matrix (
    decision_id INT AUTO_INCREMENT PRIMARY KEY,
    pakar_id INT NOT NULL,
    alternatif_id INT NOT NULL COMMENT '1=Kripto, 2=Saham, 3=SBN, 4=RekDana, 5=EmasDigital',
    
    -- K1: Return (%)
    k1_return DECIMAL(10, 2),
    -- K2: Risk (%)
    k2_risk DECIMAL(10, 2),
    -- K3: Liquidity (1-10)
    k3_liquidity DECIMAL(10, 2),
    -- K4: Capital Minimum (Rp)
    k4_capital INT,
    -- K5: Income (%)
    k5_income DECIMAL(10, 2),
    -- K6: Access (1-10)
    k6_access DECIMAL(10, 2),
    
    status VARCHAR(20) DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (pakar_id) REFERENCES tbl_pakar(pakar_id) ON DELETE CASCADE,
    UNIQUE KEY unique_pakar_alt (pakar_id, alternatif_id)
);

-- ============================================================================
-- TABLE 4: AHP RESULTS (Hasil Perhitungan AHP)
-- Diisi otomatis setelah admin jalankan perhitungan
-- ============================================================================
CREATE TABLE IF NOT EXISTS tbl_ahp_results (
    ahp_id INT AUTO_INCREMENT PRIMARY KEY,
    pakar_id INT NOT NULL,
    
    -- Bobot untuk setiap kriteria (hasil normalisasi)
    w_k1 DECIMAL(10, 4),
    w_k2 DECIMAL(10, 4),
    w_k3 DECIMAL(10, 4),
    w_k4 DECIMAL(10, 4),
    w_k5 DECIMAL(10, 4),
    w_k6 DECIMAL(10, 4),
    
    -- Consistency Check
    lambda_max DECIMAL(10, 4),
    ci DECIMAL(10, 4),
    cr DECIMAL(10, 4),
    is_consistent BOOLEAN DEFAULT FALSE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (pakar_id) REFERENCES tbl_pakar(pakar_id) ON DELETE CASCADE,
    UNIQUE KEY unique_pakar (pakar_id)
);

-- ============================================================================
-- TABLE 5: TOPSIS RESULTS (Hasil Ranking TOPSIS)
-- Diisi otomatis setelah admin jalankan perhitungan
-- ============================================================================
CREATE TABLE IF NOT EXISTS tbl_topsis_results (
    topsis_id INT AUTO_INCREMENT PRIMARY KEY,
    pakar_id INT NOT NULL,
    
    -- Hasil TOPSIS untuk setiap alternatif
    -- Alternatif 1: Kripto
    kripto_preference DECIMAL(10, 4),
    kripto_rank INT,
    
    -- Alternatif 2: Saham
    saham_preference DECIMAL(10, 4),
    saham_rank INT,
    
    -- Alternatif 3: SBN
    sbn_preference DECIMAL(10, 4),
    sbn_rank INT,
    
    -- Alternatif 4: Reksa Dana
    reksadana_preference DECIMAL(10, 4),
    reksadana_rank INT,
    
    -- Alternatif 5: Emas Digital
    emasdigital_preference DECIMAL(10, 4),
    emasdigital_rank INT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (pakar_id) REFERENCES tbl_pakar(pakar_id) ON DELETE CASCADE,
    UNIQUE KEY unique_pakar (pakar_id)
);

-- ============================================================================
-- TABLE 6: USER ASSESSMENT (Penilaian User)
-- User memasukkan penilaian mereka sendiri
-- Kemudian dibandingkan dengan penilaian pakar
-- ============================================================================
CREATE TABLE IF NOT EXISTS tbl_user_assessment (
    assessment_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    pakar_id INT NOT NULL,
    
    -- User Profile
    user_nama VARCHAR(100),
    user_usia INT,
    user_tujuan_investasi VARCHAR(200),
    
    -- Preference User (0-100)
    risk_tolerance INT COMMENT '0=Very Risk Averse, 100=Very Risk Seeker',
    return_expectation INT COMMENT '0=Conservative, 100=Aggressive',
    liquidity_need INT COMMENT '0=Need Liquid, 100=Can Lock Long-term',
    capital_available INT COMMENT 'Modal yang dimiliki user',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES tbl_users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (pakar_id) REFERENCES tbl_pakar(pakar_id) ON DELETE CASCADE,
    UNIQUE KEY unique_user (user_id),
    INDEX idx_user_id (user_id),
    INDEX idx_created (created_at)
);

-- ============================================================================
-- TABLE 7: ALTERNATIVES (Daftar Alternatif Investasi - Static)
-- ============================================================================
CREATE TABLE IF NOT EXISTS tbl_alternatives (
    alternatif_id INT AUTO_INCREMENT PRIMARY KEY,
    alternatif_nama VARCHAR(50) NOT NULL,
    alternatif_deskripsi TEXT,
    alternatif_icon VARCHAR(10),
    urutan INT
);

-- Insert data alternatif (STATIC - tidak akan berubah)
INSERT IGNORE INTO tbl_alternatives (alternatif_id, alternatif_nama, alternatif_deskripsi, alternatif_icon, urutan) VALUES
(1, 'Kripto', 'Cryptocurrency / Digital Currency (Bitcoin, Ethereum, dll)', '🪙', 1),
(2, 'Saham', 'Equity / Stock Market', '📈', 2),
(3, 'SBN Ritel', 'Surat Berharga Negara Ritel', '🏛️', 3),
(4, 'Reksa Dana', 'Mutual Fund', '💼', 4),
(5, 'Emas Digital', 'Digital Gold / E-Gold', '🔘', 5);

-- ============================================================================
-- TABLE 8: CRITERIA (Daftar Kriteria - Static)
-- ============================================================================
CREATE TABLE IF NOT EXISTS tbl_criteria (
    kriteria_id INT AUTO_INCREMENT PRIMARY KEY,
    kriteria_kode VARCHAR(10) NOT NULL,
    kriteria_nama VARCHAR(100) NOT NULL,
    kriteria_deskripsi TEXT,
    kriteria_tipe ENUM('benefit', 'cost') DEFAULT 'benefit',
    satuan VARCHAR(50),
    urutan INT
);

-- Insert data kriteria (STATIC - tidak akan berubah)
INSERT IGNORE INTO tbl_criteria (kriteria_id, kriteria_kode, kriteria_nama, kriteria_deskripsi, kriteria_tipe, satuan, urutan) VALUES
(1, 'K1', 'Return', 'Potensi keuntungan investasi', 'benefit', '%', 1),
(2, 'K2', 'Risk', 'Tingkat risiko/volatilitas', 'cost', '%', 2),
(3, 'K3', 'Liquidity', 'Kemudahan mencairkan dana', 'benefit', 'Skala 1-10', 3),
(4, 'K4', 'Capital', 'Modal minimum yang dibutuhkan', 'cost', 'Rp', 4),
(5, 'K5', 'Income', 'Passive income/dividend', 'benefit', '%', 5),
(6, 'K6', 'Access', 'Kemudahan akses platform', 'benefit', 'Skala 1-10', 6);

-- ============================================================================
-- TABLE 9: AUDIT LOG
-- ============================================================================
CREATE TABLE IF NOT EXISTS tbl_audit_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    pakar_id INT,
    action VARCHAR(100) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (pakar_id) REFERENCES tbl_pakar(pakar_id) ON DELETE SET NULL,
    INDEX idx_created (created_at)
);

-- ============================================================================
-- Status Database
-- ============================================================================
-- ✅ Semua table sudah dibuat
-- ✅ Data alternatif & kriteria sudah di-insert (STATIC)
-- ⭕ Data pakar KOSONG - siap diisi dari admin panel
-- ⭕ Data pairwise KOSONG - siap diisi dari admin panel
-- ⭕ Data decision matrix KOSONG - siap diisi dari admin panel
-- ⭕ Data AHP results KOSONG - akan diisi otomatis setelah perhitungan
-- ⭕ Data TOPSIS results KOSONG - akan diisi otomatis setelah perhitungan
-- ⭕ Data user assessment KOSONG - akan diisi dari user panel
-- ============================================================================
