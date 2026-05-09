<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - GPS Attendance Systems</title>
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
                    <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link active" href="contact.php">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin/login.php"><i class="bi bi-box-arrow-in-right"></i> Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h1 class="mb-4">Contact Us</h1>
                    <p class="lead">Get in touch with our team to discuss your attendance management needs.</p>
                    <form method="POST" action="contact.php">
                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Message</label>
                            <textarea name="message" class="form-control" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
                    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                    <div class="alert alert-success mt-3">Thank you! Your message has been sent.</div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h4 class="card-title">Contact Information</h4>
                            <p class="card-text">
                                <i class="bi bi-envelope me-2"></i>contact@gpsattendance.com<br>
                                <i class="bi bi-phone me-2"></i>+1 (555) 123-4567<br>
                                <i class="bi bi-geo-alt me-2"></i>123 Tech Park, Innovation Drive<br>
                                San Francisco, CA 94105
                            </p>
                            <hr>
                            <h5>Business Hours</h5>
                            <p class="mb-0">Monday - Friday: 9:00 AM - 6:00 PM</p>
                            <p>Saturday - Sunday: Closed</p>
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