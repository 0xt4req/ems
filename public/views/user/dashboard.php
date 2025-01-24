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
    <title>Event Dashboard</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.0/css/buttons.dataTables.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>
    <div class="container mt-5">
        <h2>Event Dashboard</h2>
        <button id="addEventBtn" class="btn btn-primary mb-3">Add Event</button>
        <table id="eventsTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Event Name</th>
                    <th>Event Date</th>
                    <th>Location</th>
                    <th>Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be populated here -->
            </tbody>
        </table>
    </div>

    <!-- Add Event Modal -->
    <div class="modal fade" id="addEventModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Event</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p id="eventDescription"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
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
                        data: null,
                        render: function(data) {
                            return `
                <button class="btn btn-info btn-sm view-btn" data-id="${data.id}" data-description="${data.description}">View Details</button>
                <button class="btn btn-danger btn-sm delete-btn" data-id="${data.id}">Delete</button>
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

            // Fetch events on page load
            fetchEvents();

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
        });
    </script>
</body>

</html>