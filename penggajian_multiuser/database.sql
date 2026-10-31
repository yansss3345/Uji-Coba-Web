CREATE DATABASE IF NOT EXISTS db_penggajian CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_penggajian;
CREATE TABLE IF NOT EXISTS users (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 username VARCHAR(50) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 role ENUM('admin','hrd') NOT NULL DEFAULT 'hrd',
 is_active TINYINT(1) NOT NULL DEFAULT 1,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS employees (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 status ENUM('Tetap','Training') NOT NULL DEFAULT 'Training',
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
INSERT INTO employees(name,status) SELECT 'Fian','Tetap' WHERE NOT EXISTS(SELECT 1 FROM employees);
INSERT INTO employees(name,status) SELECT 'Ayan','Training' WHERE (SELECT COUNT(*) FROM employees)=1;
INSERT INTO employees(name,status) SELECT 'Ade','Tetap' WHERE (SELECT COUNT(*) FROM employees)=2;
INSERT INTO employees(name,status) SELECT 'Gilang','Training' WHERE (SELECT COUNT(*) FROM employees)=3;
INSERT INTO employees(name,status) SELECT 'Roni','Tetap' WHERE (SELECT COUNT(*) FROM employees)=4;
