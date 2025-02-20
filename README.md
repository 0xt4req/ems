# Event Management System

## Overview
This is a simple web-based Event Management System built with pure PHP and MySQL. It allows authenticated users to create, manage, and view events while enabling attendees to register for events.

## API Documentation
[API Documentation](https://documenter.getpostman.com/view/19858182/2sAYX3phTy)

## Features
### Core Functionalities
- **User Authentication**: Secure login and registration with password hashing.
- **Event Management**: Authenticated users can create, update, view, and delete events with details such as name and description.
- **Attendee Registration**: Attendees can register for events, ensuring registrations do not exceed the event capacity.
- **Event Dashboard**: Events are displayed in a paginated, sortable, and filterable format.
- **Event Reports**: Admins & hosts can download attendee lists for specific events in CSV format.
- **Search functionality** across all data.
- **AJAX integration** to enhance user experience I used ajax requests.
- **JSON API endpoint** to fetch details programmatically.
- **Responsive UI**: Built with Bootstrap for a modern and user-friendly interface.
- **Error handling**: Proper error messages and status codes for better error handling.
- **Access control**: Role-based access control (RBAC) for API calls.
- **Database design**: Well-structured and normalized database design.

### Technical Details:
- This project is based on object-oriented PHP.
- Database: **MySQL**.
- Client-side and server-side validation.
- Access control validation.
- Use of **prepared statements** to prevent SQL injection.
- Responsive UI built with **Bootstrap**.
- Detailed setup instructions.

## Installation
### Prerequisites
Ensure you have the following installed:
- PHP (>=7.4 recommended)
- MySQL
- Apache or any PHP-supported web server (eg. xampp)

### Steps
1. **Clone the Repository**
   ```sh
   git clone https://github.com/your-username/ems.git
   cd ems
   ```

2. **Set Up the Database**
   - Make sure to start the xampp server with the Apache & MySQL modules enabled.
   - Open `Database.php` under classes folder and set the database connection details.
   - Navigate to /init.php endpoint. The database will be created alogin with an default admin user. ( Make sure to delete or change this this later for production use.)
   - Run the init.php file to create the database and admin user. You can also run this file to reset the database if needed.
   **Note**: Make sure to update the database connection details in `Database.php` under classes folder & `init.php` under config folder before running the init.php script.

3. **Change the baseurl in api/index.php**

# Admin Details:
- Email: admin@example.com
- Password: password
- Endpoint: http://localhost/ems/public/views/admin

# Host Details:
- Email: tareq@gmail.com
- Password: tareq
- Endpoint: http://localhost/ems/public/views/login.php

# Databaes design

![database design](https://i.ibb.co.com/ZR1JYK4Z/image.png)

**Note**: User means host.


## Usage
- Access the application at http://localhost/ems/.
- Register a new account.
- Log in to create, update, manage events & attendees.
- Attendees can register for events in the homepage.
- Admins & users can donwload event lists along with the attendees.

# Index Page

![Index Page](https://i.ibb.co.com/ycKphBnt/image.png)

# Admin Dashboard

![Admin Dashboard](https://i.ibb.co.com/DD1Cwv7R/image.png)

# Host/User Dashboard

![Host Dashboard](https://i.ibb.co.com/397m3GvW/image.png)

## Security Considerations
- Passwords are hashed before storing.
- All database interactions use **prepared statements** to prevent SQL injection.
- Input validation is performed both client-side and server-side.
- Maintaining role-based access control (RBAC) for the API calls.

## Future Enhancements
- Add password reset functionalities for the users & admins.
- Add email notifications for event confirmations.
- Data flow with the uuid.
- Archive old events.
- User & Admin Profile Section.
- Adding pagination in server-side.

## License
This project is licensed under the MIT License.

## Contact
For any issues or feature requests, feel free to reach out or create an issue in the repository.

