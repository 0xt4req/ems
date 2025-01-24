<?php include('header.php'); ?>
<body>
    <!-- Body Section with Event Cards -->
    <div class="container mt-5">
        <h1 class="text-center text-white mb-5">Upcoming Events</h1>
        <div class="row row-cols-1 row-cols-md-3 g-4" id="event-cards">
            <!-- Event cards will be dynamically loaded here -->
        </div>
    </div>

    <!-- Bootstrap Modal for Registration -->
    <div class="modal fade" id="registrationModal" tabindex="-1" aria-labelledby="registrationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registrationModalLabel">Event Registration</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="registrationForm">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const eventCardsContainer = document.getElementById('event-cards');
            const registrationModal = new bootstrap.Modal(document.getElementById('registrationModal'));
            let currentEventId = null;

            // Fetch events from the API
            fetch('/ems/api/events/public')
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    eventCardsContainer.innerHTML = ''; // Clear existing content

                    // Loop through the events and create cards
                    data.forEach(event => {
                        const card = `
                            <div class="col">
                                <div class="card event-card h-100">
                                    <div class="card-body">
                                        <h3 class="card-title">${event.name}</h3>
                                        <p class="card-text">${event.description}</p>
                                        <p><strong>Date:</strong> ${event.date}</p>
                                        <p><strong>Location:</strong> ${event.location}</p>
                                        <button class="btn btn-primary register-btn" data-event-id="${event.id}">Register</button>
                                    </div>
                                </div>
                            </div>
                        `;
                        eventCardsContainer.insertAdjacentHTML('beforeend', card);
                    });

                    // Attach click event to Register buttons
                    document.querySelectorAll('.register-btn').forEach(button => {
                        button.addEventListener('click', function () {
                            currentEventId = this.getAttribute('data-event-id');
                            registrationModal.show();
                        });
                    });
                })
                .catch(error => {
                    console.error('Error fetching events:', error);
                });

            // Handle form submission
            document.getElementById('registrationForm').addEventListener('submit', function (e) {
                e.preventDefault();

                const registrationData = {
                    eventId: currentEventId,
                    name: document.getElementById('name').value,
                    email: document.getElementById('email').value,
                    phone: document.getElementById('phone').value
                };

                // Send registration data to the server in JSON format
                fetch('/ems/api/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(registrationData)
                })
                    .then(response => response.json())
                    .then(data => {
                        alert('Registration successful!');
                        registrationModal.hide();
                        document.getElementById('registrationForm').reset();
                    })
                    .catch(error => {
                        alert('Registration failed. Please try again.');
                        console.error('Error:', error);
                    });
            });
        });
    </script>
</body>

<?php include('footer.php'); ?>