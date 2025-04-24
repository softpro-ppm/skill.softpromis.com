-- Create Database
CREATE DATABASE IF NOT EXISTS softpro_skill;
USE softpro_skill;

-- Users and Roles Tables
CREATE TABLE roles (
    role_id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    role_id INT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    full_name VARCHAR(100),
    mobile VARCHAR(15),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(role_id)
);

-- Training Partners and Centers
CREATE TABLE training_partners (
    partner_id INT PRIMARY KEY AUTO_INCREMENT,
    partner_name VARCHAR(100) NOT NULL,
    contact_person VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(15),
    address TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE training_centers (
    center_id INT PRIMARY KEY AUTO_INCREMENT,
    partner_id INT,
    center_name VARCHAR(100) NOT NULL,
    contact_person VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(15),
    address TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (partner_id) REFERENCES training_partners(partner_id)
);

-- Courses and Training Programs
CREATE TABLE sectors (
    sector_id INT PRIMARY KEY AUTO_INCREMENT,
    sector_name VARCHAR(100) NOT NULL,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE schemes (
    scheme_id INT PRIMARY KEY AUTO_INCREMENT,
    scheme_name VARCHAR(100) NOT NULL,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE courses (
    course_id INT PRIMARY KEY AUTO_INCREMENT,
    sector_id INT,
    scheme_id INT,
    course_code VARCHAR(20) NOT NULL UNIQUE,
    course_name VARCHAR(100) NOT NULL,
    duration_hours INT,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sector_id) REFERENCES sectors(sector_id),
    FOREIGN KEY (scheme_id) REFERENCES schemes(scheme_id)
);

CREATE TABLE batches (
    batch_id INT PRIMARY KEY AUTO_INCREMENT,
    center_id INT,
    course_id INT,
    batch_code VARCHAR(20) NOT NULL UNIQUE,
    start_date DATE,
    end_date DATE,
    capacity INT,
    status ENUM('upcoming', 'ongoing', 'completed', 'cancelled') DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (center_id) REFERENCES training_centers(center_id),
    FOREIGN KEY (course_id) REFERENCES courses(course_id)
);

-- Students and Assessments
CREATE TABLE students (
    student_id INT PRIMARY KEY AUTO_INCREMENT,
    enrollment_no VARCHAR(20) NOT NULL UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100),
    mobile VARCHAR(15),
    date_of_birth DATE,
    gender ENUM('male', 'female', 'other'),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE student_batch_enrollment (
    enrollment_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    batch_id INT,
    enrollment_date DATE,
    status ENUM('active', 'completed', 'dropped') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id),
    FOREIGN KEY (batch_id) REFERENCES batches(batch_id)
);

CREATE TABLE fees (
    fee_id INT PRIMARY KEY AUTO_INCREMENT,
    enrollment_id INT,
    amount DECIMAL(10,2),
    payment_date DATE,
    payment_mode VARCHAR(50),
    transaction_id VARCHAR(100),
    status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (enrollment_id) REFERENCES student_batch_enrollment(enrollment_id)
);

CREATE TABLE assessments (
    assessment_id INT PRIMARY KEY AUTO_INCREMENT,
    enrollment_id INT,
    assessment_type ENUM('theory', 'practical', 'project'),
    assessment_date DATE,
    score DECIMAL(5,2),
    max_score DECIMAL(5,2) DEFAULT 100.00,
    remarks TEXT,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (enrollment_id) REFERENCES student_batch_enrollment(enrollment_id)
);

CREATE TABLE certificates (
    certificate_id INT PRIMARY KEY AUTO_INCREMENT,
    enrollment_id INT,
    certificate_no VARCHAR(50) UNIQUE,
    issue_date DATE,
    valid_until DATE,
    status ENUM('issued', 'revoked') DEFAULT 'issued',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (enrollment_id) REFERENCES student_batch_enrollment(enrollment_id)
);

-- Insert Demo Data

-- Roles
INSERT INTO roles (role_name, description) VALUES
('admin', 'System Administrator'),
('trainer', 'Training Staff'),
('assessor', 'Assessment Staff'),
('student', 'Student User');

-- Users
INSERT INTO users (role_id, username, password, email, full_name, mobile) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@softpro.com', 'Admin User', '9876543210'),
(2, 'trainer1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'trainer@softpro.com', 'John Trainer', '9876543211'),
(3, 'assessor1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'assessor@softpro.com', 'Jane Assessor', '9876543212');

-- Training Partners
INSERT INTO training_partners (partner_name, contact_person, email, phone, address) VALUES
('TechSkill Solutions', 'Rajesh Kumar', 'contact@techskill.com', '9876543213', 'Mumbai, India'),
('Digital Training Institute', 'Priya Singh', 'info@dti.com', '9876543214', 'Delhi, India');

-- Training Centers
INSERT INTO training_centers (partner_id, center_name, contact_person, email, phone, address) VALUES
(1, 'TechSkill Mumbai Center', 'Amit Shah', 'mumbai@techskill.com', '9876543215', 'Andheri, Mumbai'),
(1, 'TechSkill Pune Center', 'Sneha Patil', 'pune@techskill.com', '9876543216', 'Hinjewadi, Pune'),
(2, 'DTI Delhi Center', 'Vikram Singh', 'delhi@dti.com', '9876543217', 'Connaught Place, Delhi');

-- Sectors
INSERT INTO sectors (sector_name, description) VALUES
('IT-ITeS', 'Information Technology and IT Enabled Services'),
('Healthcare', 'Healthcare and Medical Services'),
('Retail', 'Retail and Sales Management');

-- Schemes
INSERT INTO schemes (scheme_name, description) VALUES
('PMKVY', 'Pradhan Mantri Kaushal Vikas Yojana'),
('DDU-GKY', 'Deen Dayal Upadhyaya Grameen Kaushalya Yojana'),
('Regular', 'Regular Training Programs');

-- Courses
INSERT INTO courses (sector_id, scheme_id, course_code, course_name, duration_hours) VALUES
(1, 1, 'WD001', 'Web Development', 480),
(1, 1, 'DM001', 'Digital Marketing', 240),
(1, 3, 'DA001', 'Data Analytics', 360);

-- Batches
INSERT INTO batches (center_id, course_id, batch_code, start_date, end_date, capacity, status) VALUES
(1, 1, 'B001', '2024-01-01', '2024-06-30', 30, 'ongoing'),
(2, 2, 'B002', '2024-02-01', '2024-04-30', 25, 'ongoing'),
(3, 3, 'B003', '2024-03-01', '2024-08-31', 20, 'upcoming');

-- Students
INSERT INTO students (enrollment_no, first_name, last_name, email, mobile, date_of_birth, gender) VALUES
('ENR001', 'Rahul', 'Sharma', 'rahul@gmail.com', '9876543218', '2000-01-15', 'male'),
('ENR002', 'Priya', 'Patel', 'priya@gmail.com', '9876543219', '2001-03-20', 'female'),
('ENR003', 'Amit', 'Kumar', 'amit@gmail.com', '9876543220', '1999-07-10', 'male');

-- Enrollments
INSERT INTO student_batch_enrollment (student_id, batch_id, enrollment_date, status) VALUES
(1, 1, '2024-01-01', 'active'),
(2, 1, '2024-01-01', 'active'),
(3, 2, '2024-02-01', 'active');

-- Fees
INSERT INTO fees (enrollment_id, amount, payment_date, payment_mode, transaction_id, status) VALUES
(1, 25000.00, '2024-01-01', 'online', 'TXN123456', 'paid'),
(2, 25000.00, '2024-01-01', 'online', 'TXN123457', 'paid'),
(3, 15000.00, '2024-02-01', 'online', 'TXN123458', 'paid');

-- Assessments
INSERT INTO assessments (enrollment_id, assessment_type, assessment_date, score, remarks, status) VALUES
(1, 'theory', '2024-02-15', 85.00, 'Good performance', 'completed'),
(1, 'practical', '2024-02-20', 90.00, 'Excellent practical skills', 'completed'),
(2, 'theory', '2024-02-15', 75.00, 'Need improvement in theory', 'completed');

-- Certificates
INSERT INTO certificates (enrollment_id, certificate_no, issue_date, valid_until) VALUES
(1, 'CERT001', '2024-06-30', '2026-06-30'),
(2, 'CERT002', '2024-06-30', '2026-06-30'); 