<?php
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../index.php', 'Invalid request', 'danger');
}

$pakar_id = intval($_POST['pakar_id'] ?? 0);

if (!$pakar_id) {
    redirect('../index.php', 'Pakar ID tidak valid', 'danger');
}

$pakar = get_pakar_by_id($pakar_id);
if (!$pakar) {
    redirect('../index.php', 'Pakar tidak ditemukan', 'danger');
}

// Delete pakar - cascade delete akan menghapus semua data terkait
$query = "DELETE FROM tbl_pakar WHERE pakar_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $pakar_id);

if ($stmt->execute()) {
    audit_log($pakar_id, 'DELETE_PAKAR', "Deleted pakar: " . $pakar['pakar_nama']);
    redirect('../index.php', '✅ Pakar berhasil dihapus!', 'success');
} else {
    redirect('../index.php', '❌ Error: ' . $stmt->error, 'danger');
}

$stmt->close();
?>
