<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - GPS Attendance Systems</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .hero { background: linear-gradient(135deg, #0d6efd 0%, #0a4ed8 100%); color: white; padding: 100px 0; }
        .feature-icon { font-size: 3rem; color: #0d6efd; }
        .navbar { padding: 1rem 0; }
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
                    <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin/login.php"><i class="bi bi-box-arrow-in-right"></i> Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="container text-center">
            <h1 class="display-4 mb-4">GPS-Based Employee Attendance Management</h1>
            <p class="lead mb-4">Accurate, efficient, and location-verified attendance tracking for modern businesses</p>
            <a href="contact.php" class="btn btn-light btn-lg">Get Started</a>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <i class="bi bi-globe feature-icon"></i>
                    <h3 class="h5 mt-3">GPS Location Verification</h3>
                    <p>Server-side validation using Haversine formula ensures accurate location tracking within assigned site radii.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <i class="bi bi-phone feature-icon"></i>
                    <h3 class="h5 mt-3">Mobile & Web Apps</h3>
                    <p>Native Android app with offline support and responsive web portal for seamless employee access.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <i class="bi bi-file-earmark-bar-graph feature-icon"></i>
                    <h3 class="h5 mt-3">Comprehensive Reports</h3>
                    <p>Generate Excel and PDF reports with filtering by date range, employee, and site location.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; 2024 GPS Attendance Systems. All rights reserved.</p>
            <p class="mb-0"><a href="admin/login.php" class="text-white-50">Admin Panel</a></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>