<?php
session_start();

if (!isset($_SESSION['username'])) {
    http_response_code(302);
    header("Location: ../login.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventify</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.0/css/buttons.dataTables.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar {
            background-color: #343a40 !important;
        }

        .navbar-brand,
        .nav-link {
            color: #ffffff !important;
        }

        .card {
            background-color: #ffffff;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            color: #343a40;
        }

        .card-text {
            color: #6c757d;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-danger {
            background-color: #dc3545;
            border: none;
        }

        .btn-info {
            background-color: #17a2b8;
            border: none;
        }

        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }

        .modal-content {
            border-radius: 10px;
        }

        .modal-header {
            background-color: #343a40;
            color: #ffffff;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .modal-title {
            color: #ffffff;
        }

        .btn-close {
            color: #ffffff;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-calendar-alt"></i> Eventify</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarText">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#"><i class="fas fa-home"></i> Home</a>
                    </li>
                </ul>
                <form action="/ems/api/logout" method="POST"><button type="submit" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</button></form>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Event Dashboard</h2>

        <div class="row">
            <div class="col-md-3">
                <div class="card" style="width: 18rem;">
                    <div class="card-body mb-3">
                        <h5 class="card-title"><i class="fas fa-calendar-check"></i> Total Events</h5>
                        <p class="card-text" id="totalEvents"></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card" style="width: 18rem;">
                    <div class="card-body mb-3">
                        <h5 class="card-title"><i class="fas fa-users"></i> Total Attendees</h5>
                        <p class="card-text" id="totalAttendees"></p>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div>
            <h2>Event Details</h2>
            <button id="addEventBtn" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Add Event</button>
            <table id="eventsTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Event Name</th>
                        <th>Event Date</th>
                        <th>Location</th>
                        <th>Time</th>
                        <th>Max Capacity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated here -->
                </tbody>
            </table>
        </div>

        <div>
            <h2>Attendees</h2>
            <table id="attendeesTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Event Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated here -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Event Modal -->
    <div class="modal fade" id="addEventModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="eventForm">
                        <div class="form-group">
                            <label for="eventName">Event Name</label>
                            <input type="text" class="form-control" id="eventName" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Event Description</label>
                            <textarea name="description" id="description" class="form-control"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="eventDate">Event Date</label>
                            <input type="date" class="form-control" id="eventDate" name="date" required>
                        </div>

                        <div class="form-group">
                            <label for="eventTime">Event Time</label>
                            <input type="time" class="form-control" id="eventTime" name="time" required>
                        </div>

                        <div class="form-group">
                            <label for="eventLocation">Event Location</label>
                            <input type="text" class="form-control" id="eventLocation" name="location" required>
                        </div>

                        <div class="form-group">
                            <label for="maxCapacity">Max Capacity</label>
                            <input type="number" class="form-control" id="maxCapacity" name="maxCapacity" required>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveEventBtn">Save Event</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Description Modal -->
    <div class="modal fade" id="viewDescriptionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Event Description</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="eventDescription"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Event Modal -->
    <div class="modal fade" id="updateEventModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateEventForm">
                        <input type="hidden" id="editEventId" name="id"> <!-- Hidden field for event ID -->
                        <div class="form-group">
                            <label for="editEventName">Event Name</label>
                            <input type="text" class="form-control" id="editEventName" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="editDescription">Event Description</label>
                            <textarea name="description" id="editDescription" class="form-control"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="editEventDate">Event Date</label>
                            <input type="date" class="form-control" id="editEventDate" name="date" required>
                        </div>

                        <div class="form-group">
                            <label for="editEventTime">Event Time</label>
                            <input type="time" class="form-control" id="editEventTime" name="time" required>
                        </div>

                        <div class="form-group">
                            <label for="editEventLocation">Event Location</label>
                            <input type="text" class="form-control" id="editEventLocation" name="location" required>
                        </div>

                        <div class="form-group">
                            <label for="editMaxCapacity">Max Capacity</label>
                            <input type="number" class="form-control" id="editMaxCapacity" name="maxCapacity" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="updateEventBtn">Update Event</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.print.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable for events
            var table = $('#eventsTable').DataTable({
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                layout: {
                    topStart: 'buttons'
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'date'
                    },
                    {
                        data: 'location'
                    },
                    {
                        data: 'time'
                    },
                    {
                        data: 'max_capacity'
                    },
                    {
                        data: null,
                        render: function(data) {
                            return `
                <button class="btn btn-info btn-sm view-btn" data-id="${data.id}" data-description="${data.description}"><i class="fas fa-eye"></i></button>
                <button class="btn btn-success btn-sm edit-btn" data-id="${data.id}"><i class="fas fa-edit"></i></button>
                <button class="btn btn-danger btn-sm delete-btn" data-id="${data.id}"><i class="fas fa-trash"></i></button>
              `;
                        }
                    }
                ]

            });

            // Fetch events and populate the table
            function fetchEvents() {
                $.ajax({
                    url: '/ems/api/events', // Endpoint to fetch events
                    method: 'GET',
                    success: function(response) {
                        // Clear the table and re-populate it with new data
                        table.clear();
                        table.rows.add(response).draw();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching events:', error);
                    }
                });
            }

            // Open Add Event Modal
            $('#addEventBtn').click(function() {
                $('#addEventModal').modal('show');
            });

            // Save Event
            $('#saveEventBtn').click(function() {
                var eventName = $('#eventName').val();
                var description = $('#description').val();
                var location = $('#eventLocation').val();
                var time = $('#eventTime').val();
                var maxCapacity = $('#maxCapacity').val();
                var eventDate = $('#eventDate').val();

                if (eventName && eventDate) {
                    // Create an object with the event data
                    var eventData = {
                        name: eventName,
                        description: description,
                        date: eventDate,
                        time: time,
                        location: location,
                        max_capacity: maxCapacity
                    };

                    $.ajax({
                        url: '/ems/api/event/create', // Endpoint to add event
                        method: 'POST',
                        contentType: 'application/json', // Set the content type to JSON
                        data: JSON.stringify(eventData), // Convert the data to JSON format
                        success: function(response) {
                            $('#addEventModal').modal('hide');
                            if (response['succes'] === false) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response['message']
                                });
                                return;
                            } else {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response['message']
                                });
                            }
                            fetchEvents(); // Refresh the table after adding an event
                            fetchTotalEvents();
                            $('#eventForm')[0].reset(); // Reset form
                        },
                        error: function(xhr, status, error) {
                            console.error('Error adding event:', error);
                        }
                    });
                }
            });

            // Delete Event
            $('#eventsTable').on('click', '.delete-btn', function() {
                var eventId = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) { // Check if the user confirmed the action
                        $.ajax({
                            url: '/ems/api/event/delete', // Endpoint to delete event
                            method: 'DELETE',
                            contentType: 'application/json', // Set content type to JSON
                            data: JSON.stringify({
                                id: eventId
                            }), // Send data as JSON
                            success: function(response) {
                                fetchEvents(); // Refresh the table after deletion
                                fetchAttendees();
                                fetchTotalAttendees();
                                fetchTotalEvents();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Event has been deleted.'
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error('Error deleting event:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Failed to delete event.'
                                });
                            }
                        });
                    }
                });
            });

            // View Description
            $('#eventsTable').on('click', '.view-btn', function() {
                var description = $(this).data('description');
                $('#eventDescription').text(description); // Set the description in the modal
                $('#viewDescriptionModal').modal('show'); // Show the modal
            });

            // Edit event details & get the data from the event table
            $('#eventsTable').on('click', '.edit-btn', function() {
                var row = $(this).closest('tr');
                var data = table.row(row).data(); // Get the row data

                // Populate the update modal fields
                $('#editEventId').val(data.id || '');
                $('#editEventName').val(data.name || '');
                $('#editDescription').val(data.description || '');
                $('#editEventDate').val(data.date || '');
                $('#editEventTime').val(data.time || '');
                $('#editEventLocation').val(data.location || '');
                $('#editMaxCapacity').val(data.max_capacity || ''); // Ensure correct field name

                $('#updateEventModal').modal('show'); // Show the update modal
            });

            // Update Event
            $('#updateEventBtn').click(function() {
                var eventId = $('#editEventId').val();
                var eventName = $('#editEventName').val();
                var description = $('#editDescription').val();
                var eventDate = $('#editEventDate').val();
                var time = $('#editEventTime').val();
                var location = $('#editEventLocation').val();
                var maxCapacity = $('#editMaxCapacity').val();

                if (eventName && eventDate) {
                    // Create an object with the updated event data
                    var eventData = {
                        id: eventId,
                        name: eventName,
                        description: description,
                        date: eventDate,
                        time: time,
                        location: location,
                        max_capacity: maxCapacity
                    };

                    $.ajax({
                        url: '/ems/api/event/update', // Endpoint to update event
                        method: 'PUT',
                        contentType: 'application/json', // Set the content type to JSON
                        data: JSON.stringify(eventData), // Convert the data to JSON format
                        success: function(response) {
                            $('#updateEventModal').modal('hide');
                            if (response['success'] === false) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response['message']
                                });
                                return;
                            } else {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response['message']
                                });
                            }
                            fetchEvents(); // Refresh the table after updating
                        },
                        error: function(xhr, status, error) {
                            console.error('Error updating event:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to update event.'
                            });
                        }
                    });
                }
            });

            // Initialize DataTable for attendees
            var attendeesTable = $('#attendeesTable').DataTable({
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                layout: {
                    topStart: 'buttons'
                },
                columns: [{
                        data: 'attendee_id'
                    },
                    {
                        data: 'attendee_name'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'event_name'
                    },
                    {
                        data: null,
                        render: function(data) {
                            return `<button class="btn btn-danger btn-sm delete-btn" data-attendee-id="${data.attendee_id}" data-event-id="${data.event_id}"><i class="fas fa-trash"></i> Delete</button>`;
                        }
                    }
                ]
            });

            // Fetch attendees and populate the table
            function fetchAttendees() {
                $.ajax({
                    url: '/ems/api/attendees', // Endpoint to fetch attendees
                    method: 'GET',
                    success: function(response) {
                        console.log(response);
                        // Clear the table and re-populate it with new data
                        attendeesTable.clear();
                        attendeesTable.rows.add(response).draw();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching attendees:', error);
                    }
                });
            }

            // Delete Attendee
            $('#attendeesTable').on('click', '.delete-btn', function() {
                // Retrieve event_id and attendee_id from the button's data attributes
                var attendeeId = $(this).data('attendee-id');
                var eventId = $(this).data('event-id');

                // Confirm deletion with the user
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Send the delete request to the server
                        $.ajax({
                            url: '/ems/api/attendee/delete', // Endpoint to delete attendee
                            method: 'DELETE',
                            contentType: 'application/json',
                            data: JSON.stringify({
                                attendeeId: attendeeId,
                                eventId: eventId
                            }),
                            success: function(response) {
                                // Refresh the table after deletion
                                fetchAttendees();
                                fetchTotalAttendees();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Attendee has been deleted.'
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error('Error deleting attendee:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Failed to delete attendee.'
                                });
                            }
                        });
                    }
                });
            });

            // Fetch total events and attendees on page load
            function fetchTotalEvents() {
                $.ajax({
                    url: '/ems/api/totalEvents', // Endpoint to fetch total events
                    method: 'GET',
                    success: function(response) {
                        // console.log(response);
                        $('#totalEvents').text(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching total events:', error);
                    }
                });
            }

            function fetchTotalAttendees() {
                $.ajax({
                    url: '/ems/api/totalAttendees', // Endpoint to fetch total attendees
                    method: 'GET',
                    success: function(response) {
                        // console.log(response);
                        $('#totalAttendees').text(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching total attendees:', error);
                    }
                });
            }

            // Fetch events on page load
            fetchEvents();

            // Fetch attendees on page load
            fetchAttendees();

            fetchTotalEvents();
            fetchTotalAttendees();


        });
    </script>
</body>

</html>