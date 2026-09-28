<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

if (!isAdmin()) {
    redirect('/life-event-management/dashboard.php');
}

$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_events = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
$total_categories = $pdo->query("SELECT COUNT(*) FROM event_categories")->fetchColumn();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-4">
    <h2 class="mb-4">Admin Dashboard</h2>

    <div class="row">
        <!-- Total Users Box (Clickable) -->
        <div class="col-md-4">
            <a href="users.php" class="text-decoration-none">
                <div class="card text-white bg-primary mb-3 shadow-sm" style="transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-users"></i> Total Users</h5>
                        <p class="card-text display-4"><?= $total_users ?></p>
                        <small>Click to manage users &rarr;</small>
                    </div>
                </div>
            </a>
        </div>

        <!-- Total Events Box (Clickable) -->
        <div class="col-md-4">
            <a href="events.php" class="text-decoration-none">
                <div class="card text-white bg-success mb-3 shadow-sm" style="transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-calendar-alt"></i> Total Events</h5>
                        <p class="card-text display-4"><?= $total_events ?></p>
                        <small>Click to manage events &rarr;</small>
                    </div>
                </div>
            </a>
        </div>

        <!-- Categories Box (Clickable) -->
        <div class="col-md-4">
            <a href="categories.php" class="text-decoration-none">
                <div class="card text-white bg-info mb-3 shadow-sm" style="transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-tags"></i> Categories</h5>
                        <p class="card-text display-4"><?= $total_categories ?></p>
                        <small>Click to manage categories &rarr;</small>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>