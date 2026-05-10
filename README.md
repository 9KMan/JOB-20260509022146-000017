# GPS-Based Employee Attendance System

Production-ready GPS-based attendance management platform with Flutter Android app, PHP/MySQL backend, and company website.

## Architecture

```
[Flutter Android App] ──GPS──> [PHP API] ──>[MySQL DB]
       │                            │
       │ Offline-first              │
       └────── SQLite queue ────────┘
```

## Tech Stack

| Component | Technology |
|-----------|------------|
| Mobile App | Flutter (Dart) |
| API | PHP + MySQL + JWT |
| Admin Panel | PHP + Bootstrap |
| Employee Web | PHP + Mobile Responsive |
| Database | MySQL |
| GPS Validation | Haversine formula (server-side) |
| Offline Storage | SQLite (Flutter) |

## Data Sources

| Source | Type | Fields |
|--------|------|--------|
| employees | MySQL | id, name, email, phone, designation, department |
| sites | MySQL | id, name, lat, lng, radius_meters |
| attendance | MySQL | id, employee_id, site_id, check_in/out time, GPS coords |

## Data Model

MySQL tables:
- `employees` — employee records
- `users` — auth with role (admin/employee)
- `sites` — office locations with GPS coordinates + radius
- `attendance` — check-in/out records with GPS validation

## CLI Reference

```bash
# API endpoints
POST /api/login
POST /api/attendance/checkin
POST /api/attendance/checkout
GET /api/attendance/history?emp_id=&from=&to=
GET /api/admin/employees
POST /api/admin/sites
GET /api/admin/reports/export?format=excel|pdf
```

## Installation

```bash
# Database setup
mysql -u root -p < gps-attendance/sql/schema.sql

# PHP API
cd gps-attendance/api && php -S localhost:8000

# Flutter Android (requires Flutter SDK)
cd gps-attendance/android
flutter pub get
flutter run
```

## Quality Guarantees

- Haversine formula validates GPS within configurable site radius (default 100m)
- Offline-first: Flutter app queues attendance when offline, syncs when connected
- JWT auth on all API endpoints
- Server-side GPS validation (never trust client coordinates)

## Output Format

- Check-in response: `{success, message, distance_meters}`
- Attendance history: JSON array of `{date, check_in, check_out, valid}`
- Reports: Excel (.xlsx) and PDF export

## Project Structure

```
gps-attendance/
├── api/                    # PHP REST API
│   ├── config.php         # DB connection
│   ├── functions.php      # JWT + Haversine
│   ├── index.php          # Route dispatcher
│   └── routes/            # Endpoint handlers
├── admin/                  # Bootstrap admin panel
├── employee/               # Employee web app
├── website/                # Company website
├── sql/schema.sql          # MySQL DDL
└── android/                # Flutter Android app
    └── lib/
        ├── main.dart
        ├── services/
        └── models/
```

## Limitations

- No FCM push notifications
- No leave management module
- No payroll integration
- Single-tenant (one company per deployment)
