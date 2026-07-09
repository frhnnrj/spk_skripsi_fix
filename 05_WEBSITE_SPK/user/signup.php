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
    <style>
        .signup-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, rgba(59,130,246,0.1), rgba(139,92,246,0.1));
            padding: 1rem;
        }
        .signup-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-top: 4px solid var(--success);
        }
        .signup-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--text);
        }
        .signup-subtitle {
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
            border-color: var(--success);
            box-shadow: 0 0 0 3px rgba(34,197,94,0.1);
        }
        .form-hint {
            font-size: 0.8rem;
            color: var(--gray);
            margin-top: 0.25rem;
        }
        .btn-signup {
            width: 100%;
            padding: 0.75rem;
            background: var(--success);
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s;
        }
        .btn-signup:hover {
            background: #16a34a;
        }
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
            color: var(--gray);
        }
        .login-link a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }
        .login-link a:hover {
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
    <div class="signup-container">
        <div class="signup-card">
            <h1 class="signup-title">✨ Daftar Akun Baru</h1>
            <p class="signup-subtitle">Buat akun untuk mulai menggunakan sistem</p>

            <?php if (!empty($signup_error)): ?>
                <div class="error-box">
                    <?php echo $signup_error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="full_name" class="form-control" 
                           placeholder="Cth: Budi Santoso"
                           value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                    <div class="form-hint">Opsional - bisa diisi nanti</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" 
                           placeholder="Minimal 3 karakter"
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                           required>
                    <div class="form-hint">Username harus unik dan minimal 3 karakter</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" 
                           placeholder="email@example.com"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                           required>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" 
                           placeholder="Minimal 6 karakter"
                           required>
                    <div class="form-hint">Password harus minimal 6 karakter</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirm" class="form-control" 
                           placeholder="Ulangi password Anda"
                           required>
                </div>

                <button type="submit" class="btn-signup">✅ Buat Akun</button>
            </form>

            <div class="login-link">
                Sudah punya akun? <a href="login.php">Login di sini →</a>
            </div>

            <div class="back-home">
                <a href="../">← Kembali ke beranda</a>
            </div>
        </div>
    </div>
</body>
</html>
