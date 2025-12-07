-- Blood Donor Management System Database
-- Create database
CREATE DATABASE IF NOT EXISTS blood_donor_db;
USE blood_donor_db;

-- Donors Table
CREATE TABLE IF NOT EXISTS donors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    blood_group VARCHAR(5) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    email VARCHAR(100),
    city VARCHAR(50) NOT NULL,
    age INT NOT NULL,
    address TEXT,
    available ENUM('Yes', 'No') DEFAULT 'Yes',
    last_donation_date DATE,
    registered_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(blood_group),
    INDEX(city)
);

-- Blood Requests Table
CREATE TABLE IF NOT EXISTS blood_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_name VARCHAR(100) NOT NULL,
    blood_group VARCHAR(5) NOT NULL,
    units_needed INT NOT NULL,
    hospital_name VARCHAR(200) NOT NULL,
    city VARCHAR(50) NOT NULL,
    contact_person VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    urgency ENUM('Critical', 'Urgent', 'Normal') DEFAULT 'Normal',
    status ENUM('Pending', 'Fulfilled', 'Cancelled') DEFAULT 'Pending',
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    INDEX(blood_group),
    INDEX(status)
);

-- Organ Donation Table
CREATE TABLE IF NOT EXISTS organs_donation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_name VARCHAR(100) NOT NULL,
    donor_phone VARCHAR(15) NOT NULL,
    donor_email VARCHAR(100),
    donor_age INT NOT NULL,
    donor_blood_group VARCHAR(5) NOT NULL,
    donor_city VARCHAR(50) NOT NULL,
    organ_type VARCHAR(100) NOT NULL,
    donation_status VARCHAR(20) DEFAULT 'Pending',
    medical_eligibility VARCHAR(50),
    next_of_kin_consent VARCHAR(50),
    organ_availability VARCHAR(20) DEFAULT 'Available',
    notes TEXT,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(donor_blood_group),
    INDEX(organ_type)
);

-- Admin Users Table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
-- Note: In production, generate password using: password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO admin_users (username, password, email) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@blooddonor.com')
ON DUPLICATE KEY UPDATE username=username;
```

---

## **✅ YOUR GITHUB IS NOW COMPLETE!**

**Your Repository:** https://github.com/pandi-006/BLOOD-DONOR-MANAGEMENT-SYSTEM-

---

## **📋 FOR TCS APPLICATION, USE:**
```
Project: Blood Donor Management System
GitHub: https://github.com/pandi-006/BLOOD-DONOR-MANAGEMENT-SYSTEM-
Live Demo: http://blooddonor2025.rf.gd
Tech Stack: PHP, MySQL, HTML, CSS, JavaScript

Features:
✅ Emergency Alert System
✅ Organ Donation Module  
✅ Blood Request Management
✅ Admin Dashboard
✅ Donation History
✅ Search & Filtering
