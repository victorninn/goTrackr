# ⏱ TimeTracker — Laravel Time Tracking + Payroll System

A clean, role-based time tracking and simple payroll system built with Laravel, Blade, and Tailwind CSS (CDN).

---

## 📁 Complete File Structure

```
your-laravel-project/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── TimeLogController.php
│   │   │   ├── EmployeeController.php
│   │   │   ├── CompanyController.php
│   │   │   ├── PayrollController.php
│   │   │   └── AdminController.php
│   │   └── Middleware/
│   │       └── RoleMiddleware.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Role.php
│   │   ├── Company.php
│   │   └── TimeLog.php
│   └── Providers/
│       └── AppServiceProvider.php
│
├── bootstrap/
│   └── app.php
│
├── database/
│   ├── migrations/
│   │   ├── 2024_01_01_000001_create_roles_table.php
│   │   ├── 2024_01_01_000002_create_companies_table.php
│   │   ├── 2024_01_01_000003_create_users_table.php
│   │   └── 2024_01_01_000004_create_time_logs_table.php
│   └── seeders/
│       └── DatabaseSeeder.php
│
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php          ← Main sidebar layout
│       ├── auth/
│       │   └── login.blade.php
│       ├── dashboard/
│       │   ├── superadmin.blade.php
│       │   ├── admin.blade.php
│       │   └── employee.blade.php     ← Clock in/out + live timer
│       ├── logs/
│       │   ├── index.blade.php        ← Admin: all logs
│       │   └── my.blade.php           ← Employee: own logs
│       ├── payroll/
│       │   └── index.blade.php        ← Weekly/monthly payroll summary
│       ├── employees/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   └── edit.blade.php
│       ├── companies/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   └── edit.blade.php
│       └── admins/
│           ├── index.blade.php
│           └── create.blade.php
│
└── routes/
    └── web.php
```

---

## 🚀 Installation

### 1. Create a new Laravel project

```bash
composer create-project laravel/laravel timetracker
cd timetracker
```

### 2. Copy all provided files

Place each file exactly at the path shown in the file structure above.

### 3. Configure your `.env`

```env
APP_NAME=TimeTracker
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=timetracker
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Create the database

```sql
CREATE DATABASE timetracker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Run migrations and seed

```bash
php artisan migrate --seed
```

This creates all tables and seeds:
- 3 roles: superadmin, admin, employee
- 1 default company: "Default Company"
- 1 superadmin: `superadmin@example.com` / `password`

### 6. Create storage symlink (for company logos)

```bash
php artisan storage:link
```

### 7. Start the server

```bash
php artisan serve
```

Visit: **http://localhost:8000**

---

## 🔐 Default Login

| Role       | Email                    | Password   |
|------------|--------------------------|------------|
| Superadmin | superadmin@example.com   | password   |

After logging in as superadmin:
1. Go to **Companies** → create your company
2. Go to **Manage Admins** → create an admin, assign to company
3. Log in as that admin
4. Go to **Employees** → create employees with hourly rates

---

## 👥 Roles & Permissions

| Feature                   | Superadmin | Admin | Employee |
|---------------------------|:----------:|:-----:|:--------:|
| Create Admins             | ✅         | ❌    | ❌       |
| Manage Companies          | ✅         | ✅*   | ❌       |
| Manage Employees          | ✅         | ✅*   | ❌       |
| View All Logs             | ✅         | ✅*   | ❌       |
| Export All Logs (CSV)     | ✅         | ✅*   | ❌       |
| View Payroll Summary      | ✅         | ✅*   | ❌       |
| Export Payroll (CSV)      | ✅         | ✅*   | ❌       |
| Clock In / Clock Out      | ❌         | ❌    | ✅       |
| View Own Logs             | ❌         | ❌    | ✅       |
| Export Own Logs (CSV)     | ❌         | ❌    | ✅       |

*Scoped to their own company only

---

## ⏱ Time Tracking

- Employee clicks **Clock In** → creates a time log with current time
- System **prevents double clock-ins** (one active at a time)
- **Running elapsed timer** shows how long employee has been clocked in
- Employee clicks **Clock Out** → `total_hours` auto-computed
- Formula: `total_hours = (clock_out - clock_in) in minutes / 60`

---

## 💰 Payroll Calculation

Simple formula:
```
total_salary = hourly_rate × total_hours
```

- View by **monthly** or **weekly** period
- Shows: Employee Name, Total Hours, Hourly Rate, Total Salary
- Grand totals at the bottom of the table
- Export to CSV

---

## 📤 CSV Exports

### Time Logs CSV (Weekly or Monthly)
Columns: `Employee Name, Date, Clock In, Clock Out, Total Hours`

### Payroll CSV (Weekly or Monthly)
Columns: `Employee Name, Total Hours, Hourly Rate, Total Salary`

**Export URLs:**
- Employee own weekly: `GET /my-logs/export?type=weekly`
- Employee own monthly: `GET /my-logs/export?type=monthly`
- Admin logs weekly: `GET /logs/export?type=weekly`
- Admin logs monthly: `GET /logs/export?type=monthly`
- Payroll export: `GET /payroll/export?period=monthly&month=2024-01`

---

## 🔧 Configuration Notes

### Adding `auth` middleware
The routes use `middleware('auth')`. Laravel 11 uses the `bootstrap/app.php` approach — the provided file handles this automatically.

### File uploads (company logos)
- Stored in `storage/app/public/logos/`
- Accessible via `Storage::url($path)` after running `php artisan storage:link`
- Max size: 2MB, images only

### Pagination
Uses Laravel's built-in Tailwind pagination. Enabled in `AppServiceProvider` with `Paginator::useTailwind()`.

---

## 🎨 UI Features

- **Sidebar navigation** with role-aware menu items
- **Top bar** with company logo, live date/time clock
- **Employee dashboard**: Clock in/out widget + live elapsed timer
- **Payroll page**: Period picker (weekly/monthly), grand totals
- **Flash messages**: Success/error banners on all actions
- **Responsive tables** with pagination
- All styled with **Tailwind CSS via CDN** (no build tools needed)

---

## ❓ Troubleshooting

**SQLSTATE[23000]: Integrity constraint violation** during migration:
→ Make sure you run migrations in order. The filenames starting with `2024_01_01_000001` ensure correct order.

**"Class not found" errors:**
→ Run `composer dump-autoload`

**Logos not displaying:**
→ Run `php artisan storage:link`

**403 Unauthorized:**
→ Your user's `role_id` doesn't match the required role. Check the `roles` table and user's `role_id`.
