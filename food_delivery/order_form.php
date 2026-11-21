<?php
// INSERT (no id) and UPDATE (with ?id=) for orders
require 'db_connect.php';

$statuses = ['Pending', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editing = $id > 0;
$errors = [];

$order = ['customer_name' => '', 'phone' => '', 'address' => '', 'menu_item_id' => '', 'quantity' => 1, 'status' => 'Pending', 'notes' => ''];

if ($editing) {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $found = $stmt->get_result()->fetch_assoc();
    if (!$found) { header('Location: orders.php?err=' . urlencode('Order not found.')); exit; }
    $order = $found;
}

// Menu items the customer can pick (available ones, plus the currently selected one when editing)
$menuStmt = $conn->prepare("SELECT id, name, price, available FROM menu_items WHERE available = 1 OR id = ? ORDER BY name");
$selected = (int)($order['menu_item_id'] ?: 0);
$menuStmt->bind_param('i', $selected);
$menuStmt->execute();
$menuItems = $menuStmt->get_result()->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order['customer_name'] = trim($_POST['customer_name'] ?? '');
    $order['phone']         = trim($_POST['phone'] ?? '');
    $order['address']       = trim($_POST['address'] ?? '');
    $order['menu_item_id']  = (int)($_POST['menu_item_id'] ?? 0);
    $order['quantity']      = (int)($_POST['quantity'] ?? 0);
    $order['status']        = $_POST['status'] ?? 'Pending';
    $order['notes']         = trim($_POST['notes'] ?? '');

    if ($order['customer_name'] === '') $errors[] = 'Customer name is required.';
    if (!preg_match('/^[0-9+\-\s]{7,20}$/', $order['phone'])) $errors[] = 'Enter a valid phone number.';
    if ($order['address'] === '') $errors[] = 'Delivery address is required.';
    if ($order['quantity'] < 1 || $order['quantity'] > 99) $errors[] = 'Quantity must be between 1 and 99.';
    if (!in_array($order['status'], $statuses, true)) $errors[] = 'Invalid status.';

    // Price is always computed on the server from the menu table
    $price = null;
    $p = $conn->prepare("SELECT price FROM menu_items WHERE id = ?");
    $p->bind_param('i', $order['menu_item_id']);
    $p->execute();
    $row = $p->get_result()->fetch_assoc();
    if (!$row) { $errors[] = 'Please choose a menu item.'; } else { $price = (float)$row['price']; }

    if (!$errors) {
        $total = $price * $order['quantity'];
        $notes = $order['notes'] === '' ? null : $order['notes'];
        if ($editing) {
            $stmt = $conn->prepare("UPDATE orders SET customer_name=?, phone=?, address=?, menu_item_id=?, quantity=?, total_price=?, status=?, notes=? WHERE id=?");
            $stmt->bind_param('sssiidssi', $order['customer_name'], $order['phone'], $order['address'], $order['menu_item_id'], $order['quantity'], $total, $order['status'], $notes, $id);
            $msg = 'Order #' . $id . ' updated.';
        } else {
            $stmt = $conn->prepare("INSERT INTO orders (customer_name, phone, address, menu_item_id, quantity, total_price, status, notes) VALUES (?,?,?,?,?,?,?,?)");
            $stmt->bind_param('sssiidss', $order['customer_name'], $order['phone'], $order['address'], $order['menu_item_id'], $order['quantity'], $total, $order['status'], $notes);
            $msg = 'Order placed successfully.';
        }
        if ($stmt->execute()) {
            header('Location: orders.php?msg=' . urlencode($msg));
            exit;
        }
        $errors[] = 'Database error: ' . $stmt->error;
    }
}

$pageTitle = $editing ? 'Edit Order' : 'New Order';
$active = 'orders';
include 'includes/header.php';
?>
<div class="row justify-content-center">
<div class="col-lg-8">
    <div class="card">
        <div class="card-header bg-white fw-semibold"><?= $editing ? 'Edit Order #' . $id : 'Place a New Order' ?></div>
        <div class="card-body">
            <?php foreach ($errors as $err): ?>
                <div class="alert alert-danger py-2"><?= e($err) ?></div>
            <?php endforeach; ?>
            <form method="post" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Customer Name</label>
                    <input type="text" name="customer_name" class="form-control" maxlength="100" required value="<?= e($order['customer_name']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" maxlength="20" required value="<?= e($order['phone']) ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">Delivery Address</label>
                    <input type="text" name="address" class="form-control" maxlength="255" required value="<?= e($order['address']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Menu Item</label>
                    <select name="menu_item_id" class="form-select" required>
                        <option value="">Choose...</option>
                        <?php foreach ($menuItems as $m): ?>
                            <option value="<?= $m['id'] ?>" <?= (int)$order['menu_item_id'] === (int)$m['id'] ? 'selected' : '' ?>>
                                <?= e($m['name']) ?> &mdash; &#8369;<?= number_format($m['price'], 2) ?><?= $m['available'] ? '' : ' (unavailable)' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" class="form-control" min="1" max="99" required value="<?= (int)$order['quantity'] ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <?php foreach ($statuses as $s): ?>
                            <option <?= $order['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Notes <span class="text-muted">(optional)</span></label>
                    <input type="text" name="notes" class="form-control" maxlength="255" value="<?= e($order['notes']) ?>">
                </div>
                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-brand"><i class="bi bi-check-lg"></i> <?= $editing ? 'Save Changes' : 'Place Order' ?></button>
                    <a href="orders.php" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
<?php include 'includes/footer.php'; ?>
