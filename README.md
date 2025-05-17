# MediTrack Website 

A web-based Medicine Inventory Management System built with PHP, MySQL, HTML/CSS, and JavaScript. It enables pharmacies to manage users, medicines, customers, and sales with secure authentication and role-based access control (Admin & Cashier).

---

## User Roles

- Admin
  - Add/view/delete users
  - Add medicines
  - Access logs and attempts
- Cashier
  - Sell medicine
  - Register customers

---

## Features

- Login System with bcrypt-hashed passwords
- Sell Medicines with customer details and receipt
- Manage medicine stock and expiry
- Role-based user management (Admin & Cashier)
- Logs access & login attempts
- Optional profile pictures
- View sales history
- SQL database structure included

---

## Setup Instructions

### 1. Import Database
- Open **phpMyAdmin**
- Create a new database named `medicine_inventory`
- Import the file:  
  `database/medicine_inventory.sql`

### 2. Run the Project
- Place files in your local server directory (e.g., `htdocs` if using XAMPP)
- Start Apache & MySQL from XAMPP
- Visit:  
  `http://localhost/MediTrack-Website/login.php`

---

## Default Admin Login

| Username | Password    | Role   |
|----------|-------------|--------|
| Admin1   | Welcome123 | admin |
| Cashier1   | Welcome123 | Cashier |

> You can find credentials in the `admin` table inside the SQL dump.

---

## Security Measures

- ✅ Brute Force Protection using login attempt limits
- ✅ SQL Injection prevention using prepared statements
- ✅ XSS Protection via input/output sanitization
- ✅ Session Hijacking resistance using secure PHP sessions
- ✅ Role-based Access Control
- ✅ Passwords stored using `bcrypt` hashing

---

## License

This project is open-source and free to use for educational or commercial purposes under the [MIT License](https://opensource.org/licenses/MIT).

---

## Credits

Developed by Uswa Ahmad Khan  
University of Engineering and Technology  






