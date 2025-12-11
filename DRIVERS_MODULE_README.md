# Drivers Module Documentation

## Overview

The Drivers module is a complete replica of the Admin module structure, providing comprehensive driver management functionality including CRUD operations, responsive design, and integration with the existing KengGo shuttle system.

## Project Structure

```
app/driver/
├── controller/
│   └── DriverController.php          # Driver business logic & login
├── model/
│   └── Driver.php                    # Database operations
└── view/
    ├── css/
    │   └── driver_dashboard.css      # Responsive styling (phone/tablet/desktop)
    └── html/
        ├── dashboard.html            # Driver dashboard (assigned trips)
        ├── drivers_list.html         # Admin list of all drivers
        ├── create_driver.html        # Add new driver form
        ├── edit_driver.html          # Edit driver details
        ├── profile.html              # Driver profile view
        └── login.html                # Driver login (existing)

handlers/
└── driver_api.php                    # RESTful API for driver operations

database/
└── kenggo.sql                        # Updated schema with new drivers table
```

## Database Schema

### Updated `drivers` Table

```sql
CREATE TABLE drivers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  driver_code VARCHAR(20) UNIQUE NOT NULL,        -- Auto-generated code (DRV-XXXX)
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,                 -- Bcrypt hashed
  license_number VARCHAR(50) UNIQUE NOT NULL,
  license_expiry DATE,
  phone VARCHAR(20),
  vehicle_number VARCHAR(50),
  plate_number VARCHAR(50),
  status ENUM('active','inactive','suspended'),
  experience_years INT DEFAULT 0,
  rating DECIMAL(3,2) DEFAULT 0.00,
  total_trips INT DEFAULT 0,
  last_login TIMESTAMP NULL,
  notes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## API Endpoints

All endpoints are in `handlers/driver_api.php`:

### List All Drivers
- **Endpoint:** `GET /handlers/driver_api.php?action=list`
- **Response:** JSON array of all drivers
- **Example:**
```javascript
fetch('/handlers/driver_api.php?action=list')
  .then(r => r.json())
  .then(data => console.log(data.data))
```

### Create Driver
- **Endpoint:** `POST /handlers/driver_api.php?action=create`
- **Parameters:**
  - `name` (required)
  - `email` (required, unique)
  - `password` (required, 6+ chars)
  - `license_number` (required, unique)
  - `license_expiry` (optional, date)
  - `phone` (optional)
  - `vehicle_number` (optional)
  - `plate_number` (optional)
  - `experience_years` (optional, int)
  - `status` (optional, default: 'active')
  - `notes` (optional)

### Get Driver by ID
- **Endpoint:** `GET /handlers/driver_api.php?action=view&id=<driver_id>`
- **Response:** Single driver object

### Edit Driver
- **Endpoint:** `POST /handlers/driver_api.php?action=edit&id=<driver_id>` (GET for fetch)
- **GET:** Retrieve driver data for editing
- **POST:** Submit updates with same parameters as create (except password/email/license read-only)

### Delete Driver
- **Endpoint:** `POST /handlers/driver_api.php?action=delete` with `driver_id` parameter
- **GET:** List all drivers for deletion interface
- **POST:** Delete specific driver

### Search Drivers
- **Endpoint:** `GET /handlers/driver_api.php?action=search&q=<query>`
- **Searches:** name, email, license_number, driver_code, phone
- **Response:** Array of matching drivers

### Get Statistics
- **Endpoint:** `GET /handlers/driver_api.php?action=stats`
- **Response:** 
```json
{
  "success": true,
  "data": {
    "total": 10,
    "active": 8,
    "inactive": 1,
    "suspended": 1
  }
}
```

## Frontend Pages

### Dashboard (`dashboard.html`)
- **Purpose:** Driver's personal dashboard showing assigned trips
- **Features:**
  - Welcome card with driver code
  - List of assigned trips/shuttles
  - Quick navigation to trips, profile, notifications
  - Bottom navigation bar

### Drivers List (`drivers_list.html`)
- **Purpose:** Admin management view of all drivers
- **Features:**
  - Grid view of all drivers
  - Search functionality
  - Add driver button
  - Click to edit driver
  - Status indicators (Active/Inactive/Suspended)
  - Rating and trip count display

### Create Driver (`create_driver.html`)
- **Purpose:** Add new driver to system
- **Fields:**
  - Driver Name (required)
  - Email (required, unique)
  - License Number (required, unique)
  - License Expiry (optional)
  - Phone Number (optional)
  - Experience Years (optional)
  - Vehicle Number (optional)
  - Plate Number (optional)
  - Password (required)
  - Status (Active/Inactive/Suspended)
  - Notes (optional)
- **Features:**
  - Form validation
  - Confirmation modal before submission
  - Toast notifications for success/error

### Edit Driver (`edit_driver.html`)
- **Purpose:** Update driver information
- **Features:**
  - Pre-fills current driver data
  - Read-only license number and email
  - Editable: name, phone, license expiry, vehicle, plate, experience, status, notes
  - Confirmation before saving

### Profile (`profile.html`)
- **Purpose:** Driver's personal profile view
- **Sections:**
  - Personal Information (code, email, phone)
  - License & Vehicle (license number, expiry, vehicle, plate)
  - Experience & Rating (years, rating, total trips)
  - Account Activity (last login, status)
- **Actions:**
  - Edit profile (placeholder)
  - Logout with confirmation

## Responsive Design

CSS includes media queries for:
- **Desktop (1025px+):** 2-column grids, full spacing
- **Tablet (701px-1024px):** Adaptive grids, wrapped layouts
- **Phone (≤700px):** Single column, stacked forms, optimized spacing
- **Extra Small (≤480px):** Minimal margins, full-width elements

Key responsive classes:
- `.drivers-grid` - Auto-fits columns
- `.form-row.two-col` - Flex wraps on mobile
- `.form-actions` - Stacks vertically on phone
- `.bottom-nav` - Adjusts padding for small screens

## Authentication

### Login (`DriverController->login()`)
```php
$controller = new DriverController($conn);
$result = $controller->login('email@example.com', 'password');
// Sets session: driver_id, driver_email, driver_name, driver_code
```

### Registration (`DriverController->register()`)
```php
$result = $controller->register('John Doe', 'john@example.com', 'password123', 'DL-2025-001');
```

## Session Management

Driver sessions store:
- `driver_id` - Numeric ID
- `driver_email` - Email address
- `driver_name` - Full name
- `driver_code` - Unique driver code (DRV-XXXX)

## Code Patterns

### Model Usage
```php
require_once '../app/driver/model/Driver.php';
$driver_model = new Driver($conn);

// Get all drivers
$drivers = $driver_model->getAllDrivers();

// Get single driver
$driver = $driver_model->getDriverById(1);

// Create driver
$result = $driver_model->createDriver([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => 'secure_pass',
    'license_number' => 'DL-2025-001'
]);

// Update driver
$result = $driver_model->updateDriver(1, ['name' => 'Jane Doe']);

// Delete driver
$result = $driver_model->deleteDriver(1);

// Search drivers
$results = $driver_model->searchDrivers('John');

// Get stats
$stats = $driver_model->getStatistics();
```

### API Usage in JavaScript
```javascript
// List drivers
fetch('/handlers/driver_api.php?action=list')
  .then(r => r.json())
  .then(data => console.log(data.data))

// Create driver
const formData = new FormData();
formData.append('name', 'John Doe');
formData.append('email', 'john@example.com');
formData.append('password', 'pass123');
formData.append('license_number', 'DL-001');

fetch('/handlers/driver_api.php?action=create', {
  method: 'POST',
  body: formData
})
  .then(r => r.json())
  .then(data => console.log(data))

// Search drivers
fetch('/handlers/driver_api.php?action=search&q=John')
  .then(r => r.json())
  .then(data => console.log(data.data))
```

## Error Handling

All endpoints return JSON with consistent structure:
```json
{
  "success": true/false,
  "message": "Human-readable message",
  "data": {} // Optional, contains result data
}
```

HTTP Status Codes:
- `200` - Success
- `400` - Bad request (invalid parameters)
- `404` - Not found (driver doesn't exist)
- `405` - Method not allowed (POST when GET expected)
- `500` - Server error

## Security Features

1. **Password Hashing:** Bcrypt with cost factor 10
2. **Input Validation:** All inputs trimmed and validated
3. **SQL Injection Prevention:** Prepared statements used throughout
4. **Email Validation:** Uses PHP's filter_var()
5. **Unique Constraints:** Database enforces uniqueness on email, license_number, driver_code
6. **Session Management:** Uses custom Session class (included)

## Integration Checklist

- [x] Database schema created with proper relationships
- [x] Model class with full CRUD operations
- [x] Controller with authentication logic
- [x] RESTful API handler
- [x] Frontend pages (dashboard, list, create, edit, profile)
- [x] Responsive CSS (phone/tablet/desktop)
- [x] Form validation and confirmation modals
- [x] Toast notifications
- [x] Search functionality
- [x] Statistics endpoint

## Testing

### Manual Testing Steps

1. **Create Driver:**
   - Navigate to `drivers_list.html`
   - Click "Add" button
   - Fill form and submit
   - Verify driver appears in list

2. **Edit Driver:**
   - Click on driver card
   - Update information
   - Verify changes saved

3. **Delete Driver:**
   - Click driver on delete page
   - Confirm deletion
   - Verify removed from list

4. **Search:**
   - Enter search query
   - Verify results filtered correctly

5. **Responsive Design:**
   - View on phone (max-width: 480px)
   - View on tablet (max-width: 700px)
   - View on desktop (1024px+)
   - Verify layout adjusts correctly

### API Testing with cURL

```bash
# Get all drivers
curl http://localhost:3307/handlers/driver_api.php?action=list

# Create driver
curl -X POST http://localhost:3307/handlers/driver_api.php?action=create \
  -d "name=John&email=john@test.com&password=pass123&license_number=DL-001"

# Get driver
curl http://localhost:3307/handlers/driver_api.php?action=view&id=1

# Edit driver
curl -X POST http://localhost:3307/handlers/driver_api.php?action=edit \
  -d "driver_id=1&name=Jane"

# Delete driver
curl -X POST http://localhost:3307/handlers/driver_api.php?action=delete \
  -d "driver_id=1"

# Search
curl http://localhost:3307/handlers/driver_api.php?action=search&q=John

# Stats
curl http://localhost:3307/handlers/driver_api.php?action=stats
```

## Future Enhancements

- [ ] Driver document upload (license scan, insurance)
- [ ] Rating/review system from passengers
- [ ] Trip history and analytics
- [ ] Performance metrics dashboard
- [ ] Email notifications for trip assignments
- [ ] GPS tracking integration
- [ ] Mobile app push notifications
- [ ] Integration with payroll system

## Support

For issues or questions:
1. Check error messages in browser console
2. Review server logs for backend errors
3. Verify database connection
4. Ensure all file paths are correct
5. Check Session.php is properly included

## Version History

- **v1.0** (2025-12-11) - Initial release
  - Complete CRUD operations
  - Responsive design
  - RESTful API
  - Authentication system
