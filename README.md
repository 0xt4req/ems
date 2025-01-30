# Event Management System

## Overview
This is a simple web-based Event Management System built with pure PHP and MySQL. It allows authenticated users to create, manage, and view events while enabling attendees to register for events.

## Features
### Core Functionalities
- **User Authentication**: Secure login and registration with password hashing.
- **Event Management**: Authenticated users can create, update, view, and delete events with details such as name and description.
- **Attendee Registration**: Attendees can register for events, ensuring registrations do not exceed the event capacity.
- **Event Dashboard**: Events are displayed in a paginated, sortable, and filterable format.
- **Event Reports**: Admins can download attendee lists for specific events in CSV format.
- **Search functionality** across events and attendees.
- **AJAX integration** to enhance user experience during event registration.
- **JSON API endpoint** to fetch event details programmatically.

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
   - Open `Database.php` under classes folder and set the database connection details.
   - Navigate to /init.php endpoint. The database will be created alogin with an default admin user. ( Make sure to delete or change this this later for production use.)

3. **Start the Server**
   - If using built-in PHP server:
     ```sh
     php -S localhost:8000
     ```
   - Otherwise, deploy to an Apache server.

## Usage
- Register a new account.
- Log in to create, update, manage events & attendees.
- Attendees can register for events.
- Admins & users can donwload event lists along with the attendees.

## Security Considerations
- Passwords are hashed before storing.
- All database interactions use **prepared statements** to prevent SQL injection.
- Input validation is performed both client-side and server-side.
- Maintaining role-based access control (RBAC) for the API calls.

## Future Enhancements
- Add password reset functionalities for the users & admins.
- Add email notifications for event confirmations.

## License
This project is licensed under the MIT License.

## Contact
For any issues or feature requests, feel free to reach out or create an issue in the repository.

