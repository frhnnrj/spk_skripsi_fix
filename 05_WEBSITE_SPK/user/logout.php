<?php
require_once '../config/database.php';

// Logout the user
logout_user();

// Redirect to login page
redirect('login.php', '✅ Anda berhasil logout! Terima kasih telah menggunakan sistem.', 'success');
