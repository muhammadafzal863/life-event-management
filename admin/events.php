<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();
if (!isAdmin()) {
    redirect('/life-event-management/dashboard.php');
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$id]);
    redirect('events.php');
}

$events = $pdo->query("SELECT e.*, u.name as user_name, c.name as category_name 
                       FROM events e 
                       LEFT JOIN users u ON e.user_id = u.id 
                       LEFT JOIN event_categories c ON e.category_id = c.id 
                       ORDER BY e.event_date DESC")->fetchAll();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-4">
    <h2>Manage Events</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>User</th>
                <th>Category</th>
                <th>Date</th>
                <th>Visibility</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $e): ?>
            <tr>
                <td><?= $e['id'] ?></td>
                <td><?= htmlspecialchars($e['title']) ?></td>
                <td><?= htmlspecialchars($e['user_name']) ?></td>
                <td><?= htmlspecialchars($e['category_name'] ?? 'Uncategorized') ?></td>
                <td><?= $e['event_date'] ?></td>
                <td><?= $e['visibility'] ?></td>
                <td>
                    <a href="/life-event-management/events/view.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-info">View</a>
                    <a href="?delete=<?= $e['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete event?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>