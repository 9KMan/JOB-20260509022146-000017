<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - GPS Attendance Systems</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .navbar { padding: 1rem 0; }
        .service-card { height: 100%; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="bi bi-geo-alt"></i> GPS Attendance</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
                    <li class="nav-item"><a class="nav-link active" href="services.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin/login.php"><i class="bi bi-box-arrow-in-right"></i> Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="py-5 bg-light">
        <div class="container">
            <h1 class="mb-4 text-center">Our Services</h1>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card service-card">
                        <div class="card-body text-center">
                            <i class="bi bi-phone-check feature-icon text-primary mb-3"></i>
                            <h4>Native Android App</h4>
                            <p>Kotlin-based Android application with high-accuracy GPS, offline queue, and automatic sync when connectivity returns.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card service-card">
                        <div class="card-body text-center">
                            <i class="bi bi-laptop feature-icon text-primary mb-3"></i>
                            <h4>Employee Web Portal</h4>
                            <p>Mobile-responsive web interface for employees to check in/out, view attendance history, and manage their profile.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card service-card">
                        <div class="card-body text-center">
                            <i class="bi bi-gear feature-icon text-primary mb-3"></i>
                            <h4>Admin Dashboard</h4>
                            <p>Complete employee and site management with daily/monthly reports, Excel and PDF export capabilities.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card service-card">
                        <div class="card-body text-center">
                            <i class="bi bi-map feature-icon text-primary mb-3"></i>
                            <h4>Location Verification</h4>
                            <p>Server-side GPS validation using Haversine formula ensures attendance records are within configurable site radii.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card service-card">
                        <div class="card-body text-center">
                            <i class="bi bi-shield-check feature-icon text-primary mb-3"></i>
                            <h4>JWT Authentication</h4>
                            <p>Stateless JWT-based authentication with refresh token rotation for secure API access across all platforms.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card service-card">
                        <div class="card-body text-center">
                            <i class="bi bi-file-earmark-spreadsheet feature-icon text-primary mb-3"></i>
                            <h4>Reporting & Export</h4>
                            <p>Flexible attendance reporting with date/employee/site filters, plus Excel and PDF export for payroll integration.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; 2024 GPS Attendance Systems. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>