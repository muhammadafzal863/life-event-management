<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Fetch Categories
$cats = $pdo->query("SELECT * FROM event_categories")->fetchAll();

// Color palette for events
$colors = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0', '#6c757d', '#d63384', '#fd7e14', '#20c997', '#6f42c1'];
$cat_colors = [];
$i = 0;
foreach ($cats as $cat) {
    $cat_colors[$cat['id']] = $colors[$i % count($colors)];
    $i++;
}

// Fetch Events
$stmt = $pdo->prepare("SELECT e.id, e.title, e.event_date, e.event_time, e.location, e.category_id, c.name as category_name 
                       FROM events e 
                       LEFT JOIN event_categories c ON e.category_id = c.id 
                       WHERE e.user_id = ? 
                       ORDER BY e.event_date ASC");
$stmt->execute([$user_id]);
$events = $stmt->fetchAll();

$events_js = [];
foreach ($events as $e) {
    $color = $cat_colors[$e['category_id']] ?? '#6c757d';
    $events_js[] = [
        'id' => $e['id'],
        'title' => $e['title'],
        'start' => $e['event_date'] . ($e['event_time'] ? 'T' . $e['event_time'] : ''),
        'url' => '/life-event-management/events/view.php?id=' . $e['id'],
        'backgroundColor' => $color,
        'borderColor' => $color,
        'textColor' => '#fff'
    ];
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Urdu Font -->
<link href="https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;700&display=swap" rel="stylesheet">

<style>
    /* ============================================
       DARK THEME - Half Black Body
       ============================================ */
    body {
        background: linear-gradient(135deg, #0f0f0f 0%, #1a1a1a 50%, #2b2b2b 100%) !important;
        color: #e0e0e0;
        min-height: 100vh;
    }

    /* Navbar Dark Version */
    .navbar {
        background: linear-gradient(90deg, #000000, #1a1a1a) !important;
        box-shadow: 0 2px 10px rgba(0,0,0,0.5);
    }
    .navbar-brand, .navbar .nav-link {
        color: #ffffff !important;
    }
    .navbar .nav-link:hover {
        color: #ffc107 !important;
    }

    h2 {
        color: #ffffff;
    }

    /* Cards */
    .card {
        background-color: #1e1e1e;
        border: 1px solid #333;
        box-shadow: 0 4px 20px rgba(0,0,0,0.6);
        color: #e0e0e0;
    }
    .card-header {
        border-bottom: 1px solid #333;
    }

    /* Calendar Wrapper */
    #calendar {
        min-height: 700px;
        background-color: #1e1e1e;
        padding: 10px;
        border-radius: 8px;
    }

    /* Hijri Urdu text */
    .hijri-urdu {
        font-family: 'Noto Nastaliq Urdu', serif;
        font-size: 10px;
        color: #000000;
        margin-top: 2px;
        line-height: 1.2;
        direction: rtl;
        font-weight: 700;
    }

    /* FullCalendar Dark Overrides */
    .fc {
        color: #e0e0e0;
    }
    .fc-toolbar-title {
        font-size: 1.5rem !important;
        font-weight: bold;
        color: #ffffff !important;
    }
    .fc-toolbar-title .hijri-title {
        font-family: 'Noto Nastaliq Urdu', serif;
        font-size: 1.1rem;
        color: #ffc107 !important;
        display: block;
        direction: rtl;
        margin-top: 4px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.6);
    }
    .fc-col-header-cell {
        background-color: #0d0d0d !important;
        color: #ffc107 !important;
        padding: 8px 0;
    }
    .fc-col-header-cell-cushion {
        color: #ffc107 !important;
        text-decoration: none;
        font-weight: bold;
    }

    /* ⭐ DATE NUMBERS - NOW BLACK ⭐ */
    .fc-daygrid-day-number {
        font-weight: bold;
        color: #000000 !important;
        font-size: 15px;
    }

    .fc-daygrid-day {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .fc-daygrid-day:hover {
        transform: scale(1.03);
        box-shadow: 0 4px 14px rgba(255,255,255,0.25);
        z-index: 5;
        position: relative;
    }
    .fc-daygrid-day-frame {
        min-height: 100px;
    }
    .fc-scrollgrid {
        border-color: #333 !important;
    }
    .fc-scrollgrid td, .fc-scrollgrid th {
        border-color: #333 !important;
    }
    .fc-theme-standard .fc-scrollgrid {
        border: 1px solid #333 !important;
    }

    /* Buttons */
    .fc-button {
        background-color: #0d6efd !important;
        border-color: #0d6efd !important;
        color: #fff !important;
    }
    .fc-button:hover {
        background-color: #0b5ed7 !important;
        border-color: #0a58ca !important;
    }
    .fc-button-active {
        background-color: #ffc107 !important;
        border-color: #ffc107 !important;
        color: #000 !important;
    }

    /* Table Dark Theme */
    .year-overview-table {
        background-color: #1e1e1e;
        color: #e0e0e0;
    }
    .year-overview-table th {
        background: linear-gradient(135deg, #0d6efd, #6610f2);
        color: white;
        border-color: #333 !important;
    }
    .year-overview-table td {
        vertical-align: middle;
        border-color: #333 !important;
    }
    .hijri-cell {
        font-family: 'Noto Nastaliq Urdu', serif;
        font-size: 15px;
        direction: rtl;
        color: #ffc107;
        font-weight: bold;
    }

    .year-overview-table tbody tr:nth-child(odd) {
        background-color: #262626;
    }
    .year-overview-table tbody tr:nth-child(even) {
        background-color: #1a1a1a;
    }
    .year-overview-table tbody tr:hover {
        background-color: #333 !important;
        color: #fff;
    }

    /* Add Event button */
    .btn-primary {
        background: linear-gradient(135deg, #0d6efd, #6610f2);
        border: none;
        box-shadow: 0 4px 12px rgba(13,110,253,0.4);
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #6610f2, #0d6efd);
    }

    .card-header.bg-dark {
        background: linear-gradient(90deg, #000000, #2b2b2b) !important;
    }
</style>

<div class="container-fluid mt-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-calendar-alt text-warning"></i> Calendar</h2>
        <a href="/life-event-management/events/add.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Event
        </a>
    </div>

    <!-- Full Width Calendar -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div id="calendar"></div>
        </div>
    </div>

    <!-- Year Overview Table -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-warning">
            <h5 class="mb-0"><i class="fas fa-list-alt"></i> Yearly Months Overview — Gregorian & Islamic</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover year-overview-table text-center" id="yearOverviewTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>English Month</th>
                            <th>اردو / اسلامی مہینہ</th>
                            <th>Hijri Year</th>
                            <th>Days</th>
                        </tr>
                    </thead>
                    <tbody id="yearOverviewBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
const hijriMonthsUrdu = {
    1: 'محرم', 2: 'صفر', 3: 'ربیع الاول', 4: 'ربیع الثانی',
    5: 'جمادی الاول', 6: 'جمادی الثانی', 7: 'رجب', 8: 'شعبان',
    9: 'رمضان', 10: 'شوال', 11: 'ذوالقعدہ', 12: 'ذوالحجہ'
};

const englishMonths = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
];

// 🎨 12 Vibrant colors for each day box (dark theme friendly)
const dayColors = [
    '#FF6B6B', '#FFA94D', '#FFD43B', '#69DB7C',
    '#38D9A9', '#4DABF7', '#9775FA', '#F783AC',
    '#DA77F2', '#63E6BE', '#FFA8A8', '#74C0FC'
];

const dayBorderColors = [
    '#E03131', '#F76707', '#F59F00', '#2F9E44',
    '#099268', '#1971C2', '#6741D9', '#C2255C',
    '#9C36B5', '#087F5B', '#E03131', '#1864AB'
];

function getHijriMonthName(date) {
    try {
        var parts = new Intl.DateTimeFormat('en-TN-u-ca-islamic-nu-latn', {
            month: 'numeric', year: 'numeric'
        }).formatToParts(date);
        var month = '', year = '';
        parts.forEach(function(p) {
            if (p.type === 'month') month = p.value;
            if (p.type === 'year') year = p.value.replace(/\D/g, '');
        });
        return { name: hijriMonthsUrdu[parseInt(month)] || '', year: year };
    } catch (err) {
        return { name: '', year: '' };
    }
}

function getHijriUrdu(date) {
    try {
        var parts = new Intl.DateTimeFormat('en-TN-u-ca-islamic-nu-latn', {
            day: 'numeric', month: 'numeric', year: 'numeric'
        }).formatToParts(date);
        var day = '', month = '';
        parts.forEach(function(p) {
            if (p.type === 'day') day = p.value;
            if (p.type === 'month') month = p.value;
        });
        return day + ' ' + (hijriMonthsUrdu[parseInt(month)] || '');
    } catch (err) {
        return '';
    }
}

function getDayColorIndex(date) {
    return date.getDate() % dayColors.length;
}

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        dayMaxEvents: 3,
        events: <?= json_encode($events_js) ?>,
        dateClick: function(info) {
            window.location.href = '/life-event-management/events/add.php?date=' + info.dateStr;
        },
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            if (info.event.url) {
                window.location.href = info.event.url;
            }
        },
        datesSet: function(info) {
            var midDate = new Date((info.start.getTime() + info.end.getTime()) / 2);
            var hijri = getHijriMonthName(midDate);
            var titleEl = document.querySelector('.fc-toolbar-title');
            if (titleEl) {
                var gregorianText = titleEl.textContent.split('\n')[0];
                titleEl.innerHTML = gregorianText + 
                    '<span class="hijri-title">' + hijri.name + ' ' + hijri.year + ' ہجری</span>';
            }
        },
        dayCellDidMount: function(arg) {
            var today = new Date();
            today.setHours(0,0,0,0);
            var cellDate = arg.date;
            cellDate.setHours(0,0,0,0);

            var colorIdx = getDayColorIndex(arg.date);
            
            if (cellDate.getTime() === today.getTime()) {
                arg.el.style.backgroundColor = '#FFD93D';
                arg.el.style.border = '3px solid #FF9800';
                arg.el.style.boxShadow = '0 0 15px rgba(255,152,0,0.9)';
            } else {
                arg.el.style.backgroundColor = dayColors[colorIdx];
                arg.el.style.border = '1px solid ' + dayBorderColors[colorIdx];
                arg.el.style.opacity = '0.95';
            }

            // Urdu Hijri date
            var hijriText = getHijriUrdu(arg.date);
            if (hijriText) {
                var hijriEl = document.createElement('div');
                hijriEl.className = 'hijri-urdu';
                hijriEl.textContent = hijriText;
                var dayNumberEl = arg.el.querySelector('.fc-daygrid-day-number');
                if (dayNumberEl) {
                    dayNumberEl.parentNode.insertBefore(hijriEl, dayNumberEl.nextSibling);
                }
            }
        }
    });
    
    calendar.render();
    buildYearOverview();
});

function buildYearOverview() {
    var tbody = document.getElementById('yearOverviewBody');
    var currentYear = new Date().getFullYear();
    
    for (var m = 0; m < 12; m++) {
        var midDate = new Date(currentYear, m, 15);
        var hijri = getHijriMonthName(midDate);
        var daysInMonth = new Date(currentYear, m + 1, 0).getDate();
        
        var row = document.createElement('tr');
        row.innerHTML = 
            '<td>' + (m + 1) + '</td>' +
            '<td><strong>' + englishMonths[m] + ' ' + currentYear + '</strong></td>' +
            '<td class="hijri-cell">' + hijri.name + '</td>' +
            '<td>' + hijri.year + ' ھ</td>' +
            '<td>' + daysInMonth + '</td>';
        tbody.appendChild(row);
    }
}
</script>

<?php include 'includes/footer.php'; ?>