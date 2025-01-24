<?php include('../header.php'); ?>

<!-- Custom CSS for the dashboard -->
<style>
    .dashboard-container {
        padding: 2rem;
    }

    .card {
        background: rgba(255, 255, 255, 0.1); /* Semi-transparent white background */
        border: none;
        border-radius: 15px;
        backdrop-filter: blur(10px); /* Blur effect */
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .card-header {
        background: transparent;
        border-bottom: none;
        padding: 1rem 0;
    }

    .card-header h2 {
        color: #ffffff;
        font-weight: 600;
        margin: 0;
    }

    .btn-primary {
        background: #007bff; /* Bootstrap primary blue */
        border: none;
        border-radius: 5px;
        padding: 10px;
        font-size: 16px;
        font-weight: 500;
        transition: background 0.3s ease;
    }

    .btn-primary:hover {
        background: #0056b3; /* Darker blue on hover */
    }

    .btn-danger {
        background: #dc3545; /* Bootstrap danger red */
        border: none;
        border-radius: 5px;
        padding: 10px;
        font-size: 16px;
        font-weight: 500;
        transition: background 0.3s ease;
    }

    .btn-danger:hover {
        background: #c82333; /* Darker red on hover */
    }

    .btn-warning {
        background: #ffc107; /* Bootstrap warning yellow */
        border: none;
        border-radius: 5px;
        padding: 10px;
        font-size: 16px;
        font-weight: 500;
        transition: background 0.3s ease;
    }

    .btn-warning:hover {
        background: #e0a800; /* Darker yellow on hover */
    }

    .table {
        color: #ffffff; /* White text for the table */
    }

    .dataTables_wrapper .dataTables_filter input {
        color: #ffffff; /* White text for search input */
        background: rgba(255, 255, 255, 0.1); /* Semi-transparent background */
        border: none;
        border-radius: 5px;
        padding: 5px 10px;
    }

    .dataTables_wrapper .dataTables_length select {
        color: #ffffff; /* White text for length select */
        background: rgba(255, 255, 255, 0.1); /* Semi-transparent background */
        border: none;
        border-radius: 5px;
        padding: 5px 10px;
    }

    .dataTables_wrapper .dataTables_info {
        color: #ffffff; /* White text for table info */
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        color: #ffffff !important; /* White text for pagination buttons */
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: rgba(255, 255, 255, 0.1); /* Semi-transparent background on hover */
    }

    .modal-content {
        background: rgba(26, 26, 52, 0.9); /* Semi-transparent dark blue */
        backdrop-filter: blur(10px); /* Blur effect */
        border: none;
        border-radius: 15px;
    }

    .modal-header {
        border-bottom: none;
    }

    .modal-title {
        color: #ffffff;
    }

    .modal-body {
        color: #ffffff;
    }

    .modal-footer {
        border-top: none;
    }
</style>

<!-- Dashboard Content -->
<div class="dashboard-container">
    <div class="card">
        <div class="card-header">
            <h2>Events</h2>
        </div>
        <div class="card-body">
            <!-- Add Event Button -->
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addEventModal">
                Add Event
            </button>

            <!-- Events Table -->
            <table id="eventsTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Location</th>
                        <th>Max Capacity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated dynamically using DataTables -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEventModalLabel">Add Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addEventForm">
                    <div class="form-group mb-3">
                        <label for="eventName">Name</label>
                        <input type="text" class="form-control" id="eventName" name="eventName" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="eventDescription">Description</label>
                        <textarea class="form-control" id="eventDescription" name="eventDescription" required></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="eventDate">Date</label>
                        <input type="date" class="form-control" id="eventDate" name="eventDate" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="eventTime">Time</label>
                        <input type="time" class="form-control" id="eventTime" name="eventTime" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="eventLocation">Location</label>
                        <input type="text" class="form-control" id="eventLocation" name="eventLocation" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="eventCapacity">Max Capacity</label>
                        <input type="number" class="form-control" id="eventCapacity" name="eventCapacity" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="addEvent()">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Event Modal -->
<div class="modal fade" id="editEventModal" tabindex="-1" aria-labelledby="editEventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editEventModalLabel">Edit Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editEventForm">
                    <input type="hidden" id="editEventId" name="editEventId">
                    <div class="form-group mb-3">
                        <label for="editEventName">Name</label>
                        <input type="text" class="form-control" id="editEventName" name="editEventName" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="editEventDescription">Description</label>
                        <textarea class="form-control" id="editEventDescription" name="editEventDescription" required></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="editEventDate">Date</label>
                        <input type="date" class="form-control" id="editEventDate" name="editEventDate" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="editEventTime">Time</label>
                        <input type="time" class="form-control" id="editEventTime" name="editEventTime" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="editEventLocation">Location</label>
                        <input type="text" class="form-control" id="editEventLocation" name="editEventLocation" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="editEventCapacity">Max Capacity</label>
                        <input type="number" class="form-control" id="editEventCapacity" name="editEventCapacity" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="updateEvent()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Attendees Modal -->
<div class="modal fade" id="attendeesModal" tabindex="-1" aria-labelledby="attendeesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="attendeesModalLabel">Attendees</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table id="attendeesTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be populated dynamically -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- DataTables and Bootstrap JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

<!-- Custom JS -->
<script>
    // Initialize DataTable
    $(document).ready(function() {
        $('#eventsTable').DataTable({
            ajax: {
                url: '/ems/api/events', // API endpoint to fetch events
                dataSrc: ''
            },
            columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'description' },
                { data: 'date' },
                { data: 'time' },
                { data: 'location' },
                { data: 'max_capacity' },
                {
                    data: null,
                    render: function(data) {
                        return `
                            <button class="btn btn-primary btn-sm" onclick="viewAttendees(${data.id})">Attendees</button>
                            <button class="btn btn-warning btn-sm" onclick="editEvent(${data.id})">Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteEvent(${data.id})">Delete</button>
                        `;
                    }
                }
            ]
        });
    });

    // Add Event
    function addEvent() {
        const formData = {
            name: $('#eventName').val(),
            description: $('#eventDescription').val(),
            date: $('#eventDate').val(),
            time: $('#eventTime').val(),
            location: $('#eventLocation').val(),
            max_capacity: $('#eventCapacity').val()
        };

        fetch('/ems/api/events', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#addEventModal').modal('hide');
                $('#eventsTable').DataTable().ajax.reload();
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Event added successfully!'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Failed to add event.'
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
    }

    // Edit Event
    function editEvent(eventId) {
        fetch(`/ems/api/events/${eventId}`)
        .then(response => response.json())
        .then(data => {
            $('#editEventId').val(data.id);
            $('#editEventName').val(data.name);
            $('#editEventDescription').val(data.description);
            $('#editEventDate').val(data.date);
            $('#editEventTime').val(data.time);
            $('#editEventLocation').val(data.location);
            $('#editEventCapacity').val(data.max_capacity);
            $('#editEventModal').modal('show');
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred. Please try again.'
            });
        });
    }

    // Update Event
    function updateEvent() {
        const formData = {
            id: $('#editEventId').val(),
            name: $('#editEventName').val(),
            description: $('#editEventDescription').val(),
            date: $('#editEventDate').val(),
            time: $('#editEventTime').val(),
            location: $('#editEventLocation').val(),
            max_capacity: $('#editEventCapacity').val()
        };

        fetch(`/ems/api/events/${formData.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#editEventModal').modal('hide');
                $('#eventsTable').DataTable().ajax.reload();
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Event updated successfully!'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Failed to update event.'
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
    }

    // Delete Event
    function deleteEvent(eventId) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You will not be able to recover this event!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/ems/api/events/${eventId}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        $('#eventsTable').DataTable().ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: 'Event deleted successfully.'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Failed to delete event.'
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
            }
        });
    }

    // View Attendees
    function viewAttendees(eventId) {
        fetch(`/ems/api/events/${eventId}/attendees`)
        .then(response => response.json())
        .then(data => {
            $('#attendeesTable').DataTable().clear().rows.add(data).draw();
            $('#attendeesModal').modal('show');
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred. Please try again.'
            });
        });
    }
</script>

<?php include('../footer.php'); ?>