<?php
require_once '../config/database.php';

// If already logged in, redirect to dashboard
if (is_logged_in()) {
    redirect('index.php', 'Anda sudah login!', 'info');
}

$signup_error = '';
$signup_success = '';

// Handle signup form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    
    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($password_confirm)) {
        $signup_error = '❌ Semua field harus diisi!';
    } elseif ($password !== $password_confirm) {
        $signup_error = '❌ Password dan konfirmasi password tidak cocok!';
    } else {
        $result = register_user($username, $email, $password, $full_name);
        
        if ($result['success']) {
            redirect('index.php', '✅ Akun berhasil dibuat! Selamat datang ' . $username . '!', 'success');
        } else {
            $signup_error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - SPK AHP-TOPSIS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-layout">
        <div class="auth-card">
            <div class="auth-header">
                <h1>✨ Daftar Akun Baru</h1>
                <p class="auth-subtitle">Buat akun untuk mulai melakukan penilaian investasi.</p>
            </div>

            <?php if (!empty($signup_error)): ?>
                <div class="alert alert-danger">
                    <?php echo $signup_error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="full_name"
                           placeholder="Cth: Budi Santoso"
                           value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                    <small>Opsional - bisa diisi nanti.</small>
                </div>

                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username"
                           placeholder="Minimal 3 karakter"
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                           required>
                    <small>Username harus unik dan minimal 3 karakter.</small>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email"
                           placeholder="email@example.com"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                           required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password"
                           placeholder="Minimal 6 karakter"
                           required>
                    <small>Password harus minimal 6 karakter.</small>
                </div>

                <div class="form-group">
                    <label>Konfirmasi Password</label>
                    <input type="password" name="password_confirm"
                           placeholder="Ulangi password Anda"
                           required>
                </div>

                <button type="submit" class="btn btn-success auth-cta">✅ Buat Akun</button>
            </form>

            <div class="auth-links">
                Sudah punya akun? <a href="login.php">Login di sini →</a>
            </div>

            <div class="auth-back">
                <a href="../">← Kembali ke beranda</a>
            </div>
        </div>
    </div>
</body>
</html>
