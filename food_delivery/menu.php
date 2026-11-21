<?php
// SELECT: list menu items
require 'db_connect.php';
$pageTitle = 'Menu';
$active = 'menu';
$items = $conn->query("SELECT * FROM menu_items ORDER BY category, name");
include 'includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h1 class="h3 mb-0">Menu</h1>
    <a href="menu_form.php" class="btn btn-brand"><i class="bi bi-plus-lg"></i> Add Dish</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>#</th><th>Name</th><th>Category</th><th>Description</th><th>Price</th><th>Availability</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            <?php if ($items->num_rows === 0): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">No dishes yet.</td></tr>
            <?php endif; ?>
            <?php while ($m = $items->fetch_assoc()): ?>
                <tr>
                    <td><?= $m['id'] ?></td>
                    <td class="fw-semibold"><?= e($m['name']) ?></td>
                    <td><?= e($m['category']) ?></td>
                    <td class="text-muted small"><?= e($m['description']) ?></td>
                    <td>&#8369;<?= number_format($m['price'], 2) ?></td>
                    <td><?= $m['available'] ? '<span class="badge bg-success">Available</span>' : '<span class="badge bg-secondary">Sold out</span>' ?></td>
                    <td class="text-end text-nowrap">
                        <a href="menu_form.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil-square"></i></a>
                        <form action="menu_delete.php" method="post" class="d-inline" onsubmit="return confirm('Delete this dish?');">
                            <input type="hidden" name="id" value="<?= $m['id'] ?>">
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
