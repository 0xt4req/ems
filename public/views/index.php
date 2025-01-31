<?php include('header.php'); ?>

<body>
    <!-- Body Section with Event Cards -->
    <div class="container mt-5">
        <h1 class="text-center text-white mb-5">Upcoming Events</h1>
        <div class="row row-cols-1 row-cols-md-3 g-4" id="event-cards">
            <!-- Event cards will be dynamically loaded here -->
        </div>
    </div>

    <!-- Bootstrap Modal for Viewing Description -->
    <div class="modal fade" id="descriptionModal" tabindex="-1" aria-labelledby="descriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="descriptionModalLabel">Event Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="eventDescriptionContent">
                    <!-- Description will be loaded dynamically -->
                </div>
            </div>
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
                            <input type="text" class="form-control" id="name" maxlength="100" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" maxlength="100" required>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const eventCardsContainer = document.getElementById('event-cards');
            const registrationModal = new bootstrap.Modal(document.getElementById('registrationModal'));
            const descriptionModal = new bootstrap.Modal(document.getElementById('descriptionModal'));
            let currentEventId = null;

            // Fetch events from the API
            function fetchEvents() {
                fetch('/ems/api/events/public')
                .then(response => response.json())
                .then(data => {
                    eventCardsContainer.innerHTML = ''; // Clear existing content

                    data.forEach(event => {
                        const formattedTime = new Date(`1970-01-01T${event.time}`).toLocaleTimeString([], {
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        });

                        const card = `
                            <div class="col">
                                <div class="card event-card h-100">
                                    <div class="card-body">
                                        <h3 class="card-title"><i class="fas fa-calendar-alt"></i> ${event.name}</h3>
                                        <p><strong><i class="fas fa-clock"></i> Time:</strong> ${formattedTime}</p>
                                        <p><strong><i class="fas fa-map-marker-alt"></i> Location:</strong> ${event.location}</p>
                                        <p><strong><i class="fas fa-user"></i> Event By:</strong> ${event.username}</p>
                                        <p><strong><i class="fas fa-users"></i> Attendees:</strong> ${event.total_attendees}/${event.max_capacity}</p>
                                        <button class="btn btn-info view-desc-btn" data-description="${event.description}"><i class="fas fa-info-circle"></i> View Details</button><br><br>
                                        <button class="btn btn-primary register-btn" data-event-id="${event.id}"><i class="fas fa-ticket-alt"></i> Register</button>
                                    </div>
                                </div>
                            </div>
                        `;
                        eventCardsContainer.insertAdjacentHTML('beforeend', card);
                    });

                    // Attach click event to Register buttons
                    document.querySelectorAll('.register-btn').forEach(button => {
                        button.addEventListener('click', function() {
                            currentEventId = this.getAttribute('data-event-id');
                            registrationModal.show();
                        });
                    });

                    // Attach click event to View Description buttons
                    document.querySelectorAll('.view-desc-btn').forEach(button => {
                        button.addEventListener('click', function() {
                            document.getElementById('eventDescriptionContent').innerText = this.getAttribute('data-description');
                            descriptionModal.show();
                        });
                    });
                })
                .catch(error => {
                    console.error('Error fetching events:', error);
                });
            }

            // Handle form submission
            document.getElementById('registrationForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const registrationData = {
                    eventId: currentEventId,
                    name: document.getElementById('name').value,
                    email: document.getElementById('email').value,
                };

                fetch('/ems/api/attendees/create', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(registrationData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: data.message
                            });
                            fetchEvents();
                            registrationModal.hide();
                            document.getElementById('registrationForm').reset();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message
                            });
                        }
                        
                    })
                    .catch(error => {
                        alert('Registration failed. Please try again.');
                        console.error('Error:', error);
                    });
            });
            fetchEvents();
        });
    </script>
    <script src="https://kit.fontawesome.com/3111411978.js" crossorigin="anonymous"></script>
</body>

<?php include('footer.php'); ?>