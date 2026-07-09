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
    <style>
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, rgba(59,130,246,0.1), rgba(139,92,246,0.1));
            padding: 1rem;
        }
        .login-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-top: 4px solid var(--primary);
        }
        .login-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--text);
        }
        .login-subtitle {
            font-size: 0.95rem;
            color: var(--gray);
            margin-bottom: 2rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text);
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
        .btn-login {
            width: 100%;
            padding: 0.75rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s;
        }
        .btn-login:hover {
            background: #2563eb;
        }
        .signup-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
            color: var(--gray);
        }
        .signup-link a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }
        .signup-link a:hover {
            text-decoration: underline;
        }
        .error-box {
            background: rgba(239,68,68,0.1);
            border: 1px solid #fca5a5;
            border-left: 4px solid #ef4444;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            color: #7f1d1d;
        }
        .back-home {
            margin-top: 1rem;
            text-align: center;
        }
        .back-home a {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <h1 class="login-title">🔐 Login</h1>
            <p class="login-subtitle">Masuk ke akun Anda untuk menggunakan sistem</p>

            <?php if (!empty($login_error)): ?>
                <div class="error-box">
                    <?php echo $login_error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" 
                           placeholder="Masukkan username Anda"
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                           required>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" 
                           placeholder="Masukkan password Anda"
                           required>
                </div>

                <button type="submit" class="btn-login">🔓 Login</button>
            </form>

            <div class="signup-link">
                Belum punya akun? <a href="signup.php">Buat akun baru →</a>
            </div>

            <div class="back-home">
                <a href="../">← Kembali ke beranda</a>
            </div>
        </div>
    </div>
</body>
</html>
