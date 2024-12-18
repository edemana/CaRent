# CarRent - Modern Car Rental Service

CarRent is a comprehensive web-based car rental management system that allows users to browse, book, and manage car rentals with ease. The system provides both customer and administrative interfaces with robust features for managing the entire rental process.

## Features

### For Customers
- Browse featured cars with detailed information
- Advanced search and filtering options
- User registration and authentication
- Profile management
- Booking management
- Rental history tracking
- Real-time car availability checking

### For Administrators
- Dashboard with analytics
- Car fleet management
- Booking management
- User management
- Reports generation
- Settings configuration

## Technical Stack

- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Server**: Apache (XAMPP)
- **Additional Libraries**: 
  - Font Awesome 6.0.0
  - Modern UI Framework

## Database Schema

### Core Tables
1. **users**
   - User_id (PK)
   - Fname
   - Email
   - Password
   - Phone
   - Image
   - User_type

2. **car**
   - Vehicle_id (PK)
   - Model
   - Description
   - RentalPrice
   - Img
   - Status
   - Type_id (FK)

3. **cartype**
   - Type_id (PK)
   - Name
   - Description

4. **bookings**
   - Booking_id (PK)
   - User_id (FK)
   - Vehicle_id (FK)
   - Start_date
   - End_date
   - Status
   - Total_amount

## Installation

1. Install XAMPP (version 7.4 or higher)
2. Clone the repository to your htdocs folder:
   ```bash
   cd c:/xampp/htdocs
   git clone [repository-url] mycarent
   ```
3. Import the database:
   - Start Apache and MySQL in XAMPP
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named 'mycarent'
   - Import the SQL file from the 'database' folder

4. Configure the database connection:
   - Open `php/config.php`
   - Update the database credentials if necessary

5. Start the application:
   - Open your browser
   - Navigate to http://localhost/mycarent

## User Journey

### Customer Journey
1. **Discovery**
   - Visit homepage
   - Browse featured cars
   - View car details

2. **Account Creation**
   - Register new account
   - Verify email
   - Complete profile

3. **Car Selection**
   - Search for cars
   - Apply filters
   - Compare options
   - View availability

4. **Booking Process**
   - Select dates
   - Review pricing
   - Confirm booking
   - Make payment

5. **Rental Management**
   - View booking details
   - Track rental status
   - Manage bookings
   - View history

### Admin Journey
1. **Dashboard Overview**
   - View statistics
   - Monitor bookings
   - Track revenue

2. **Fleet Management**
   - Add new cars
   - Update car details
   - Manage availability
   - Set pricing

3. **Booking Management**
   - Review new bookings
   - Update status
   - Handle modifications
   - Process returns

## Security Features

- Password hashing
- Prepared SQL statements
- Session management
- Input validation
- XSS protection
- CSRF protection

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, please email support@carrent.com or open an issue in the repository.
