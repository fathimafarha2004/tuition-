# Simple Tuition Management System 🎓

A comprehensive web-based Tuition & Educational Institute Management System developed in PHP and MySQL. Designed to streamline student management, class scheduling, fee tracking, course enrollment requests, and attendance tracking for coaching academies and private tutors.

---

## 🌟 Key Features

### 👨‍💼 Admin Portal (`/admin/`)
- **Dashboard Overview**: Key metrics including active student count, class count, pending fee count, total collected revenue, and recent activities.
- **Student Management**: View, add, edit, and deactivate student accounts with full profiles.
- **Class & Schedule Management**: Create and schedule classes by grade, subject, teacher name, classroom, and fee rates.
- **Fee Management**: Create fee vouchers, monitor payment statuses (*Pending*, *Paid*, *Overdue*), and record payments.
- **Enrollment Requests**: Review student applications to join classes and approve/reject them with admin remarks.
- **Attendance System**: Take and update class attendance by date (*Present*, *Absent*, *Late*).

### 🎓 Student Portal (`/student/`)
- **Student Registration & Login**: Self-signup with auto-generated student IDs (`ST...`).
- **Student Dashboard**: Real-time summary of enrolled classes, pending dues, and attendance rate.
- **Class Schedule**: View enrolled subjects, teacher information, room allocations, and weekly schedule.
- **Enrollment Requests**: Browse active classes and submit enrollment applications with personal notes.
- **Fee Status**: Check payment history, overdue payments, and receipt dates.
- **Attendance History**: View past attendance logs.
- **Profile Management**: Update personal contact details and addresses.

---

## 🛠️ Tech Stack

- **Backend**: PHP (MySQLi, Sessions, Password Hashing)
- **Database**: MySQL / MariaDB
- **Frontend**: HTML5, CSS3 (Custom Responsive Design System), JavaScript

---

## 🚀 Installation & Setup

### 1. Requirements
- Web server environment: [XAMPP](https://www.apachefriends.org/), [WampServer](https://www.wampserver.com/), or [Laragon] with PHP 7.4+ or 8.x
- MySQL / MariaDB database server

### 2. Database Configuration
1. Start Apache and MySQL in your local server control panel.
2. Open phpMyAdmin (`http://localhost/phpmyadmin/`).
3. Create a new database named `simple_tuition_db`.
4. Import the SQL files from the `database/` directory:
   - First import `database/simple_tuition_db.sql`
   - Then import `database/enrollment_requests.sql`

### 3. Application Setup
1. Copy or clone this repository into your server root directory:
   - For XAMPP: `htdocs/simple_tuition`
   - For WampServer: `www/simple_tuition`
2. Configure database credentials in `config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'simple_tuition_db');
   ```

### 4. Default Login Credentials
- **Admin Portal**: `http://localhost/simple_tuition/admin/login.php`
  - **Username**: `admin`
  - **Password**: `password` (or hash configured in seed database)
- **Student Portal**: `http://localhost/simple_tuition/student/login.php`
  - Register as a new student via `http://localhost/simple_tuition/student/register.php`

---

## 📁 Project Directory Structure

```text
simple_tuition/
├── admin/                     # Admin portal controllers & views
│   ├── includes/              # Admin header & sidebar components
│   ├── attendance.php         # Class attendance tracking
│   ├── class-form.php         # Add/edit class form
│   ├── classes.php            # Class list
│   ├── dashboard.php          # Admin analytics dashboard
│   ├── enrollment-requests.php# Manage student enrollment requests
│   ├── fee-form.php           # Add/edit fee records
│   ├── fees.php               # Fee management table
│   ├── login.php              # Admin login
│   ├── logout.php             # Admin session logout
│   ├── student-form.php       # Add/edit student form
│   └── students.php           # Student directory
├── css/
│   ├── admin-style.css        # Admin & student dashboard styles
│   └── student-style.css
├── database/
│   ├── simple_tuition_db.sql  # Core database schema & seed data
│   └── enrollment_requests.sql# Enrollment requests table schema
├── student/                   # Student portal controllers & views
│   ├── includes/              # Student header & sidebar components
│   ├── attendance.php         # Student attendance records
│   ├── classes.php            # Enrolled classes list
│   ├── dashboard.php          # Student home dashboard
│   ├── fees.php               # Fee history & dues
│   ├── login.php              # Student login
│   ├── logout.php             # Student logout
│   ├── profile.php            # Edit profile details
│   ├── register.php           # Student registration
│   └── request-enrollment.php # Request class enrollment
├── config.php                 # App config & DB connection
├── index.php                  # Public landing page
└── README.md                  # Project documentation
```

---

## 📄 License
This project is open-source and free to use for educational purposes.
