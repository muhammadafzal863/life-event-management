# 🌟 Life Event Management System

A modern, feature-rich **web-based Life Event Management System** built with **PHP** and **MySQL**. It allows users to record, organize, search, and manage important personal and family life events — with a beautiful **dual-calendar** (Gregorian + Islamic/Hijri) interface in **Urdu**.

---

## 📖 Table of Contents

1. [Overview](#-overview)
2. [Key Features](#-key-features)
3. [Screenshots & Preview](#-screenshots--preview)
4. [Technology Stack](#-technology-stack)
5. [Database Schema](#-database-schema)
6. [Folder Structure](#-folder-structure)
7. [Installation Guide](#-installation-guide)
8. [Usage Guide](#-usage-guide)
9. [Security](#-security)
10. [Urdu Islamic Calendar](#-urdu-islamic-calendar)
11. [Future Enhancements](#-future-enhancements)
12. [License](#-license)
13. [Credits](#-credits)

---

## 🌐 Overview

**Life Event Management System** is a complete web application that helps users organize their life's most important moments — birthdays, weddings, anniversaries, graduations, births, achievements, career milestones, property purchases, memorials, and custom events.

The system includes:
- A modern **dark-themed calendar** with **Islamic Hijri dates in Urdu**
- **Reminders** for upcoming events
- Full **admin panel** for system management
- Secure **user authentication** with sessions
- Responsive **Bootstrap 5** interface

---

## ✨ Key Features

### 👤 User Authentication
- Registration, login, logout
- Secure password hashing with `password_hash()`
- Session-based authentication
- Password change via profile page
- Cache-control headers prevent back-button access after logout

### 📊 User Dashboard
- **Summary cards**: Total Events, This Month, Upcoming (30 days)
- List of upcoming events
- Quick access to add new events
- View upcoming reminders

### 📅 Event Management
- **Add, edit, view, delete** events
- Search and filter by category
- Upload event images
- Set visibility (**private / public**)
- Optional reminder date/time for each event

### 🏷️ Event Categories (10 Predefined)
1. Birthday
2. Wedding
3. Wedding Anniversary
4. Graduation
5. Birth
6. Achievement
7. Career / Job
8. Property / Moving
9. Memorial
10. Custom Event

Admins can **add or delete** categories dynamically.

### 🗓️ Dual Calendar (Gregorian + Islamic)
- FullCalendar.js integration (Month / Week / List views)
- **Colorful day cells** — every day gets a unique vibrant color
- **Today highlighted** in glowing yellow with orange border
- **Islamic (Hijri) dates in Urdu** under each Gregorian day
- **Calendar title** shows both Gregorian and Islamic month names (e.g., `October 2026` + `ربیع الثانی 1448 ہجری`)
- **Click any date** → Add Event page opens with pre-filled date
- **Yearly Months Overview Table** — lists all 12 Gregorian months with their Urdu Islamic equivalents
- **Dark Mode** — body uses a black-to-grey gradient background
- Black bold date numbers on vibrant day boxes

### 🔔 Reminders
- Set reminder date/time when adding/editing an event
- View upcoming reminders on dashboard
- Reminder status: `pending` / `sent`

### 👤 Profile Management
- Update name, phone, profile image
- Change password
- View past activity

### 🛡️ Admin Panel
- **Dashboard** with clickable cards (Total Users, Total Events, Categories)
- **Manage Users**: toggle status, promote/demote admin, delete
- **Manage Events**: view or delete any event
- **Manage Categories**: add new categories, delete existing ones
- Admin-only pages protected with `isAdmin()` checks

### 🎨 UI / UX
- **Dark theme** calendar with vibrant day cells
- Bootstrap 5 responsive grid
- Font Awesome 6 icons
- **Noto Nastaliq Urdu** font for Islamic dates
- Hover effects and smooth transitions
- Mobile-friendly layout

---

## 🖼️ Screenshots & Preview

| Page | Description |
|------|-------------|
| **Login / Register** | Clean card layout with Bootstrap 5 |
| **Dashboard** | Three summary cards + upcoming events table |
| **Events List** | Search + filter by category, action buttons |
| **Event Form** | Full form with image upload and reminder field |
| **Calendar** | Dark mode with colorful day boxes and Urdu Hijri dates |
| **Profile** | Editable form with profile image |
| **Admin Panel** | Clickable stat cards + management pages |

---

## 🛠️ Technology Stack

| Layer       | Technology                                      |
|-------------|-------------------------------------------------|
| **Frontend**    | HTML5, CSS3, Bootstrap 5, JavaScript        |
| **Backend**     | PHP 8+ (PDO for database access)            |
| **Database**    | MySQL / MariaDB                             |
| **Calendar**    | FullCalendar.js 6                           |
| **Icons**       | Font Awesome 6                              |
| **Fonts**       | Google Fonts — Noto Nastaliq Urdu           |
| **Hijri Dates** | JavaScript `Intl.DateTimeFormat` (built-in) |
| **Charts**      | (Optional — Chart.js planned)               |

---

## 🗄️ Database Schema

Database name: **`life_event_management`**

### `users`
| Field           | Type                       | Notes                  |
|-----------------|----------------------------|------------------------|
| id              | INT PK AUTO_INCREMENT      |                        |
| name            | VARCHAR(100)               |                        |
| email           | VARCHAR(100) UNIQUE        |                        |
| password        | VARCHAR(255)               | hashed with bcrypt     |
| phone           | VARCHAR(20)                |                        |
| profile_image   | VARCHAR(255)               |                        |
| role            | ENUM('user','admin')       | default `user`         |
| status          | ENUM('active','inactive')  | default `active`       |
| created_at      | TIMESTAMP                  | default CURRENT_TIMESTAMP |

### `event_categories`
| Field       | Type         |
|-------------|--------------|
| id          | INT PK AI    |
| name        | VARCHAR(50)  |
| description | TEXT         |
| icon        | VARCHAR(50)  |
| created_at  | TIMESTAMP    |

### `events`
| Field        | Type                       | Notes                    |
|--------------|----------------------------|--------------------------|
| id           | INT PK AI                  |                          |
| user_id      | INT FK → users.id          | ON DELETE CASCADE        |
| category_id  | INT FK → event_categories.id | ON DELETE SET NULL     |
| title        | VARCHAR(200)               |                          |
| description  | TEXT                       |                          |
| event_date   | DATE                       |                          |
| event_time   | TIME                       |                          |
| location     | VARCHAR(255)               |                          |
| image        | VARCHAR(255)               |                          |
| visibility   | ENUM('private','public')   | default `private`        |
| created_at   | TIMESTAMP                  |                          |

### `reminders`
| Field         | Type                       | Notes                    |
|---------------|----------------------------|--------------------------|
| id            | INT PK AI                  |                          |
| event_id      | INT FK → events.id         | ON DELETE CASCADE        |
| user_id       | INT FK → users.id          | ON DELETE CASCADE        |
| reminder_date | DATETIME                   |                          |
| status        | ENUM('pending','sent')     | default `pending`        |
| created_at    | TIMESTAMP                  |                          |

---

## 📁 Folder Structure

---

## 🚀 Installation Guide

### 1. Prerequisites
- PHP **8.0+**
- MySQL **5.7+** or MariaDB **10.4+**
- Web server: **XAMPP**, **WAMP**, **MAMP**, or **LAMP**
- Modern browser (Chrome / Edge / Firefox / Safari)

### 2. Download / Clone
Place the project folder `life-event-management` inside your web root:
- **XAMPP**: `C:\xampp\htdocs\`
- **WAMP**: `C:\wamp64\www\`
- **Linux**: `/var/www/html/`

### 3. Create the Database

Open phpMyAdmin (or MySQL CLI) and run:

```sql
CREATE DATABASE IF NOT EXISTS life_event_management;
USE life_event_management;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    profile_image VARCHAR(255),
    role ENUM('user','admin') DEFAULT 'user',
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE event_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO event_categories (name, description, icon) VALUES
('Birthday', 'Birthday celebrations', 'fa-birthday-cake'),
('Wedding', 'Wedding ceremonies', 'fa-ring'),
('Wedding Anniversary', 'Anniversary celebrations', 'fa-heart'),
('Graduation', 'Graduation ceremonies', 'fa-graduation-cap'),
('Birth', 'Birth of a child', 'fa-baby'),
('Achievement', 'Personal achievements', 'fa-trophy'),
('Career / Job', 'Job promotions, new jobs', 'fa-briefcase'),
('Property / Moving', 'Buying property, moving', 'fa-home'),
('Memorial', 'Memorial events', 'fa-dove'),
('Custom Event', 'Other custom events', 'fa-calendar');

CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    event_time TIME,
    location VARCHAR(255),
    image VARCHAR(255),
    visibility ENUM('private','public') DEFAULT 'private',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES event_categories(id) ON DELETE SET NULL
);

CREATE TABLE reminders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    reminder_date DATETIME NOT NULL,
    status ENUM('pending','sent') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);