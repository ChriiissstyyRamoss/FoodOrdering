<?php
// DELETE an order (POST only)
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: orders.php'); exit; }

$id = (int)($_POST['id'] ?? 0);
$stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();

$msg = $stmt->affected_rows > 0 ? 'Order #' . $id . ' deleted.' : 'Order not found.';
header('Location: orders.php?msg=' . urlencode($msg));
exit;
