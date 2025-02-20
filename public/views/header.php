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
            background-color: #0a192f;
            /* Navy blue */
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
            z-index: -1;
            /* Ensure it stays in the background */
        }

        .navbar {
            z-index: 1000;
            background-color: rgba(26, 26, 52, 0.9);
            /* Semi-transparent dark blue */
            backdrop-filter: blur(10px);
            /* Blur effect */
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
            color: #007bff !important;
            /* Blue on hover */
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
            padding-top: 80px;
            /* Space for the fixed navbar */
        }

        .event-card {
            background-color: #212145;
            padding: 15px;
            border-radius: 5px;
            color: white;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .modal {
            z-index: 1050;
            /* Ensure modal is above Particles.js */
        }

        .modal-backdrop {
            z-index: 1040;
            /* Ensure backdrop is above Particles.js but below modal */
        }

        .modal-backdrop.fade.show {
            background-color: rgba(0, 0, 0, 0.5);
            /* Ensure the backdrop is visible */
        }

        .modal-content {
            background-color: #212145;
            color: #ffffff;
            border-radius: 10px;
        }

        .modal-header {
            background-color: #007bff;
            color: #ffffff;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .modal-body .form-control {
            background-color: #2a2a4d;
            color: #ffffff;
            border: 1px solid #444;
        }

        .modal-body .form-control:focus {
            background-color: #2a2a4d;
            color: #ffffff;
            border-color: #007bff;
        }

        .modal-body .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 0.75rem;
            font-size: 1rem;
        }

        .modal-body .btn-primary:hover {
            background-color: #0056b3;
        }

        .modal-backdrop {
            display: none;
            /* This will hide the backdrop */
        }
        .card {
        background: rgba(255, 255, 255, 0.1); /* Semi-transparent white background */
        border: none;
        border-radius: 15px;
        backdrop-filter: blur(10px); /* Blur effect */
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        padding: 2px;
        width: 100%;
        max-width: 400px;
        margin: 0 auto;
        animation: fadeIn 0.5s ease-in-out;
    }

    .card-header {
        background: transparent;
        border-bottom: none;
        text-align: center;
        padding: 1rem 0;
    }

    .card-header h2 {
        color: #ffffff;
        font-weight: 600;
        margin: 0;
    }

    .form-control {
        background: rgba(255, 255, 255, 0.1); /* Semi-transparent input fields */
        border: none;
        border-radius: 5px;
        color: #ffffff;
        padding: 10px 15px;
        margin-bottom: 1rem;
    }

    .form-control:focus {
        background: rgba(255, 255, 255, 0.2); /* Lighter background on focus */
        box-shadow: none;
        border: none;
        color: #ffffff;
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.7); /* Placeholder text color */
    }

    .form-control.is-invalid {
        border: 1px solid #dc3545; /* Red border for invalid inputs */
    }

    .btn-primary {
        background: #007bff; /* Bootstrap primary blue */
        border: none;
        border-radius: 5px;
        padding: 10px;
        font-size: 16px;
        font-weight: 500;
        width: 100%;
        transition: background 0.3s ease;
    }

    .btn-primary:hover {
        background: #0056b3; /* Darker blue on hover */
    }

    .form-group label {
        color: #ffffff;
        font-weight: 500;
    }

    .password-toggle {
        position: relative;
    }

    .password-toggle .toggle-icon {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: rgba(255, 255, 255, 0.7);
    }

    .password-toggle .toggle-icon:hover {
        color: #ffffff;
    }

    /* Animation for the card */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
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
                    <li class="nav-item">
                        <a class="nav-link" href="https://documenter.getpostman.com/view/19858182/2sAYX3phTy" target="_blank">API Docs</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="content">