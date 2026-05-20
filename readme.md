# Bengkel Broom Garage

A comprehensive web-based workshop (bengkel) management system built with PHP and MySQL. It features role-based access control to streamline workshop operations, including managing master data (services and spare parts), handling customer vehicles, tracking service requests, and generating reports.

## 🌟 App Features

The application is divided into three main roles with specific access levels:

### 1. Owner (Administrator)
- **Full Access Control:** Can manage all aspects of the application.
- **User Management:** Create, edit, and delete user accounts (Owner, Service Advisor, Mechanic).
- **Master Data:** Manage Services (Jasa) and Spare Parts inventory.
- **Operational:** Full access to manage Customers, Vehicles, and Service Requests.
- **Reporting:** Generate monthly reports and monitor overall workshop progress.

### 2. Service Advisor (SA)
- **Operational Focus:** Acts as the front desk for receiving vehicles.
- **Customer & Vehicle Management:** Input and manage data for customers and their vehicles.
- **Service Requests:** Register new service requests, select services/spare parts, and assign mechanics to tasks.
- **Billing:** Print PDF invoices (nota) for completed services.
- **Reporting:** Generate and view monthly reports.

### 3. Mechanic
- **Execution Focus:** Dedicated dashboard for assigned tasks.
- **Task Management:** View assigned service requests and update work statuses (`Assigned`, `Pending`, `Done`).

---

## 🚀 Installation & Setup

### Prerequisites
- Local server environment such as **XAMPP**, **WAMP**, or **MAMP**
- PHP (7.4 or newer recommended)
- MySQL Database

### Step-by-Step Guide

1. **Clone or Move the Project:**
   Place the project folder into your web server's local directory:
   - For XAMPP: `C:\xampp\htdocs\bengkel-main`
   - For MAMP/WAMP: Place it in the respective `www` or `htdocs` folder.

2. **Database Setup:**
   - Start **Apache** and **MySQL** from your XAMPP/WAMP control panel.
   - Open phpMyAdmin in your browser (`http://localhost/phpmyadmin`).
   - Create a new database named **`db_bengkel_bengawan`**.
   - Import the provided database file: **`db_bengkel_bengawan_revisi.sql`** into the newly created database.

3. **Database Configuration:**
   Verify the connection settings in `config/config.php` to match your local setup:
   ```php
   $servername = "localhost";
   $username = "root"; // Your MySQL username
   $password = "";     // Your MySQL password (usually blank for local)
   $dbname = "db_bengkel_bengawan";
   ```

4. **Run the Application:**
   Open your browser and navigate to:
   ```text
   http://localhost/bengkel-main/
   ```
   You will automatically be redirected to the login page.

---

## 📁 Directory Structure

```text
bengkel-main/
│
├── assets/             # Frontend assets (CSS, Images, Javascript)
├── auth/               # Authentication module (Login, Logout, Role Validation)
├── config/             # Database connection configuration
├── includes/           # Reusable UI components (Sidebar, Navbar, Footer)
├── mechanic/           # Mechanic module and pages
├── owner/              # Owner module and pages
├── service_advisor/    # Service Advisor module and pages
├── db_bengkel_bengawan_revisi.sql # Database dump for setup
└── index.php           # Main entry point (redirects to login)
```

---

## 📈 Development Progress

### ✅ Completed Features
- **Authentication:** Login UI, MD5 password hashing, role-based session protection, logout.
- **UI & Layout:** Dynamic sidebar, flexbox footer, pure CSS dropdown menus, responsive dashboard styling.
- **User Management:** Full CRUD for user accounts with dynamic forms for mechanic specializations.
- **Database Schema:** Updated schema with proper foreign keys and `ON DELETE CASCADE`.
- **Customer & Vehicle Module:** Multi-table transactions for simultaneously saving customer and vehicle data.
- **Service Requests:** Dynamic form with real-time vehicle filtering (JS) and multi-table inserts.
- **Spare Parts & Inventory:** Multi-sparepart support per service, automatic stock deduction, and rollback when edits occur.
- **Billing & Invoice:** Checkout page with auto-calculation, payment history, and PDF printing.

### ⏳ Upcoming Features
- [ ] Mechanic dashboard updates to allow marking status from `Assigned` to `Done` effectively.
