<?php
require_once '../../config/database.php';

$pakar_id = isset($_GET['id']) ? intval($_GET['id']) : null;
$pakar = null;
$page_title = 'Tambah Pakar Baru';

if ($pakar_id) {
    $pakar = get_pakar_by_id($pakar_id);
    $page_title = 'Edit Pakar';
    if (!$pakar) {
        redirect('../index.php', 'Pakar tidak ditemukan', 'danger');
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pakar_nama = sanitize($_POST['pakar_nama'] ?? '');
    $pakar_deskripsi = sanitize($_POST['pakar_deskripsi'] ?? '');

    if (empty($pakar_nama)) {
        $_SESSION['message'] = 'Nama pakar tidak boleh kosong';
        $_SESSION['message_type'] = 'danger';
    } else {
        if ($pakar_id) {
            // Update pakar
            $query = "UPDATE tbl_pakar SET pakar_nama = ?, pakar_deskripsi = ? WHERE pakar_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssi", $pakar_nama, $pakar_deskripsi, $pakar_id);
            
            if ($stmt->execute()) {
                audit_log($pakar_id, 'UPDATE_PAKAR', "Updated pakar: $pakar_nama");
                redirect('../index.php', '✅ Pakar berhasil diperbarui!', 'success');
            } else {
                $_SESSION['message'] = '❌ Error: ' . $stmt->error;
                $_SESSION['message_type'] = 'danger';
            }
            $stmt->close();
        } else {
            // Check if pakar already exists
            $check = get_all_pakar();
            if (count($check) > 0) {
                $_SESSION['message'] = '⚠️ Sistem hanya mendukung 1 pakar. Hapus pakar lama terlebih dahulu.';
                $_SESSION['message_type'] = 'warning';
            } else {
                // Insert pakar baru
                $query = "INSERT INTO tbl_pakar (pakar_nama, pakar_deskripsi) VALUES (?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ss", $pakar_nama, $pakar_deskripsi);
                
                if ($stmt->execute()) {
                    $new_pakar_id = $stmt->insert_id;
                    audit_log($new_pakar_id, 'CREATE_PAKAR', "Created pakar: $pakar_nama");
                    redirect('../index.php', '✅ Pakar baru berhasil ditambahkan!', 'success');
                } else {
                    $_SESSION['message'] = '❌ Error: ' . $stmt->error;
                    $_SESSION['message_type'] = 'danger';
                }
                $stmt->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - SPK AHP-TOPSIS</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="main-container">
        <header>
            <div class="container">
                <h1>🔐 Panel Admin</h1>
                <p><?php echo $page_title; ?></p>
            </div>
        </header>

        <nav>
            <ul>
                <li><a href="../index.php">🏠 Dashboard</a></li>
                <li><a href="pakar_form.php" class="active">➕ Pakar</a></li>
                <li><a href="pairwise_form.php">📊 Pairwise Matrix</a></li>
                <li><a href="decision_form.php">📋 Decision Matrix</a></li>
                <li><a href="results.php">📈 Hasil Kalkulasi</a></li>
                <li style="margin-left: auto;"><a href="../../">🏠 Home</a></li>
            </ul>
        </nav>

        <main>
            <div class="container">
                <h2><?php echo $page_title; ?></h2>

                <?php if (!empty($_SESSION['message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                        <?php 
                        echo $_SESSION['message'];
                        unset($_SESSION['message']);
                        unset($_SESSION['message_type']);
                        ?>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="pakar_nama">👤 Nama Pakar *</label>
                            <input type="text" id="pakar_nama" name="pakar_nama" 
                                   value="<?php echo $pakar ? htmlspecialchars($pakar['pakar_nama']) : ''; ?>" 
                                   placeholder="Contoh: Dr. Budi Santoso" required>
                            <small>Masukkan nama lengkap pakar/expert</small>
                        </div>

                        <div class="form-group">
                            <label for="pakar_deskripsi">📝 Deskripsi Pakar</label>
                            <textarea id="pakar_deskripsi" name="pakar_deskripsi" 
                                      placeholder="Contoh: Pakar investasi dengan pengalaman 10 tahun di sektor fintech..."
                                      rows="5"><?php echo $pakar ? htmlspecialchars($pakar['pakar_deskripsi']) : ''; ?></textarea>
                            <small>Tulis latar belakang/expertise pakar (opsional)</small>
                        </div>

                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">
                                <?php echo $pakar ? '✏️ Update Pakar' : '➕ Tambah Pakar'; ?>
                            </button>
                            <a href="../index.php" class="btn btn-secondary">↩️ Kembali</a>
                        </div>
                    </form>
                </div>

                <?php if ($pakar): ?>
                <div class="box" style="border-left-color: var(--danger);">
                    <h3>⚠️ Zona Berbahaya</h3>
                    <p>Hapus pakar ini (tidak bisa dibatalkan):</p>
                    <form method="POST" action="delete_pakar.php" style="display: inline;" 
                          onsubmit="return confirm('Yakin ingin menghapus pakar? Semua data terkait akan dihapus!');">
                        <input type="hidden" name="pakar_id" value="<?php echo $pakar['pakar_id']; ?>">
                        <button type="submit" class="btn btn-danger">🗑️ Hapus Pakar</button>
                    </form>
                </div>
                <?php endif; ?>

                <div class="box">
                    <h3>📌 Info</h3>
                    <ul>
                        <li>Sistem hanya mendukung <strong>1 pakar</strong> untuk saat ini</li>
                        <li>Data pakar dapat diubah kapan saja</li>
                        <li>Setelah tambah pakar, lakukan input pairwise matrix & decision matrix</li>
                    </ul>
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
