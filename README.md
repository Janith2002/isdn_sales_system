# 🛒 ISDN Online Sales & Delivery Management System

A full-featured **e-commerce and delivery management platform** with separate dashboards for admins, customers, and delivery drivers. Handles the full order lifecycle — from browsing to delivery.

## ✨ Features

- 🛍️ Customer shopping & order placement
- 📦 Order tracking with real-time status updates
- 🚗 Driver management — assignment, tracking, and status updates
- 🧾 Invoice generation
- 🔐 Role-based access: Admin / Customer / Driver
- 📂 Category & product management
- ✅ Order cancellation & reassignment

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP |
| Database | MySQL |
| Frontend | HTML, CSS, Bootstrap |

## 🚀 Getting Started

### Prerequisites
- PHP 8.x & Apache (XAMPP recommended)
- MySQL

### Installation

```bash
git clone https://github.com/Janith2002/isdn_sales_system.git

# Import the SQL file into MySQL
# Configure db connection in the config file
# Open in browser via localhost
```

## 📁 Key Structure

```
admin/
├── dashboard/      # Admin overview
├── orders/         # Order management
├── drivers/        # Driver assignment & tracking
├── categories/     # Product categories
customer/
├── shop, cart, checkout, orders
```

---

> Built by [Janith Akalanka](https://github.com/Janith2002)

## ⚙️ Local Setup

### Prerequisites
- PHP 8.x & Apache (XAMPP recommended)
- MySQL

### Steps

```bash
# 1. Clone the repo
git clone https://github.com/Janith2002/isdn_sales_system.git
cd isdn_sales_system

# 2. Create the database config file
cp app/config/db.example.php app/config/db.php
# Then edit app/config/db.php with your MySQL credentials

# 3. Create the database in MySQL
# Open phpMyAdmin or MySQL CLI and run:
# CREATE DATABASE isdn_db;

# 4. Import the database schema
# Import sql/database.sql via phpMyAdmin or:
# mysql -u root -p isdn_db < sql/database.sql

# 5. Start Apache & MySQL via XAMPP
# Visit: http://localhost/isdn_sales_system/public/
```

### Default Login
| Role | Email | Password |
|------|-------|----------|
| Admin | admin@isdn.com | admin123 |
| Customer | customer@isdn.com | customer123 |
