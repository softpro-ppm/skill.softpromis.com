-- User Management Functions
DELIMITER //

CREATE PROCEDURE sp_authenticate_user(
    IN p_username VARCHAR(50),
    IN p_password VARCHAR(255)
)
BEGIN
    SELECT u.*, r.role_name 
    FROM users u 
    JOIN roles r ON u.role_id = r.role_id 
    WHERE u.username = p_username 
    AND u.password = p_password 
    AND u.status = 'active';
END //

CREATE PROCEDURE sp_create_user(
    IN p_role_id INT,
    IN p_username VARCHAR(50),
    IN p_password VARCHAR(255),
    IN p_email VARCHAR(100),
    IN p_full_name VARCHAR(100),
    IN p_mobile VARCHAR(15)
)
BEGIN
    INSERT INTO users (role_id, username, password, email, full_name, mobile)
    VALUES (p_role_id, p_username, p_password, p_email, p_full_name, p_mobile);
END //

-- Training Partner Functions
CREATE PROCEDURE sp_get_training_partners(
    IN p_status VARCHAR(20)
)
BEGIN
    SELECT * FROM training_partners 
    WHERE status = COALESCE(p_status, status);
END //

CREATE PROCEDURE sp_get_training_centers(
    IN p_partner_id INT
)
BEGIN
    SELECT c.*, p.partner_name 
    FROM training_centers c
    JOIN training_partners p ON c.partner_id = p.partner_id
    WHERE c.partner_id = COALESCE(p_partner_id, c.partner_id);
END //

-- Course Management Functions
CREATE PROCEDURE sp_get_courses(
    IN p_sector_id INT,
    IN p_scheme_id INT
)
BEGIN
    SELECT c.*, s.sector_name, sch.scheme_name 
    FROM courses c
    JOIN sectors s ON c.sector_id = s.sector_id
    JOIN schemes sch ON c.scheme_id = sch.scheme_id
    WHERE c.sector_id = COALESCE(p_sector_id, c.sector_id)
    AND c.scheme_id = COALESCE(p_scheme_id, c.scheme_id);
END //

CREATE PROCEDURE sp_get_batches(
    IN p_center_id INT,
    IN p_course_id INT,
    IN p_status VARCHAR(20)
)
BEGIN
    SELECT b.*, c.course_name, tc.center_name 
    FROM batches b
    JOIN courses c ON b.course_id = c.course_id
    JOIN training_centers tc ON b.center_id = tc.center_id
    WHERE b.center_id = COALESCE(p_center_id, b.center_id)
    AND b.course_id = COALESCE(p_course_id, b.course_id)
    AND b.status = COALESCE(p_status, b.status);
END //

-- Student Management Functions
CREATE PROCEDURE sp_create_student(
    IN p_enrollment_no VARCHAR(20),
    IN p_first_name VARCHAR(50),
    IN p_last_name VARCHAR(50),
    IN p_email VARCHAR(100),
    IN p_mobile VARCHAR(15),
    IN p_date_of_birth DATE,
    IN p_gender VARCHAR(10),
    IN p_address TEXT
)
BEGIN
    INSERT INTO students (enrollment_no, first_name, last_name, email, mobile, date_of_birth, gender, address)
    VALUES (p_enrollment_no, p_first_name, p_last_name, p_email, p_mobile, p_date_of_birth, p_gender, p_address);
END //

CREATE PROCEDURE sp_enroll_student(
    IN p_student_id INT,
    IN p_batch_id INT,
    IN p_enrollment_date DATE
)
BEGIN
    INSERT INTO student_batch_enrollment (student_id, batch_id, enrollment_date)
    VALUES (p_student_id, p_batch_id, p_enrollment_date);
END //

-- Assessment Functions
CREATE PROCEDURE sp_create_assessment(
    IN p_enrollment_id INT,
    IN p_assessment_type VARCHAR(20),
    IN p_assessment_date DATE,
    IN p_score DECIMAL(5,2),
    IN p_remarks TEXT
)
BEGIN
    INSERT INTO assessments (enrollment_id, assessment_type, assessment_date, score, remarks)
    VALUES (p_enrollment_id, p_assessment_type, p_assessment_date, p_score, p_remarks);
END //

CREATE PROCEDURE sp_get_student_assessments(
    IN p_student_id INT
)
BEGIN
    SELECT a.*, s.first_name, s.last_name, c.course_name, b.batch_code
    FROM assessments a
    JOIN student_batch_enrollment e ON a.enrollment_id = e.enrollment_id
    JOIN students s ON e.student_id = s.student_id
    JOIN batches b ON e.batch_id = b.batch_id
    JOIN courses c ON b.course_id = c.course_id
    WHERE e.student_id = p_student_id;
END //

-- Fee Management Functions
CREATE PROCEDURE sp_record_fee_payment(
    IN p_enrollment_id INT,
    IN p_amount DECIMAL(10,2),
    IN p_payment_mode VARCHAR(50),
    IN p_transaction_id VARCHAR(100)
)
BEGIN
    INSERT INTO fees (enrollment_id, amount, payment_date, payment_mode, transaction_id, status)
    VALUES (p_enrollment_id, p_amount, CURDATE(), p_payment_mode, p_transaction_id, 'paid');
END //

CREATE PROCEDURE sp_get_student_fees(
    IN p_student_id INT
)
BEGIN
    SELECT f.*, s.first_name, s.last_name, c.course_name, b.batch_code
    FROM fees f
    JOIN student_batch_enrollment e ON f.enrollment_id = e.enrollment_id
    JOIN students s ON e.student_id = s.student_id
    JOIN batches b ON e.batch_id = b.batch_id
    JOIN courses c ON b.course_id = c.course_id
    WHERE e.student_id = p_student_id;
END //

-- Certificate Functions
CREATE PROCEDURE sp_issue_certificate(
    IN p_enrollment_id INT,
    IN p_certificate_no VARCHAR(50),
    IN p_valid_years INT
)
BEGIN
    INSERT INTO certificates (enrollment_id, certificate_no, issue_date, valid_until)
    VALUES (p_enrollment_id, p_certificate_no, CURDATE(), DATE_ADD(CURDATE(), INTERVAL p_valid_years YEAR));
END //

-- Dashboard Statistics Functions
CREATE PROCEDURE sp_get_dashboard_stats()
BEGIN
    SELECT 
        (SELECT COUNT(*) FROM students) as total_students,
        (SELECT COUNT(*) FROM batches WHERE status = 'ongoing') as active_batches,
        (SELECT COUNT(*) FROM assessments WHERE status = 'completed') as completed_assessments,
        (SELECT COUNT(*) FROM certificates WHERE status = 'issued') as issued_certificates;
END //

-- Report Generation Functions
CREATE PROCEDURE sp_generate_batch_report(
    IN p_batch_id INT
)
BEGIN
    SELECT 
        b.batch_code,
        c.course_name,
        tc.center_name,
        s.enrollment_no,
        s.first_name,
        s.last_name,
        a.assessment_type,
        a.score,
        a.status as assessment_status,
        f.amount as fee_paid,
        f.status as payment_status
    FROM batches b
    JOIN student_batch_enrollment e ON b.batch_id = e.batch_id
    JOIN students s ON e.student_id = s.student_id
    JOIN courses c ON b.course_id = c.course_id
    JOIN training_centers tc ON b.center_id = tc.center_id
    LEFT JOIN assessments a ON e.enrollment_id = a.enrollment_id
    LEFT JOIN fees f ON e.enrollment_id = f.enrollment_id
    WHERE b.batch_id = p_batch_id;
END //

DELIMITER ; 