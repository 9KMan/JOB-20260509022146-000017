<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - GPS Attendance Systems</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
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
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="about.php">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin/login.php"><i class="bi bi-box-arrow-in-right"></i> Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="py-5">
        <div class="container">
            <h1 class="mb-4">About Us</h1>
            <div class="row">
                <div class="col-md-8">
                    <h2 class="h4">Professional GPS-Based Attendance Solutions</h2>
                    <p class="lead">We provide cutting-edge employee attendance management systems with precise GPS-based location verification.</p>
                    <p>Our platform combines native Android applications with responsive web interfaces, ensuring employees can check in seamlessly while maintaining accurate location-based validation. With server-side Haversine formula calculations, organizations can trust that their attendance data is both reliable and secure.</p>
                    <h3 class="h5 mt-4">Our Mission</h3>
                    <p>To deliver enterprise-grade attendance management solutions that simplify workforce tracking while enforcing location-based integrity through proven GPS technology.</p>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h4 class="card-title">Why Choose Us?</h4>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Sub-second check-in response</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>200m radius validation</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Offline-first Android app</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Comprehensive admin panel</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Excel & PDF exports</li>
                            </ul>
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