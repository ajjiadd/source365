-- Create Database 
CREATE DATABASE IF NOT EXISTS ucdp;
USE ucdp;

-- Table: users (Citizen users)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nid VARCHAR(20) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(15) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    otp_code VARCHAR(6),
    is_verified TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: admins (Admin users)
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'super_admin') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: documents (User's government documents)
CREATE TABLE documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    doc_type ENUM('NID', 'Passport', 'Driving_License', 'Birth_Certificate', 'Education_Certificate', 'Vehicle_Registration') NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    doc_number VARCHAR(50),
    expiry_date DATE,
    status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    verified_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table: shares 
CREATE TABLE shares (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doc_id INT NOT NULL,
    sharer_user_id INT NOT NULL,
    shared_with VARCHAR(100) NOT NULL, 
    share_token VARCHAR(255) UNIQUE NOT NULL,
    expiry_time TIMESTAMP NOT NULL,
    is_accessed TINYINT(1) DEFAULT 0,
    accessed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doc_id) REFERENCES documents(id) ON DELETE CASCADE,
    FOREIGN KEY (sharer_user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table: family_relations 
CREATE TABLE family_relations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guardian_user_id INT NOT NULL,
    member_user_id INT NOT NULL,
    relation_type VARCHAR(50) NOT NULL, 
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (guardian_user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (member_user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_relation (guardian_user_id, member_user_id)
);