<?php
require_once '../config/database.php';

// If already logged in, redirect to dashboard
if (is_logged_in()) {
    redirect('index.php', 'Anda sudah login!', 'info');
}

$login_error = '';
$login_success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $login_error = '❌ Username dan password harus diisi!';
    } else {
        $result = login_user($username, $password);
        
        if ($result['success']) {
            redirect('index.php', '✅ Login berhasil! Selamat datang ' . $username . '!', 'success');
        } else {
            $login_error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SPK AHP-TOPSIS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-layout">
        <div class="auth-card auth-card-sm">
            <div class="auth-header">
                <h1>🔐 Login</h1>
                <p class="auth-subtitle">Masuk untuk mengakses penilaian dan hasil rekomendasi investasi.</p>
            </div>

            <?php if (!empty($login_error)): ?>
                <div class="alert alert-danger">
                    <?php echo $login_error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username"
                           placeholder="Masukkan username Anda"
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                           required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password"
                           placeholder="Masukkan password Anda"
                           required>
                </div>

                <button type="submit" class="btn btn-primary auth-cta">🔓 Login</button>
            </form>

            <div class="auth-links">
                Belum punya akun? <a href="signup.php">Buat akun baru →</a>
            </div>

            <div class="auth-back">
                <a href="../">← Kembali ke beranda</a>
            </div>
        </div>
    </div>
</body>
</html>
