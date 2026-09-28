<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

$user_id = $_SESSION['user_id'];

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$sql = "SELECT e.*, c.name as category_name FROM events e 
        LEFT JOIN event_categories c ON e.category_id = c.id 
        WHERE e.user_id = ?";
$params = [$user_id];

if ($search) {
    $sql .= " AND (e.title LIKE ? OR e.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($category) {
    $sql .= " AND e.category_id = ?";
    $params[] = $category;
}
$sql .= " ORDER BY e.event_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll();

$cats = $pdo->query("SELECT * FROM event_categories")->fetchAll();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-4">
    <h2>My Events</h2>
    <div class="d-flex justify-content-between mb-3">
        <a href="add.php" class="btn btn-primary">Add New Event</a>
    </div>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">All Categories</option>
                <?php foreach ($cats as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $category == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-secondary">Filter</button>
        </div>
    </form>

    <?php if (count($events) > 0): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event): ?>
                <tr>
                    <td><?= htmlspecialchars($event['title']) ?></td>
                    <td><?= htmlspecialchars($event['category_name'] ?? 'Uncategorized') ?></td>
                    <td><?= htmlspecialchars($event['event_date']) ?></td>
                    <td><?= htmlspecialchars($event['event_time'] ?? '') ?></td>
                    <td><?= htmlspecialchars($event['location'] ?? '') ?></td>
                    <td>
                        <a href="view.php?id=<?= $event['id'] ?>" class="btn btn-sm btn-info">View</a>
                        <a href="edit.php?id=<?= $event['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete.php?id=<?= $event['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this event?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No events found.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>