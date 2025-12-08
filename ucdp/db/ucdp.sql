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

-- Table: audit_logs 
CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    admin_id INT NULL,
    action VARCHAR(100) NOT NULL, -- e.g., 'upload_document', 'verify_doc'
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL
);

-- Table: feedback 
CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    email VARCHAR(100),
    message TEXT NOT NULL,
    status ENUM('pending', 'resolved') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Table: mock_govt_data 
CREATE TABLE mock_govt_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nid VARCHAR(20) NOT NULL,
    doc_type ENUM('NID', 'Passport', 'Driving_License') NOT NULL,
    fake_data JSON NOT NULL, 
    source ENUM('EC', 'PO', 'BRTA') NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_nid_type (nid, doc_type)
);

-- Indexes for performance
CREATE INDEX idx_users_nid ON users(nid);
CREATE INDEX idx_documents_user_status ON documents(user_id, status);
CREATE INDEX idx_shares_token ON shares(share_token);
CREATE INDEX idx_logs_action ON audit_logs(action);
CREATE INDEX idx_feedback_status ON feedback(status);




-- Sample Data Inserts (Fake data for testing)

-- Insert sample users
INSERT INTO users (nid, full_name, email, phone, password_hash, is_verified) VALUES
('12345678901234567', 'Abdullah Al Jiad', 'jiad@example.com', '+8801712345678', '$2y$10$hashedpassword1', 1),
('98765432109876543', 'MD. Swadhin Miah', 'swadhin@example.com', '+8801812345679', '$2y$10$hashedpassword2', 1);

-- Insert sample admins
INSERT INTO admins (username, password_hash, full_name, role) VALUES
('admin1', '$2y$10$hashedadmin1', 'Admin User', 'admin'),
('superadmin', '$2y$10$hashedsuper1', 'Super Admin', 'super_admin');

-- Insert sample documents
INSERT INTO documents (user_id, doc_type, file_path, doc_number, expiry_date, status) VALUES
(1, 'NID', 'uploads/nid_jiad.pdf', '12345678901234567', NULL, 'verified'),
(1, 'Passport', 'uploads/passport_jiad.pdf', 'AB1234567', '2030-12-31', 'pending'),
(2, 'Driving_License', 'uploads/license_swadhin.pdf', 'DL789012', '2028-06-15', 'verified');

-- Insert sample shares
INSERT INTO shares (doc_id, sharer_user_id, shared_with, share_token, expiry_time) VALUES
(1, 1, 'bank@example.com', 'share_token_abc123', DATE_ADD(NOW(), INTERVAL 24 HOUR));

-- Insert sample family relations
INSERT INTO family_relations (guardian_user_id, member_user_id, relation_type) VALUES
(1, 2, 'guardian');

-- Insert sample audit logs
INSERT INTO audit_logs (user_id, admin_id, action, description, ip_address) VALUES
(1, 1, 'upload_document', 'User uploaded NID', '127.0.0.1'),
(2, NULL, 'login', 'User logged in', '127.0.0.1');

-- Insert sample feedback
INSERT INTO feedback (user_id, full_name, phone, email, message) VALUES
(1, 'MD. Abdullah Al Jiad', '+8801712345678', 'jiad@example.com', 'Great portal, needs mobile app!');

-- Insert sample mock govt data
INSERT INTO mock_govt_data (nid, doc_type, fake_data, source) VALUES
('12345678901234567', 'NID', '{"name": "MD. Abdullah Al Jiad", "dob": "1990-01-01", "address": "Dhaka"}', 'EC'),
('12345678901234567', 'Passport', '{"number": "AB1234567", "issue_date": "2020-01-01", "expiry": "2030-12-31"}', 'PO');

-- Step 1: Add new doc_type to ENUM (Police_Clearance, Car_Registration - Vehicle_Registration already there)
ALTER TABLE documents MODIFY COLUMN doc_type ENUM('NID', 'Passport', 'Driving_License', 'Birth_Certificate', 'Education_Certificate', 'Vehicle_Registration', 'Police_Clearance') NOT NULL;

-- Step 2: Extend mock_govt_data ENUM for new types
ALTER TABLE mock_govt_data MODIFY COLUMN doc_type ENUM('NID', 'Passport', 'Driving_License', 'Birth_Certificate', 'Education_Certificate', 'Vehicle_Registration', 'Police_Clearance') NOT NULL;

-- Step 3: Add sample mock data for new types (for NID=12345678901234567)
INSERT INTO mock_govt_data (nid, doc_type, fake_data, source) VALUES
('12345678901234567', 'Birth_Certificate', '{"number": "BC123456", "dob": "1990-01-01", "place": "Dhaka"}', 'Registrar'),
('12345678901234567', 'Education_Certificate', '{"degree": "BSc CSE", "board": "Dhaka", "year": "2015"}', 'Education Board'),
('12345678901234567', 'Police_Clearance', '{"certificate_id": "PC789012", "issue_date": "2025-01-01", "valid_till": "2026-01-01"}', 'Police'),
('12345678901234567', 'Vehicle_Registration', '{"reg_no": "DHAKA-METRO-GA-123456", "type": "Car", "issue_date": "2020-01-01"}', 'BRTA');  -- Car/Bike example

-- Step 4: Verify sample users have docs (run if needed)
INSERT INTO documents (user_id, doc_type, file_path, status) VALUES
(1, 'Birth_Certificate', 'uploads/birth_cert_sample.pdf', 'verified'),  -- Auto-linked simulation
(1, 'Education_Certificate', 'uploads/edu_cert_sample.pdf', 'verified');