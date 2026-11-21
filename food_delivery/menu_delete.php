<?php
// DELETE a menu item (POST only). Blocked if existing orders reference it (foreign key).
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: menu.php'); exit; }

$id = (int)($_POST['id'] ?? 0);

$chk = $conn->prepare("SELECT COUNT(*) c FROM orders WHERE menu_item_id = ?");
$chk->bind_param('i', $id);
$chk->execute();
if ((int)$chk->get_result()->fetch_assoc()['c'] > 0) {
    header('Location: menu.php?err=' . urlencode('Cannot delete: this dish has orders. Mark it as unavailable instead.'));
    exit;
}

$stmt = $conn->prepare("DELETE FROM menu_items WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();

$msg = $stmt->affected_rows > 0 ? 'Dish deleted.' : 'Dish not found.';
header('Location: menu.php?msg=' . urlencode($msg));
exit;
