# Laravel Controllers for Airline Management System

## Overview
I have created a comprehensive set of Laravel controllers for your airline management system. Each controller provides full CRUD operations and specialized methods for managing different aspects of the airline system.

## Controllers Created

### 1. **PassengerController** (`app/Http/Controllers/PassengerController.php`)
**Purpose**: Manages passenger information and profiles
**Key Features**:
- Full CRUD operations for passengers
- Search passengers by name, email, passport number
- Filter by nationality, frequent flyer status
- Get passenger booking history
- Check passport expiry warnings
- Passenger profile management

**Main Methods**:
- `index()` - List passengers with search and filters
- `store()` - Create new passenger
- `show()` - Get passenger details
- `update()` - Update passenger information
- `destroy()` - Delete passenger
- `bookings()` - Get passenger's booking history
- `checkPassportExpiry()` - Check expiring passports

### 2. **AirlineController** (`app/Http/Controllers/AirlineController.php`)
**Purpose**: Manages airline companies and their information
**Key Features**:
- CRUD operations for airlines
- Search by name, codes, country
- Filter by active status
- Get airline's aircraft fleet
- Get airline's flight schedules
- Manage airline operational data

**Main Methods**:
- `index()` - List airlines with search/filter
- `store()` - Create new airline
- `show()` - Get airline details
- `update()` - Update airline information
- `destroy()` - Delete airline
- `aircraft()` - Get airline's fleet
- `flights()` - Get airline's flights
- `active()` - Get only active airlines

### 3. **AircraftController** (`app/Http/Controllers/AircraftController.php`)
**Purpose**: Manages aircraft fleet and maintenance
**Key Features**:
- Aircraft fleet management
- Maintenance scheduling and tracking
- Seat configuration management
- Aircraft status monitoring
- Fleet utilization tracking

**Main Methods**:
- `index()` - List aircraft with filters
- `store()` - Add new aircraft
- `show()` - Get aircraft details
- `update()` - Update aircraft information
- `destroy()` - Remove aircraft
- `seats()` - Get aircraft seat configuration
- `maintenanceSchedule()` - Get maintenance due
- `updateMaintenance()` - Update maintenance dates
- `byStatus()` - Get aircraft by status

### 4. **AirportController** (`app/Http/Controllers/AirportController.php`)
**Purpose**: Manages airport information and operations
**Key Features**:
- Airport database management
- Geographic search capabilities
- International airport filtering
- Gate management integration
- Location-based services

**Main Methods**:
- `index()` - List airports with search
- `store()` - Add new airport
- `show()` - Get airport details
- `update()` - Update airport information
- `destroy()` - Remove airport
- `byCountry()` - Get airports by country
- `international()` - Get international airports
- `searchByCode()` - Find airport by IATA/ICAO code
- `nearby()` - Find nearby airports by coordinates
- `gates()` - Get airport gates

### 5. **FlightController** (`app/Http/Controllers/FlightController.php`)
**Purpose**: Manages flight schedules and operations
**Key Features**:
- Flight scheduling and management
- Real-time status updates
- Flight search capabilities
- Delay management
- Booking integration

**Main Methods**:
- `index()` - List flights with comprehensive filters
- `store()` - Create new flight
- `show()` - Get flight details with relationships
- `update()` - Update flight information
- `destroy()` - Cancel/remove flight
- `updateStatus()` - Update flight status with logging
- `byDate()` - Get flights by specific date
- `delayed()` - Get delayed flights
- `searchFlights()` - Search available flights for booking
- `statusUpdates()` - Get flight status history

### 6. **BookingController** (`app/Http/Controllers/BookingController.php`)
**Purpose**: Manages flight bookings and reservations
**Key Features**:
- Complete booking lifecycle management
- Payment integration
- Seat availability checking
- Booking reference system
- Multi-class booking support

**Main Methods**:
- `index()` - List bookings with filters
- `store()` - Create new booking with seat availability check
- `show()` - Get booking details with all relationships
- `update()` - Update booking status/details
- `destroy()` - Cancel booking and free seats
- `findByReference()` - Find booking by PNR
- `confirmPayment()` - Process payment confirmation
- `cancel()` - Cancel booking with refund handling
- `byFlight()` - Get all bookings for a flight
- `byPassenger()` - Get passenger's booking history
- `statistics()` - Get booking statistics

### 7. **SeatController** (`app/Http/Controllers/SeatController.php`)
**Purpose**: Manages aircraft seating and assignments
**Key Features**:
- Seat configuration management
- Availability tracking
- Premium seat handling
- Bulk seat operations
- Flight-specific availability

**Main Methods**:
- `index()` - List seats with filters
- `store()` - Add new seat with uniqueness validation
- `show()` - Get seat details
- `update()` - Update seat information
- `destroy()` - Remove seat
- `byAircraft()` - Get all seats for aircraft
- `availableForFlight()` - Get available seats for specific flight
- `bulkCreate()` - Create multiple seats at once
- `seatMap()` - Get visual seat map

### 8. **PaymentController** (`app/Http/Controllers/PaymentController.php`)
**Purpose**: Manages payment processing and financial transactions
**Key Features**:
- Payment processing simulation
- Multiple payment methods
- Refund handling
- Payment status tracking
- Financial reporting

**Main Methods**:
- `index()` - List payments with filters
- `store()` - Record new payment
- `show()` - Get payment details
- `update()` - Update payment status
- `destroy()` - Remove payment record
- `processPayment()` - Simulate payment processing
- `refund()` - Process payment refunds
- `byBooking()` - Get payments for booking
- `statistics()` - Payment analytics
- `byMethod()` - Payment method breakdown

### 9. **CrewMemberController** (`app/Http/Controllers/CrewMemberController.php`)
**Purpose**: Manages airline crew and staff
**Key Features**:
- Crew member database
- License tracking and expiry
- Schedule availability
- Position management
- Department organization

**Main Methods**:
- `index()` - List crew members with filters
- `store()` - Add new crew member
- `show()` - Get crew member details
- `update()` - Update crew information
- `destroy()` - Remove crew member
- `byPosition()` - Get crew by position
- `expiringLicenses()` - Get crew with expiring licenses
- `assignments()` - Get crew flight assignments
- `available()` - Get available crew for date/position
- `statistics()` - Crew statistics and breakdown

### 10. **NotificationController** (`app/Http/Controllers/NotificationController.php`)
**Purpose**: Manages system notifications and communications
**Key Features**:
- Multi-type notification system
- Read/unread status tracking
- Bulk operations
- Automated flight notifications
- Scheduled notifications

**Main Methods**:
- `index()` - List notifications with filters
- `store()` - Create new notification
- `show()` - Get notification details
- `update()` - Update notification
- `destroy()` - Delete notification
- `markAsRead()` - Mark single notification as read
- `markAllAsRead()` - Mark all notifications as read
- `unread()` - Get unread notifications
- `sendFlightStatusNotification()` - Auto-send flight updates
- `sendBookingConfirmation()` - Send booking confirmations
- `statistics()` - Notification analytics
- `cleanupOldNotifications()` - Cleanup old notifications

### 11. **RouteController** (`app/Http/Controllers/RouteController.php`)
**Purpose**: Manages flight routes between airports
**Key Features**:
- Route planning and management
- Distance and duration tracking
- Popular route analytics
- Bulk route operations
- Route search capabilities

**Main Methods**:
- `index()` - List routes with filters
- `store()` - Create new route
- `show()` - Get route details
- `update()` - Update route information
- `destroy()` - Remove route
- `fromAirport()` - Get routes from airport
- `toAirport()` - Get routes to airport
- `searchRoutes()` - Search routes between airports
- `popular()` - Get popular routes
- `statistics()` - Route analytics
- `bulkCreate()` - Create multiple routes

### 12. **DashboardController** (`app/Http/Controllers/DashboardController.php`)
**Purpose**: Provides system overview and analytics
**Key Features**:
- Comprehensive system statistics
- Real-time dashboard data
- Performance metrics
- System alerts
- Activity monitoring

**Main Methods**:
- `overview()` - Complete system statistics
- `recentActivities()` - Recent system activities
- `flightBoard()` - Today's flight status board
- `systemAlerts()` - System warnings and alerts
- `performanceMetrics()` - Key performance indicators

## Key Features Across All Controllers

### 1. **Comprehensive Validation**
- All input data is validated using Laravel's validation rules
- Custom validation for business logic (e.g., seat availability, duplicate routes)
- Proper error messages and status codes

### 2. **Relationship Loading**
- Efficient use of Eloquent relationships
- Eager loading to prevent N+1 queries
- Nested relationship loading for complex data

### 3. **Search and Filtering**
- Advanced search capabilities across relevant fields
- Multiple filter options for data refinement
- Pagination for large datasets

### 4. **Error Handling**
- Proper HTTP status codes
- Consistent error response format
- Business logic validation

### 5. **RESTful Design**
- Standard REST endpoints
- Consistent response structure
- Resource-based URLs

### 6. **Business Logic Integration**
- Seat availability checking
- Payment processing
- Status tracking
- Automated notifications

## Next Steps

1. **Create Models**: Create corresponding Eloquent models for each controller
2. **Database Seeding**: Create seeders to populate the database with sample data
3. **API Routes**: Define routes in `routes/api.php` or `routes/web.php`
4. **Middleware**: Add authentication and authorization middleware
5. **Testing**: Create feature and unit tests for each controller
6. **Documentation**: Generate API documentation using tools like Laravel Passport or Sanctum

## Usage Examples

```php
// Example API endpoints you can create:

// Get all passengers with search
GET /api/passengers?search=john&nationality=US

// Create new booking
POST /api/bookings
{
    "passenger_id": 1,
    "flight_id": 5,
    "booking_class": "economy",
    "total_amount": 299.99
}

// Search flights
GET /api/flights/search?departure_airport=LAX&arrival_airport=JFK&departure_date=2024-12-01

// Get dashboard overview
GET /api/dashboard/overview
```

All controllers are designed to work together as a complete airline management system with proper data relationships and business logic integration.