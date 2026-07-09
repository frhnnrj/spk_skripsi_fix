<?php
/**
 * Authentication Functions - Standalone module
 */

// First require database (this has include guard)
require_once __DIR__ . '/database.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if functions already exist
if (function_exists('is_logged_in')) {
    return; // Exit if already loaded
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current logged in user ID
 */
function get_current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current logged in user data
 */
function get_current_user() {
    global $conn;
    
    if (!is_logged_in()) {
        return null;
    }
    
    $user_id = get_current_user_id();
    $query = "SELECT user_id, username, email, full_name, created_at FROM tbl_users WHERE user_id = $user_id LIMIT 1";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

/**
 * Login user
 */
function login_user($username, $password) {
    global $conn;
    
    $username = sanitize($username);
    
    $query = "SELECT user_id, username, password_hash FROM tbl_users WHERE username = '$username' LIMIT 1";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            
            return [
                'success' => true,
                'message' => 'Login berhasil!',
                'user_id' => $user['user_id']
            ];
        } else {
            return [
                'success' => false,
                'message' => '❌ Password salah!',
                'user_id' => null
            ];
        }
    } else {
        return [
            'success' => false,
            'message' => '❌ Username tidak ditemukan!',
            'user_id' => null
        ];
    }
}

/**
 * Register new user
 */
function register_user($username, $email, $password, $full_name = '') {
    global $conn;
    
    $username = sanitize($username);
    $email = sanitize($email);
    $full_name = sanitize($full_name);
    
    if (strlen($username) < 3) {
        return [
            'success' => false,
            'message' => '❌ Username minimal 3 karakter!',
            'user_id' => null
        ];
    }
    
    if (strlen($password) < 6) {
        return [
            'success' => false,
            'message' => '❌ Password minimal 6 karakter!',
            'user_id' => null
        ];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [
            'success' => false,
            'message' => '❌ Email tidak valid!',
            'user_id' => null
        ];
    }
    
    $check_query = "SELECT user_id FROM tbl_users WHERE username = '$username' LIMIT 1";
    $check_result = $conn->query($check_query);
    if ($check_result && $check_result->num_rows > 0) {
        return [
            'success' => false,
            'message' => '❌ Username sudah digunakan!',
            'user_id' => null
        ];
    }
    
    $check_query = "SELECT user_id FROM tbl_users WHERE email = '$email' LIMIT 1";
    $check_result = $conn->query($check_query);
    if ($check_result && $check_result->num_rows > 0) {
        return [
            'success' => false,
            'message' => '❌ Email sudah terdaftar!',
            'user_id' => null
        ];
    }
    
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    
    $insert_query = "INSERT INTO tbl_users (username, email, password_hash, full_name, created_at) 
                     VALUES ('$username', '$email', '$password_hash', '$full_name', NOW())";
    
    if ($conn->query($insert_query)) {
        $user_id = $conn->insert_id;
        
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        
        return [
            'success' => true,
            'message' => '✅ Akun berhasil dibuat! Anda sudah login.',
            'user_id' => $user_id
        ];
    } else {
        return [
            'success' => false,
            'message' => '❌ Error: ' . $conn->error,
            'user_id' => null
        ];
    }
}

/**
 * Logout user
 */
function logout_user() {
    session_destroy();
    unset($_SESSION['user_id']);
    unset($_SESSION['username']);
}

/**
 * Require login - redirect if not authenticated
 */
function require_login() {
    if (!is_logged_in()) {
        redirect('../login.php', 'Silakan login terlebih dahulu untuk mengakses halaman ini.', 'warning');
    }
}




