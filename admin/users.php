<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

if (!isAdmin()) {
    redirect('/life-event-management/dashboard.php');
}

$error = '';
$success = '';

// Handle Delete User
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // Prevent admin from deleting themselves
    if ($id == $_SESSION['user_id']) {
        $error = 'You cannot delete your own account.';
    } else {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'User deleted successfully.';
    }
}

// Handle Toggle Status
if (isset($_GET['toggle'])) {
    $id = $_GET['toggle'];
    if ($id == $_SESSION['user_id']) {
        $error = 'You cannot deactivate your own account.';
    } else {
        $stmt = $pdo->prepare("UPDATE users SET status = IF(status='active','inactive','active') WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'User status updated.';
    }
}

// Handle Role Change
if (isset($_GET['make_admin'])) {
    $id = $_GET['make_admin'];
    $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
    $stmt->execute([$id]);
    $success = 'User promoted to Admin.';
}
if (isset($_GET['remove_admin'])) {
    $id = $_GET['remove_admin'];
    if ($id == $_SESSION['user_id']) {
        $error = 'You cannot remove your own admin role.';
    } else {
        $stmt = $pdo->prepare("UPDATE users SET role = 'user' WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Admin role removed.';
    }
}

// Fetch all users
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Users</h2>
        <a href="dashboard.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Admin Dashboard
        </a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Profile</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td>
                                <?php if (!empty($u['profile_image']) && file_exists('../assets/images/' . $u['profile_image'])): ?>
                                    <img src="../assets/images/<?= htmlspecialchars($u['profile_image']) ?>" width="40" height="40" class="rounded-circle">
                                <?php else: ?>
                                    <i class="fas fa-user-circle fa-2x text-secondary"></i>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($u['name']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= htmlspecialchars($u['phone'] ?? '-') ?></td>
                            <td>
                                <?php if ($u['role'] === 'admin'): ?>
                                    <span class="badge bg-warning text-dark">Admin</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">User</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($u['status'] === 'active'): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                            <td>
                                <!-- Toggle Status -->
                                <a href="?toggle=<?= $u['id'] ?>" class="btn btn-sm btn-warning" title="Toggle Status">
                                    <i class="fas fa-power-off"></i>
                                </a>

                                <!-- Change Role -->
                                <?php if ($u['role'] === 'admin'): ?>
                                    <a href="?remove_admin=<?= $u['id'] ?>" class="btn btn-sm btn-secondary" title="Remove Admin" onclick="return confirm('Remove admin role?')">
                                        <i class="fas fa-user-minus"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="?make_admin=<?= $u['id'] ?>" class="btn btn-sm btn-info" title="Make Admin" onclick="return confirm('Make this user an admin?')">
                                        <i class="fas fa-user-shield"></i>
                                    </a>
                                <?php endif; ?>

                                <!-- Delete -->
                                <a href="?delete=<?= $u['id'] ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this user? All their events will also be deleted.')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>