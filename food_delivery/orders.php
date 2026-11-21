<?php
// SELECT: list all orders (with optional search + status filter)
require 'db_connect.php';
$pageTitle = 'Orders';
$active = 'orders';

$search = trim($_GET['q'] ?? '');
$status = $_GET['status'] ?? '';
$statuses = ['Pending', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled'];

$sql = "SELECT o.*, m.name AS item
        FROM orders o JOIN menu_items m ON m.id = o.menu_item_id
        WHERE (o.customer_name LIKE ? OR o.phone LIKE ? OR m.name LIKE ?)";
$like = '%' . $search . '%';
$types = 'sss';
$params = [$like, $like, $like];
if (in_array($status, $statuses, true)) {
    $sql .= " AND o.status = ?";
    $types .= 's';
    $params[] = $status;
}
$sql .= " ORDER BY o.order_date DESC, o.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

include 'includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h1 class="h3 mb-0">Orders</h1>
    <a href="order_form.php" class="btn btn-brand"><i class="bi bi-plus-lg"></i> New Order</a>
</div>

<form class="row g-2 mb-3" method="get">
    <div class="col-md-6"><input type="text" name="q" class="form-control" placeholder="Search customer, phone or item..." value="<?= e($search) ?>"></div>
    <div class="col-md-3">
        <select name="status" class="form-select">
            <option value="">All statuses</option>
            <?php foreach ($statuses as $s): ?>
                <option <?= $status === $s ? 'selected' : '' ?>><?= e($s) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3 d-grid d-md-flex gap-2">
        <button class="btn btn-dark"><i class="bi bi-search"></i> Filter</button>
        <a href="orders.php" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr><th>#</th><th>Customer</th><th>Phone</th><th>Address</th><th>Item</th><th>Qty</th><th>Total</th><th>Status</th><th>Date</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
            <?php if ($result->num_rows === 0): ?>
                <tr><td colspan="10" class="text-center text-muted py-4">No orders found.</td></tr>
            <?php endif; ?>
            <?php while ($r = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $r['id'] ?></td>
                    <td><?= e($r['customer_name']) ?></td>
                    <td><?= e($r['phone']) ?></td>
                    <td><?= e($r['address']) ?></td>
                    <td><?= e($r['item']) ?></td>
                    <td><?= $r['quantity'] ?></td>
                    <td>&#8369;<?= number_format($r['total_price'], 2) ?></td>
                    <td><?php include 'includes/status_badge.php'; ?></td>
                    <td class="small text-muted"><?= date('M d, Y h:i A', strtotime($r['order_date'])) ?></td>
                    <td class="text-end text-nowrap">
                        <a href="order_form.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil-square"></i></a>
                        <form action="order_delete.php" method="post" class="d-inline" onsubmit="return confirm('Delete order #<?= $r['id'] ?>?');">
                            <input type="hidden" name="id" value="<?= $r['id'] ?>">
                            <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
