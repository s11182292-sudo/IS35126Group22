SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS tourism_booking;
USE tourism_booking;

-- --------------------------------------------------------
-- USERS TABLE
-- --------------------------------------------------------

CREATE TABLE users (
  id INT(11) NOT NULL AUTO_INCREMENT,
  fullname VARCHAR(100) DEFAULT NULL,
  email VARCHAR(100) DEFAULT NULL,
  password VARCHAR(255) DEFAULT NULL,
  role ENUM('admin','agent','customer') DEFAULT 'customer',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  failed_attempts INT(11) DEFAULT 0,
  lock_until DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- TRAVEL PACKAGES
-- --------------------------------------------------------

CREATE TABLE travel_packages (
  id INT(11) NOT NULL AUTO_INCREMENT,
  package_name VARCHAR(100) DEFAULT NULL,
  description TEXT DEFAULT NULL,
  destination VARCHAR(100) DEFAULT NULL,
  price DECIMAL(10,2) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- BOOKINGS
-- --------------------------------------------------------

CREATE TABLE bookings (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) DEFAULT NULL,
  package_id INT(11) DEFAULT NULL,
  travel_date DATE DEFAULT NULL,
  persons INT(11) DEFAULT NULL,
  status ENUM('Pending','Approved','Rejected') DEFAULT 'Pending',
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- OTP VERIFICATIONS
-- --------------------------------------------------------

CREATE TABLE otp_verifications (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) NOT NULL,
  otp_code VARCHAR(10) NOT NULL,
  expires_at DATETIME NOT NULL,
  verified TINYINT(1) DEFAULT 0,
  PRIMARY KEY (id),
  KEY user_id (user_id),
  CONSTRAINT otp_verifications_ibfk_1
    FOREIGN KEY (user_id) REFERENCES users (id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- AUDIT LOGS
-- --------------------------------------------------------

CREATE TABLE audit_logs (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) DEFAULT NULL,
  action VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- SAMPLE DATA (OPTIONAL)
-- --------------------------------------------------------

INSERT INTO users (fullname, email, password, role) VALUES
('Maria Yalayalatabua', 'm.yalayalatabua@gmail.com', '$2y$10$N8icZcBudcl73JEqEkI30.6VatIE78UYYJmprUPsOoZbF6WeR2.YK', 'admin'),
('John Smith', 'john@gmail.com', '$2y$10$ZXCfMzqTgFDVTEdPVMvAtOjnMuyJ0sMg5619DzpvbmBeMYlRiw2a6', 'customer'),
('Margaret Vodo', 'margie@gmail.com', '$2y$10$y.FrJzwj5Ix6eVRfqPCuDuPy2hIuDDmb3Y1t8jQTiTrZM7VMmWlVa', 'admin'),
('Salaseini Waqa', 'sala@gmail.com', '$2y$10$xExldkL3J.Ug0Hu7k2AAYuEH3.fne.jNo.hbyTN05wmZPHwjL/dqe', 'customer');

INSERT INTO travel_packages (package_name, description, destination, price) VALUES
('Sunset Island Escape',
 'Enjoy a 3-day luxury island getaway in the beautiful Mamanuca Islands. This package includes beachfront accommodation, daily breakfast, snorkeling tours, sunset cruise, and guided island exploration.',
 'Mamanuca Islands, Fiji',
 899.00);

COMMIT;
