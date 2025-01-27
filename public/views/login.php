<?php include('header.php'); ?>

<!-- Custom CSS for the registration page -->
<style>
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

<!-- Registration Form -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mt-5">
                <div class="card-header">
                    <h2>Login</h2>
                </div>
                <div class="card-body">
                    <form id="loginForm" method="POST">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                        <div class="form-group password-toggle">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                            <span class="toggle-icon" onclick="togglePassword('password')">👁️</span>
                        </div>
                        <br>
                        <button type="submit" class="btn btn-primary">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Send AJAX request to /api/register to register the user -->
<script>

    // Form submission
    document.getElementById('loginForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = {
            email: document.getElementById('email').value,
            password: document.getElementById('password').value
        };

        fetch('/ems/api/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message
                }).then(() => {
                    window.location.href = data.location; // Redirect to user dashboard page
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Invalid Username or Password'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred. Please try again.'
            });
        });
    });
</script>

<?php include('footer.php'); ?>