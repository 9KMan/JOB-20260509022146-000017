-- GPS-Based Employee Attendance System Database Schema

CREATE DATABASE IF NOT EXISTS gps_attendance;
USE gps_attendance;

-- Employees
CREATE TABLE employees (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE,
  phone VARCHAR(20),
  designation VARCHAR(50),
  department VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Users (auth)
CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  employee_id INT,
  username VARCHAR(50) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','employee') DEFAULT 'employee',
  status ENUM('active','inactive') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE SET NULL
);

-- Office Sites
CREATE TABLE sites (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  address VARCHAR(255),
  latitude DECIMAL(10,8) NOT NULL,
  longitude DECIMAL(11,8) NOT NULL,
  radius_meters INT DEFAULT 100,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Attendance Records
CREATE TABLE attendance (
  id INT PRIMARY KEY AUTO_INCREMENT,
  employee_id INT,
  site_id INT,
  check_in_time DATETIME NOT NULL,
  check_out_time DATETIME,
  check_in_lat DECIMAL(10,8),
  check_in_lng DECIMAL(11,8),
  check_out_lat DECIMAL(10,8),
  check_out_lng DECIMAL(11,8),
  check_in_valid BOOLEAN DEFAULT FALSE,
  check_out_valid BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
  FOREIGN KEY (site_id) REFERENCES sites(id) ON DELETE SET NULL
);

-- Indexes for performance
CREATE INDEX idx_attendance_employee ON attendance(employee_id);
CREATE INDEX idx_attendance_date ON attendance(check_in_time);
CREATE INDEX idx_attendance_site ON attendance(site_id);
CREATE INDEX idx_users_username ON users(username);
