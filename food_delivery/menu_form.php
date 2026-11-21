<?php
// INSERT (no id) and UPDATE (with ?id=) for menu items
require 'db_connect.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editing = $id > 0;
$errors = [];
$item = ['name' => '', 'category' => 'Main Dish', 'description' => '', 'price' => '', 'available' => 1];

if ($editing) {
    $stmt = $conn->prepare("SELECT * FROM menu_items WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $found = $stmt->get_result()->fetch_assoc();
    if (!$found) { header('Location: menu.php?err=' . urlencode('Dish not found.')); exit; }
    $item = $found;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item['name']        = trim($_POST['name'] ?? '');
    $item['category']    = trim($_POST['category'] ?? '');
    $item['description'] = trim($_POST['description'] ?? '');
    $item['price']       = $_POST['price'] ?? '';
    $item['available']   = isset($_POST['available']) ? 1 : 0;

    if ($item['name'] === '') $errors[] = 'Dish name is required.';
    if ($item['category'] === '') $errors[] = 'Category is required.';
    if (!is_numeric($item['price']) || $item['price'] <= 0) $errors[] = 'Price must be a positive number.';

    if (!$errors) {
        $price = (float)$item['price'];
        if ($editing) {
            $stmt = $conn->prepare("UPDATE menu_items SET name=?, category=?, description=?, price=?, available=? WHERE id=?");
            $stmt->bind_param('sssdii', $item['name'], $item['category'], $item['description'], $price, $item['available'], $id);
            $msg = 'Dish updated.';
        } else {
            $stmt = $conn->prepare("INSERT INTO menu_items (name, category, description, price, available) VALUES (?,?,?,?,?)");
            $stmt->bind_param('sssdi', $item['name'], $item['category'], $item['description'], $price, $item['available']);
            $msg = 'Dish added to the menu.';
        }
        if ($stmt->execute()) {
            header('Location: menu.php?msg=' . urlencode($msg));
            exit;
        }
        $errors[] = 'Database error: ' . $stmt->error;
    }
}

$pageTitle = $editing ? 'Edit Dish' : 'Add Dish';
$active = 'menu';
include 'includes/header.php';
?>
<div class="row justify-content-center">
<div class="col-lg-7">
    <div class="card">
        <div class="card-header bg-white fw-semibold"><?= $editing ? 'Edit Dish' : 'Add a New Dish' ?></div>
        <div class="card-body">
            <?php foreach ($errors as $err): ?>
                <div class="alert alert-danger py-2"><?= e($err) ?></div>
            <?php endforeach; ?>
            <form method="post" class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Dish Name</label>
                    <input type="text" name="name" class="form-control" maxlength="100" required value="<?= e($item['name']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Price (&#8369;)</label>
                    <input type="number" step="0.01" min="0.01" name="price" class="form-control" required value="<?= e($item['price']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-control" maxlength="50" list="cats" required value="<?= e($item['category']) ?>">
                    <datalist id="cats"><option>Main Dish</option><option>Burgers</option><option>Noodles</option><option>Dessert</option><option>Drinks</option></datalist>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" name="available" id="av" <?= $item['available'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="av">Available for ordering</label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"><?= e($item['description']) ?></textarea>
                </div>
                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-brand"><i class="bi bi-check-lg"></i> <?= $editing ? 'Save Changes' : 'Add Dish' ?></button>
                    <a href="menu.php" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
<?php include 'includes/footer.php'; ?>
