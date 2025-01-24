<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Particles.js -->
    <script src="https://cdn.jsdelivr.net/npm/particles.js"></script>
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #0a192f; /* Navy blue */
            color: #ffffff;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: -1; /* Ensure it stays in the background */
        }

        .navbar {
            z-index: 1000;
            background-color: rgba(26, 26, 52, 0.9); /* Semi-transparent dark blue */
            backdrop-filter: blur(10px); /* Blur effect */
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 600;
            color: #ffffff !important;
        }

        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            font-weight: 500;
            margin-left: 1.5rem;
            transition: color 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: #007bff !important; /* Blue on hover */
        }

        .navbar-toggler {
            border: none;
            outline: none;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba(255, 255, 255, 0.8)' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
        }

        .content {
            position: relative;
            z-index: 1;
            padding-top: 80px; /* Space for the fixed navbar */
        }
    </style>
</head>

<body>
    <!-- Particles.js Background -->
    <div id="particles-js"></div>

    <!-- Navigation Header -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">Eventify</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="content">