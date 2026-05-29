# Shipping Management System

A PHP-based shipping and freight management system for tracking shipments, managing bills of lading, and coordinating with agents and brokers.

## 🚀 Features

- **Shipment Management**: Create and track shipments
- **Bill of Lading**: Generate and manage bills of lading
- **Agent Management**: Manage shipping agents
- **Broker Management**: Coordinate with shipping brokers
- **Naval Line Management**: Manage naval line information
- **Company Management**: Manage shipping companies
- **Freight Charges**: Calculate and manage freight charges
- **Arrival Approval**: Approve shipment arrivals
- **Dashboard**: Overview of shipping operations

## 📊 Architecture

### Technology Stack
- **Backend**: PHP
- **Database**: MySQL
- **Frontend**: HTML, CSS, JavaScript
- **Dependencies**: Composer (PHP package manager)

### Project Structure
```
shipping/
├── index.php                      # Main entry point
├── dashboard.php                  # Dashboard overview
├── db_config.php                  # Database configuration
├── schema.sql                     # Database schema
├── schema_init.sql                # Initial database setup
├── add_*.php                      # Add forms (agent, broker, company, etc.)
├── process_add_*.php              # Process add forms
├── shipment_form.php              # Shipment form
├── search_shipment.php            # Search shipments
├── view_shipment.php              # View shipment details
├── search_bill_of_lading.php      # Search bills of lading
├── view_bill_of_lading.php        # View bill of lading
├── generate_bill_of_lading.php   # Generate bill of lading
├── arrival_dashboard.php          # Arrival management
├── arrival_approval.php          # Approve arrivals
├── print_delivery_order.php       # Print delivery orders
├── styles.css                     # Styles
├── img/                           # Images
├── sql/                           # SQL scripts
└── vendor/                        # Composer dependencies
```

## 🔧 Installation

### Prerequisites

- PHP 7.4+
- MySQL 5.7+
- Apache web server
- Composer

### Setup

```bash
# Clone the repository
git clone <repository-url>
cd shipping

# Install PHP dependencies
composer install

# Configure database
# Edit db_config.php with your database credentials
$host = 'localhost';
$dbname = 'shipping_db';
$username = 'your_username';
$password = 'your_password';

# Import database schema
mysql -u username -p shipping_db < schema.sql
mysql -u username -p shipping_db < schema_init.sql

# Set permissions
chmod -R 755 img
```

## 🚀 Usage

### Accessing the System

- **Dashboard**: `dashboard.php`
- **Main Page**: `index.php`
- **Add Shipment**: `shipment_form.php`
- **Search Shipments**: `search_shipment.php`
- **Arrival Dashboard**: `arrival_dashboard.php`

### Key Functions

- **Add Agent**: `add_agent.php` - Add new shipping agent
- **Add Broker**: `add_broker.php` - Add new shipping broker
- **Add Company**: `add_company.php` - Add new shipping company
- **Add Naval Line**: `add_naval_line.php` - Add naval line information
- **Add Shipment**: `add_new.php` - Create new shipment
- **Generate Bill of Lading**: `generate_bill_of_lading.php` - Generate bill of lading document

## 🗄️ Database Schema

### Core Tables

- **agents**: Shipping agent information
- **brokers**: Shipping broker information
- **companies**: Shipping company information
- **naval_lines**: Naval line details
- **shipments**: Shipment records
- **bill_of_lading**: Bill of lading documents
- **freight_charges**: Freight charge calculations
- **arrival_approvals**: Arrival approval records

## ⚙️ Configuration

### Database Configuration

Edit `db_config.php`:

```php
$host = 'localhost';
$dbname = 'shipping_db';
$username = 'your_username';
$password = 'your_password';
```

## 📝 Dependencies

```json
{
  "require": {
    "php": ">=7.4"
  }
}
```

## 🔒 Security

- Input validation and sanitization
- SQL injection prevention
- Session management
- File upload security

## 🚧 Production Deployment

### Deployment Checklist

1. Configure production database
2. Set up SSL/HTTPS
3. Configure error reporting
4. Set proper file permissions
5. Configure backup strategy
6. Set up monitoring

## 📧 Support

For questions or support, please open an issue in the repository.

## 📄 License

This project is developed for shipping and freight management purposes.

---

**Note**: This is a shipping management system. Ensure proper database configuration and security measures are in place for production use.
