<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Fetch categories for colors
$cats = $pdo->query("SELECT * FROM event_categories")->fetchAll();
$colors = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0', '#6c757d', '#d63384', '#fd7e14', '#20c997', '#6f42c1'];
$cat_colors = [];
$i = 0;
foreach ($cats as $cat) {
    $cat_colors[$cat['id']] = $colors[$i % count($colors)];
    $i++;
}

// Upcoming events
$stmt = $pdo->prepare("SELECT e.*, c.name as category_name FROM events e 
                       LEFT JOIN event_categories c ON e.category_id = c.id 
                       WHERE e.user_id = ? AND e.event_date >= CURDATE() 
                       ORDER BY e.event_date ASC LIMIT 10");
$stmt->execute([$user_id]);
$upcoming = $stmt->fetchAll();

// Stats
$stmt = $pdo->prepare("SELECT COUNT(*) as total, 
                       SUM(MONTH(event_date) = MONTH(CURDATE()) AND YEAR(event_date) = YEAR(CURDATE())) as this_month 
                       FROM events WHERE user_id = ?");
$stmt->execute([$user_id]);
$stats = $stmt->fetch();

// Upcoming Reminders
$stmt = $pdo->prepare("SELECT r.*, e.title, e.event_date, e.id as event_id 
                       FROM reminders r 
                       JOIN events e ON r.event_id = e.id 
                       WHERE r.user_id = ? AND r.status = 'pending' AND r.reminder_date >= NOW() 
                       ORDER BY r.reminder_date ASC LIMIT 5");
$stmt->execute([$user_id]);
$reminders = $stmt->fetchAll();

// Recently Added
$stmt = $pdo->prepare("SELECT e.*, c.name as category_name FROM events e 
                       LEFT JOIN event_categories c ON e.category_id = c.id 
                       WHERE e.user_id = ? 
                       ORDER BY e.created_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$recent = $stmt->fetchAll();

include 'includes/header.php';
include 'includes/navbar.php';
?>

<link href="https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;700&display=swap" rel="stylesheet">

<style>
    /* ============================================
       COMPLETE WHITE BACKGROUND
       ============================================ */
    html, body {
        min-height: 100vh;
    }
    body {
        background: #ffffff !important;
        color: #212529;
        min-height: 100vh;
    }

    /* Navbar stays blue (default) - keep as is */
    /* Page Headings */
    h2.page-title {
        color: #212529;
    }
    p.welcome-text {
        color: #495057;
    }

    /* Section titles */
    h3.section-title {
        border-left: 4px solid #ffc107;
        padding-left: 12px;
        margin-top: 30px;
        margin-bottom: 20px;
        color: #212529;
    }

    /* Cards (general) */
    .card {
        background-color: #ffffff;
        border: 1px solid #e9ecef;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        color: #212529;
    }

    /* Stat Cards */
    .stat-card {
        border-radius: 12px;
        border: none;
        overflow: hidden;
        position: relative;
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 150%;
        height: 150%;
        background: radial-gradient(circle, rgba(255,255,255,0.15), transparent 70%);
        pointer-events: none;
    }
    .stat-card .display-4 {
        font-weight: bold;
        text-shadow: 0 2px 6px rgba(0,0,0,0.3);
    }

    /* Quick Action Buttons */
    .quick-action {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 20px 10px;
        border-radius: 12px;
        background: #ffffff;
        border: 1px solid #e9ecef;
        color: #212529;
        text-decoration: none;
        transition: all 0.25s ease;
        height: 100%;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    .quick-action:hover {
        transform: translateY(-5px);
        color: #0d6efd;
        border-color: #0d6efd;
        box-shadow: 0 8px 25px rgba(13,110,253,0.25);
    }
    .quick-action i {
        font-size: 2rem;
        margin-bottom: 10px;
        color: #0d6efd;
    }
    .quick-action:hover i { color: #6610f2; }
    .quick-action span {
        font-weight: 600;
        font-size: 0.95rem;
    }

    /* Event Cards */
    .event-card {
        background-color: #ffffff;
        border-left: 5px solid #0d6efd;
        border-radius: 10px;
        padding: 15px 18px;
        margin-bottom: 15px;
        transition: all 0.25s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        border-left-width: 5px;
    }
    .event-card:hover {
        transform: translateX(5px);
        box-shadow: 0 8px 25px rgba(13,110,253,0.25);
    }
    .event-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #212529;
        text-decoration: none;
    }
    .event-title:hover { color: #0d6efd; }

    .event-meta {
        font-size: 0.88rem;
        color: #495057;
        margin-top: 4px;
    }
    .event-meta i { width: 18px; color: #0d6efd; }

    .event-date-badge {
        border-radius: 8px;
        padding: 8px 14px;
        text-align: center;
        min-width: 80px;
        color: #fff;
    }
    .event-date-badge .day {
        font-size: 1.6rem;
        font-weight: bold;
        line-height: 1;
    }
    .event-date-badge .month {
        font-size: 0.75rem;
        text-transform: uppercase;
        opacity: 0.9;
    }
    .event-date-badge .hijri {
        font-family: 'Noto Nastaliq Urdu', serif;
        font-size: 0.75rem;
        color: #ffc107;
        direction: rtl;
        display: block;
        margin-top: 4px;
    }

    .category-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        color: #fff;
        font-weight: 500;
    }

    .btn-view {
        background: linear-gradient(135deg, #0dcaf0, #0d6efd);
        border: none;
        color: #fff;
        font-weight: 500;
        border-radius: 8px;
        padding: 6px 16px;
    }
    .btn-view:hover {
        background: linear-gradient(135deg, #0d6efd, #6610f2);
        color: #fff;
    }

    /* Reminder Card */
    .reminder-card {
        background: #ffffff;
        border-left: 5px solid #ffc107;
        border-radius: 10px;
        padding: 12px 16px;
        margin-bottom: 12px;
        transition: all 0.25s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        border-left-width: 5px;
    }
    .reminder-card:hover {
        transform: translateX(5px);
        box-shadow: 0 6px 20px rgba(255,193,7,0.35);
    }
    .reminder-icon {
        font-size: 1.8rem;
        color: #dc3545;
        margin-right: 12px;
    }
    .reminder-title {
        font-weight: 600;
        color: #212529;
        font-size: 1rem;
    }
    .reminder-time {
        font-size: 0.85rem;
        color: #6c757d;
    }

    /* Recently Added Card */
    .recent-card {
        background: #ffffff;
        border-radius: 10px;
        padding: 12px 15px;
        margin-bottom: 10px;
        border: 1px solid #e9ecef;
        transition: all 0.25s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    .recent-card:hover {
        background-color: #f8f9fa;
        border-color: #0dcaf0;
        box-shadow: 0 6px 20px rgba(13,202,240,0.25);
    }
    .recent-title {
        color: #212529;
        text-decoration: none;
        font-weight: 600;
    }
    .recent-title:hover { color: #0d6efd; }
    .recent-meta {
        font-size: 0.8rem;
        color: #6c757d;
    }

    /* Empty state */
    .no-events {
        text-align: center;
        padding: 30px 20px;
        color: #495057;
        background: #f8f9fa;
        border-radius: 12px;
        border: 2px dashed #adb5bd;
    }
    .no-events i {
        font-size: 2.5rem;
        color: #adb5bd;
        margin-bottom: 10px;
    }

    /* Section Badge */
    .section-badge {
        background: #ffc107;
        color: #000;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: bold;
        margin-left: 8px;
    }
</style>

<div class="container mt-4">
    <h2 class="page-title"><i class="fas fa-tachometer-alt text-primary"></i> Dashboard</h2>
    <p class="welcome-text">Welcome back, <strong class="text-primary"><?= htmlspecialchars($_SESSION['user_name']) ?></strong>!</p>

    <!-- ============ STATS CARDS ============ -->
    <div class="row mt-4">
        <div class="col-md-3 mb-3">
            <div class="card stat-card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-calendar-check"></i> Total Events</h5>
                    <p class="card-text display-4"><?= $stats['total'] ?? 0 ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-calendar-day"></i> This Month</h5>
                    <p class="card-text display-4"><?= $stats['this_month'] ?? 0 ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-hourglass-half"></i> Upcoming</h5>
                    <p class="card-text display-4"><?= count($upcoming) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-bell"></i> Reminders</h5>
                    <p class="card-text display-4"><?= count($reminders) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- ============ QUICK ACTIONS ============ -->
    <h3 class="section-title"><i class="fas fa-bolt text-warning"></i> Quick Actions</h3>
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <a href="/life-event-management/events/add.php" class="quick-action">
                <i class="fas fa-plus-circle"></i>
                <span>Add New Event</span>
            </a>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <a href="/life-event-management/events/index.php" class="quick-action">
                <i class="fas fa-list"></i>
                <span>My Events</span>
            </a>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <a href="/life-event-management/calendar.php" class="quick-action">
                <i class="fas fa-calendar-alt"></i>
                <span>Calendar</span>
            </a>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <a href="/life-event-management/profile/index.php" class="quick-action">
                <i class="fas fa-user-circle"></i>
                <span>My Profile</span>
            </a>
        </div>
    </div>

    <div class="row">
        <!-- ============ UPCOMING EVENTS ============ -->
        <div class="col-lg-7">
            <h3 class="section-title">
                <i class="fas fa-calendar-week text-primary"></i> Upcoming Events
                <span class="section-badge"><?= count($upcoming) ?></span>
            </h3>

            <?php if (count($upcoming) > 0): ?>
                <?php foreach ($upcoming as $event): 
                    $catColor = $cat_colors[$event['category_id']] ?? '#6c757d';
                    $dateObj = strtotime($event['event_date']);
                ?>
                    <div class="event-card" style="border-left-color: <?= $catColor ?>;">
                        <div class="d-flex justify-content-between align-items-start flex-wrap">
                            <div class="d-flex align-items-center flex-grow-1">
                                <div class="event-date-badge me-3" style="background: linear-gradient(135deg, <?= $catColor ?>, #000);">
                                    <div class="day"><?= date('d', $dateObj) ?></div>
                                    <div class="month"><?= date('M Y', $dateObj) ?></div>
                                    <span class="hijri" data-greg-date="<?= $event['event_date'] ?>">...</span>
                                </div>
                                <div>
                                    <a href="/life-event-management/events/view.php?id=<?= $event['id'] ?>" class="event-title">
                                        <?= htmlspecialchars($event['title']) ?>
                                    </a>
                                    <div class="event-meta mt-1">
                                        <span class="category-badge" style="background-color: <?= $catColor ?>;">
                                            <?= htmlspecialchars($event['category_name'] ?? 'Uncategorized') ?>
                                        </span>
                                    </div>
                                    <div class="event-meta mt-2">
                                        <?php if ($event['event_time']): ?>
                                            <div><i class="fas fa-clock"></i> <?= date('h:i A', strtotime($event['event_time'])) ?></div>
                                        <?php endif; ?>
                                        <?php if ($event['location']): ?>
                                            <div><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['location']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 mt-md-0">
                                <a href="/life-event-management/events/view.php?id=<?= $event['id'] ?>" class="btn btn-view btn-sm">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-events">
                    <i class="fas fa-calendar-times"></i>
                    <h5>No upcoming events</h5>
                    <p>Add your first event to see it here!</p>
                    <a href="/life-event-management/events/add.php" class="btn btn-primary mt-2">
                        <i class="fas fa-plus"></i> Add New Event
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- ============ RIGHT COLUMN ============ -->
        <div class="col-lg-5">

            <!-- Reminders -->
            <h3 class="section-title">
                <i class="fas fa-bell text-danger"></i> Upcoming Reminders
                <span class="section-badge"><?= count($reminders) ?></span>
            </h3>

            <?php if (count($reminders) > 0): ?>
                <?php foreach ($reminders as $rem): ?>
                    <div class="reminder-card">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-bell reminder-icon"></i>
                            <div class="flex-grow-1">
                                <a href="/life-event-management/events/view.php?id=<?= $rem['event_id'] ?>" class="text-decoration-none">
                                    <div class="reminder-title"><?= htmlspecialchars($rem['title']) ?></div>
                                </a>
                                <div class="reminder-time mt-1">
                                    <i class="fas fa-clock"></i> Reminder: 
                                    <strong class="text-danger"><?= date('M d, Y h:i A', strtotime($rem['reminder_date'])) ?></strong>
                                </div>
                                <div class="reminder-time">
                                    <i class="fas fa-calendar"></i> Event: <?= date('M d, Y', strtotime($rem['event_date'])) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-events">
                    <i class="fas fa-bell-slash"></i>
                    <p class="mb-0">No upcoming reminders</p>
                    <small>Set reminders when adding events</small>
                </div>
            <?php endif; ?>

            <!-- Recently Added -->
            <h3 class="section-title">
                <i class="fas fa-history text-primary"></i> Recently Added
                <span class="section-badge"><?= count($recent) ?></span>
            </h3>

            <?php if (count($recent) > 0): ?>
                <?php foreach ($recent as $rec): 
                    $catColor = $cat_colors[$rec['category_id']] ?? '#6c757d';
                ?>
                    <div class="recent-card" style="border-left: 4px solid <?= $catColor ?>;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="/life-event-management/events/view.php?id=<?= $rec['id'] ?>" class="recent-title">
                                    <?= htmlspecialchars($rec['title']) ?>
                                </a>
                                <div class="recent-meta mt-1">
                                    <span class="category-badge" style="background-color: <?= $catColor ?>;">
                                        <?= htmlspecialchars($rec['category_name'] ?? 'Uncategorized') ?>
                                    </span>
                                    <span class="ms-2">
                                        <i class="fas fa-plus-circle"></i> 
                                        <?= date('M d, Y', strtotime($rec['created_at'])) ?>
                                    </span>
                                </div>
                            </div>
                            <a href="/life-event-management/events/view.php?id=<?= $rec['id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-events">
                    <i class="fas fa-inbox"></i>
                    <p class="mb-0">No events added yet</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- Urdu Islamic Dates Script -->
<script>
const hijriMonthsUrdu = {
    1: 'محرم', 2: 'صفر', 3: 'ربیع الاول', 4: 'ربیع الثانی',
    5: 'جمادی الاول', 6: 'جمادی الثانی', 7: 'رجب', 8: 'شعبان',
    9: 'رمضان', 10: 'شوال', 11: 'ذوالقعدہ', 12: 'ذوالحجہ'
};

function getHijriUrdu(date) {
    try {
        var parts = new Intl.DateTimeFormat('en-TN-u-ca-islamic-nu-latn', {
            day: 'numeric', month: 'numeric'
        }).formatToParts(date);
        var day = '', month = '';
        parts.forEach(function(p) {
            if (p.type === 'day') day = p.value;
            if (p.type === 'month') month = p.value;
        });
        return day + ' ' + (hijriMonthsUrdu[parseInt(month)] || '');
    } catch (err) { return ''; }
}

document.addEventListener('DOMContentLoaded', function() {
    var elements = document.querySelectorAll('.hijri[data-greg-date]');
    elements.forEach(function(el) {
        var gregDate = el.getAttribute('data-greg-date');
        el.textContent = getHijriUrdu(new Date(gregDate)) || '';
    });
});
</script>

<?php include 'includes/footer.php'; ?>