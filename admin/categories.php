<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();
if (!isAdmin()) {
    redirect('/life-event-management/dashboard.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $icon = trim($_POST['icon']);

    if (empty($name)) {
        $error = 'Category name is required.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO event_categories (name, description, icon) VALUES (?, ?, ?)");
        if ($stmt->execute([$name, $description, $icon])) {
            $success = 'Category added.';
        } else {
            $error = 'Failed to add category.';
        }
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM event_categories WHERE id = ?");
    $stmt->execute([$id]);
    redirect('categories.php');
}

$categories = $pdo->query("SELECT * FROM event_categories ORDER BY name")->fetchAll();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-4">
    <h2>Manage Categories</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <div class="row">
        <div class="col-md-4">
            <h4>Add Category</h4>
            <form method="POST">
                <div class="mb-3">
                    <label>Name *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>
                <div class="mb-3">
                    <label>Icon (FontAwesome class)</label>
                    <input type="text" name="icon" class="form-control" placeholder="fa-birthday-cake">
                </div>
                <button type="submit" class="btn btn-primary">Add</button>
            </form>
        </div>
        <div class="col-md-8">
            <h4>Existing Categories</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Icon</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= $cat['id'] ?></td>
                        <td><?= htmlspecialchars($cat['name']) ?></td>
                        <td><?= htmlspecialchars($cat['description']) ?></td>
                        <td><i class="fas <?= htmlspecialchars($cat['icon']) ?>"></i> <?= htmlspecialchars($cat['icon']) ?></td>
                        <td>
                            <a href="?delete=<?= $cat['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete category?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>