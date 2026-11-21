<?php
require 'db_connect.php';
$pageTitle = 'Dashboard';
$active = 'home';

$totalOrders  = (int)$conn->query("SELECT COUNT(*) c FROM orders")->fetch_assoc()['c'];
$pending      = (int)$conn->query("SELECT COUNT(*) c FROM orders WHERE status IN ('Pending','Preparing','Out for Delivery')")->fetch_assoc()['c'];
$revenue      = (float)$conn->query("SELECT COALESCE(SUM(total_price),0) s FROM orders WHERE status='Delivered'")->fetch_assoc()['s'];
$menuCount    = (int)$conn->query("SELECT COUNT(*) c FROM menu_items WHERE available=1")->fetch_assoc()['c'];

$recent = $conn->query("SELECT o.*, m.name AS item FROM orders o JOIN menu_items m ON m.id=o.menu_item_id ORDER BY o.order_date DESC, o.id DESC LIMIT 5");

include 'includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h1 class="h3 mb-0">Dashboard</h1>
        <p class="text-muted mb-0">Overview of your food orders and deliveries.</p>
    </div>
    <a href="order_form.php" class="btn btn-brand"><i class="bi bi-plus-lg"></i> New Order</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3"><div class="card stat-card p-3"><i class="bi bi-receipt stat-icon"></i><div class="h3 mb-0"><?= $totalOrders ?></div><div class="text-muted">Total Orders</div></div></div>
    <div class="col-6 col-lg-3"><div class="card stat-card p-3"><i class="bi bi-bicycle stat-icon"></i><div class="h3 mb-0"><?= $pending ?></div><div class="text-muted">In Progress</div></div></div>
    <div class="col-6 col-lg-3"><div class="card stat-card p-3"><i class="bi bi-cash-coin stat-icon"></i><div class="h3 mb-0">&#8369;<?= number_format($revenue, 2) ?></div><div class="text-muted">Delivered Revenue</div></div></div>
    <div class="col-6 col-lg-3"><div class="card stat-card p-3"><i class="bi bi-egg-fried stat-icon"></i><div class="h3 mb-0"><?= $menuCount ?></div><div class="text-muted">Available Dishes</div></div></div>
</div>

<div class="card">
    <div class="card-header bg-white fw-semibold">Recent Orders</div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>#</th><th>Customer</th><th>Item</th><th>Qty</th><th>Total</th><th>Status</th></tr></thead>
            <tbody>
            <?php if ($recent->num_rows === 0): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">No orders yet.</td></tr>
            <?php endif; ?>
            <?php while ($r = $recent->fetch_assoc()): ?>
                <tr>
                    <td><?= $r['id'] ?></td>
                    <td><?= e($r['customer_name']) ?></td>
                    <td><?= e($r['item']) ?></td>
                    <td><?= $r['quantity'] ?></td>
                    <td>&#8369;<?= number_format($r['total_price'], 2) ?></td>
                    <td><?php include 'includes/status_badge.php'; ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
