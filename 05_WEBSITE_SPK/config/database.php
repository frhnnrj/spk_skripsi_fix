<?php
/**
 * Database Configuration & Helper Functions
 * SPK AHP-TOPSIS Investasi Gen Z Indonesia
 */

// Include guard - prevent multiple includes
if (defined('DATABASE_PHP_LOADED')) {
    // Already loaded - exit
    return;
}
define('DATABASE_PHP_LOADED', true);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database credentials
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "spk_investasi";

// Create connection
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");

// ============================================================================
// HELPER FUNCTIONS
// ============================================================================

/**
 * Sanitize input untuk mencegah SQL injection & XSS
 */
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect dengan message
 */
function redirect($location, $message = '', $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
    header("Location: " . $location);
    exit;
}

/**
 * Get all pakar
 */
function get_all_pakar() {
    global $conn;
    $query = "SELECT * FROM tbl_pakar ORDER BY created_at DESC";
    $result = $conn->query($query);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Get pakar by ID
 */
function get_pakar_by_id($pakar_id) {
    global $conn;
    $query = "SELECT * FROM tbl_pakar WHERE pakar_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $pakar_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Get pairwise matrix
 */
function get_pairwise_matrix($pakar_id) {
    global $conn;
    $query = "SELECT * FROM tbl_pairwise_matrix WHERE pakar_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $pakar_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Get decision matrix
 */
function get_decision_matrix($pakar_id) {
    global $conn;
    $query = "SELECT * FROM tbl_decision_matrix WHERE pakar_id = ? ORDER BY alternatif_id";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $pakar_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get AHP results
 */
function get_ahp_results($pakar_id) {
    global $conn;
    $query = "SELECT * FROM tbl_ahp_results WHERE pakar_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $pakar_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Get TOPSIS results
 */
function get_topsis_results($pakar_id) {
    global $conn;
    $query = "SELECT * FROM tbl_topsis_results WHERE pakar_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $pakar_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Get all alternatives
 */
function get_all_alternatives() {
    global $conn;
    $query = "SELECT * FROM tbl_alternatives ORDER BY urutan";
    $result = $conn->query($query);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Get all criteria
 */
function get_all_criteria() {
    global $conn;
    $query = "SELECT * FROM tbl_criteria ORDER BY urutan";
    $result = $conn->query($query);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Log audit
 */
function audit_log($pakar_id, $action, $details = '') {
    global $conn;
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
    $query = "INSERT INTO tbl_audit_log (pakar_id, action, details, ip_address) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("isss", $pakar_id, $action, $details, $ip);
        $stmt->execute();
        $stmt->close();
    }
}

// ============================================================================
// AUTHENTICATION FUNCTIONS (as closures to avoid redeclaration errors)
// ============================================================================

// Create auth function repository
$GLOBALS['auth_functions'] = $GLOBALS['auth_functions'] ?? [];

if (empty($GLOBALS['auth_functions'])) {
    $GLOBALS['auth_functions']['is_logged_in'] = function() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    };
    
    $GLOBALS['auth_functions']['get_current_user_id'] = function() {
        return $_SESSION['user_id'] ?? null;
    };
    
    $GLOBALS['auth_functions']['get_current_user'] = function() {
        if (!isset($GLOBALS['auth_functions']['is_logged_in'])) return null;
        if (!$GLOBALS['auth_functions']['is_logged_in']()) return null;
        
        global $conn;
        $user_id = $_SESSION['user_id'] ?? null;
        
        if (!$user_id || !$conn) {
            return ['user_id' => $user_id, 'username' => $_SESSION['username'] ?? 'User'];
        }
        
        $query = "SELECT user_id, username, email, full_name, created_at FROM tbl_users WHERE user_id = $user_id LIMIT 1";
        $result = @$conn->query($query);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return ['user_id' => $user_id, 'username' => $_SESSION['username'] ?? 'User'];
    };
    
    $GLOBALS['auth_functions']['login_user'] = function($username, $password) {
        global $conn;
        $username = sanitize($username);
        $query = "SELECT user_id, username, password_hash FROM tbl_users WHERE username = '$username' LIMIT 1";
        $result = $conn->query($query);
        
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                return ['success' => true, 'message' => 'Login berhasil!', 'user_id' => $user['user_id']];
            } else {
                return ['success' => false, 'message' => '❌ Password salah!', 'user_id' => null];
            }
        } else {
            return ['success' => false, 'message' => '❌ Username tidak ditemukan!', 'user_id' => null];
        }
    };
    
    $GLOBALS['auth_functions']['register_user'] = function($username, $email, $password, $full_name = '') {
        global $conn;
        $username = sanitize($username);
        $email = sanitize($email);
        $full_name = sanitize($full_name);
        
        if (strlen($username) < 3) {
            return ['success' => false, 'message' => '❌ Username minimal 3 karakter!', 'user_id' => null];
        }
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => '❌ Password minimal 6 karakter!', 'user_id' => null];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => '❌ Email tidak valid!', 'user_id' => null];
        }
        
        $check_query = "SELECT user_id FROM tbl_users WHERE username = '$username' LIMIT 1";
        $check_result = $conn->query($check_query);
        if ($check_result && $check_result->num_rows > 0) {
            return ['success' => false, 'message' => '❌ Username sudah digunakan!', 'user_id' => null];
        }
        
        $check_query = "SELECT user_id FROM tbl_users WHERE email = '$email' LIMIT 1";
        $check_result = $conn->query($check_query);
        if ($check_result && $check_result->num_rows > 0) {
            return ['success' => false, 'message' => '❌ Email sudah terdaftar!', 'user_id' => null];
        }
        
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $insert_query = "INSERT INTO tbl_users (username, email, password_hash, full_name, created_at) 
                         VALUES ('$username', '$email', '$password_hash', '$full_name', NOW())";
        
        if ($conn->query($insert_query)) {
            $user_id = $conn->insert_id;
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            return ['success' => true, 'message' => '✅ Akun berhasil dibuat! Anda sudah login.', 'user_id' => $user_id];
        } else {
            return ['success' => false, 'message' => '❌ Error: ' . $conn->error, 'user_id' => null];
        }
    };
    
    $GLOBALS['auth_functions']['logout_user'] = function() {
        session_destroy();
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
    };
    
    $GLOBALS['auth_functions']['require_login'] = function() {
        if (!isset($GLOBALS['auth_functions']['is_logged_in'])) return;
        if (!$GLOBALS['auth_functions']['is_logged_in']()) {
            redirect('../login.php', 'Silakan login terlebih dahulu untuk mengakses halaman ini.', 'warning');
        }
    };
}

// Wrapper functions that call closures
if (!function_exists('is_logged_in')) {
    function is_logged_in() {
        return $GLOBALS['auth_functions']['is_logged_in']();
    }
}
if (!function_exists('get_current_user_id')) {
    function get_current_user_id() {
        return $GLOBALS['auth_functions']['get_current_user_id']();
    }
}
if (!function_exists('get_current_user')) {
    function get_current_user() {
        return $GLOBALS['auth_functions']['get_current_user']();
    }
}
if (!function_exists('login_user')) {
    function login_user($username, $password) {
        return $GLOBALS['auth_functions']['login_user']($username, $password);
    }
}
if (!function_exists('register_user')) {
    function register_user($username, $email, $password, $full_name = '') {
        return $GLOBALS['auth_functions']['register_user']($username, $email, $password, $full_name);
    }
}
if (!function_exists('logout_user')) {
    function logout_user() {
        return $GLOBALS['auth_functions']['logout_user']();
    }
}
if (!function_exists('require_login')) {
    function require_login() {
        return $GLOBALS['auth_functions']['require_login']();
    }
}

?>
