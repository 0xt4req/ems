<?php include('header.php');?>
    <!-- Body Section with Event Cards -->
    <div class="container mt-5">
        <h1 class="text-center text-white mb-4">Upcoming Events</h1>
        <div class="row" id="event-cards">
            <!-- Event cards will be dynamically loaded here -->
            <div class="col-md-4">
                <div class="event-card">
                    <h3>Tech Conference 2023</h3>
                    <p>Join us for the biggest tech event of the year!</p>
                    <p><strong>Date:</strong> 2023-12-15</p>
                    <p><strong>Location:</strong> New York, USA</p>
                    <a href="#" class="btn btn-primary">Register</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="event-card">
                    <h3>Music Festival</h3>
                    <p>A weekend of music, food, and fun!</p>
                    <p><strong>Date:</strong> 2023-11-20</p>
                    <p><strong>Location:</strong> Los Angeles, USA</p>
                    <a href="#" class="btn btn-primary">Register</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="event-card">
                    <h3>Startup Pitch Night</h3>
                    <p>Witness the next big ideas in tech!</p>
                    <p><strong>Date:</strong> 2023-10-30</p>
                    <p><strong>Location:</strong> San Francisco, USA</p>
                    <a href="#" class="btn btn-primary">Register</a>
                </div>
            </div>
        </div>
    </div>

<?php include('footer.php');?>