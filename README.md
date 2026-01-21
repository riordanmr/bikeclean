# BikeClean - Bike Repair Checklist System

A mobile-first PHP web application for managing bike repairs at a bike shop. Track mechanics, bikes, and repair checklists with automatic AJAX updates.

## Features

- **Mechanics Management**: Add, edit, and delete mechanics
- **Bike Management**: Add, edit, delete, and assign bikes to mechanics
- **Interactive Checklist**: Mobile-optimized checklist with 19 repair items
- **Auto-Save**: Automatically saves changes every 10 seconds via AJAX
- **Progress Tracking**: Visual progress bar showing repair completion
- **Mobile-First Design**: Optimized for use on mobile devices in the shop

## Repair Checklist Items

1. Frame: Clean
2. Wheels: Clean
3. Wheels: True
4. Spokes: Clean
5. Kickstand: Tighten
6. Seat: Inspect
7. Tires: Straighten Valve Stems
8. Tires: Inflate
9. Rear Derailleur: Clean and Adjust
10. Cassette: Clean
11. Chain: Clean
12. Chainrings: Clean
13. Front Derailleur: Clean and Adjust
14. Cranks: Clean and Tighten
15. Pedals: Clean and Tighten
16. Headset: Tighten
17. Brakes: Lubricate and Adjust
18. Reflectors: Check for Front and Back
19. Chrome: Clean with Scotch-Brite

## Installation

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache, Nginx, or PHP built-in server)

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd bikeclean
   ```

2. **Create the database**
   ```bash
   mysql -u root -p < schema.sql
   ```
   
   Or manually:
   - Open MySQL command line or phpMyAdmin
   - Run the SQL commands in `schema.sql`

3. **Configure database connection**
   
   Edit `config.php` and update the database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'bikeclean');
   ```

4. **Start the web server**

   **Option A: PHP Built-in Server (Development)**
   ```bash
   php -S localhost:8000
   ```
   
   **Option B: Apache/Nginx**
   - Copy files to your web root directory (e.g., `/var/www/html/bikeclean`)
   - Configure virtual host to point to the directory
   - Ensure proper permissions

5. **Access the application**
   
   Open your browser and navigate to:
   - PHP built-in: `http://localhost:8000`
   - Apache/Nginx: `http://localhost/bikeclean` (or your configured URL)

## File Structure

```
bikeclean/
├── config.php          # Database configuration
├── schema.sql          # Database schema
├── index.php           # Main dashboard
├── mechanics.php       # Mechanics management
├── bikes.php           # Bikes management
├── checklist.php       # Bike repair checklist
├── update_bike.php     # AJAX endpoint for updates
├── style.css           # Mobile-first CSS styles
├── LICENSE             # License file
└── README.md           # This file
```

## Usage

### Managing Mechanics

1. Navigate to "Manage Mechanics"
2. Add mechanics using the form
3. Edit or delete existing mechanics as needed

### Managing Bikes

1. Navigate to "Manage Bikes"
2. Add a new bike with description (e.g., "Red Trek Mountain Bike - Customer: John Doe")
3. Assign a mechanic to the bike (optional)
4. Click "Checklist" to access the repair checklist

### Using the Checklist

1. Access a bike's checklist from the Bikes page
2. Check off items as repairs are completed
3. Changes are automatically saved every 10 seconds
4. Progress bar shows completion percentage
5. Status indicator shows save state

## Database Schema

### mechanics table
- `id` (INT, AUTO_INCREMENT, PRIMARY KEY)
- `full_name` (VARCHAR(100))
- `created_at` (TIMESTAMP)

### bikes table
- `id` (INT, AUTO_INCREMENT, PRIMARY KEY)
- `mechanic_id` (INT, FOREIGN KEY)
- `description` (VARCHAR(255))
- 19 repair item columns (TINYINT(1), default 0)
- `created_at` (TIMESTAMP)
- `updated_at` (TIMESTAMP)

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **AJAX**: Fetch API
- **Design**: Mobile-first responsive design

## Browser Support

- Chrome/Edge (latest)
- Firefox (latest)
- Safari (iOS 12+)
- Chrome Mobile (Android)

## License

See LICENSE file for details.

## Support

For issues or questions, please open an issue in the repository.
Web app to act as a checklist when cleaning a bike at a bike shop
