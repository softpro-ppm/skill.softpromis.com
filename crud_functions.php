<?php
require_once 'config.php';

// Training Partners CRUD
class TrainingPartner {
    public static function getAll($status = null) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("CALL sp_get_training_partners(?)");
        $stmt->execute([$status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT * FROM training_partners WHERE partner_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO training_partners (partner_name, contact_person, email, phone, address, website, status, created_at, updated_at, registration_doc, agreement_doc) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), ?, ?)");
        return $stmt->execute([
            $data['partner_name'],
            $data['contact_person'],
            $data['email'],
            $data['phone'],
            $data['address'],
            $data['website'],
            $data['status'],
            $data['registration_doc'] ?? null,
            $data['agreement_doc'] ?? null
        ]);
    }

    public static function update($id, $data) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("UPDATE training_partners 
                               SET partner_name = ?, contact_person = ?, email = ?, phone = ?, address = ?, website = ?, status = ?, updated_at = NOW(), registration_doc = ?, agreement_doc = ? 
                               WHERE partner_id = ?");
        return $stmt->execute([
            $data['partner_name'],
            $data['contact_person'],
            $data['email'],
            $data['phone'],
            $data['address'],
            $data['website'],
            $data['status'],
            $data['registration_doc'] ?? null,
            $data['agreement_doc'] ?? null,
            $id
        ]);
    }

    public static function delete($id) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("DELETE FROM training_partners WHERE partner_id = ?");
        return $stmt->execute([$id]);
    }
}

// Training Centers CRUD
class TrainingCenter {
    public static function getAll($partnerId = null) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("CALL sp_get_training_centers(?)");
        $stmt->execute([$partnerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT c.*, p.partner_name FROM training_centers c 
                               JOIN training_partners p ON c.partner_id = p.partner_id 
                               WHERE c.center_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO training_centers (partner_id, center_name, contact_person, email, phone, address, city, state, pincode, status, created_at, updated_at) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        return $stmt->execute([
            $data['partner_id'],
            $data['center_name'],
            $data['contact_person'],
            $data['email'],
            $data['phone'],
            $data['address'],
            $data['city'],
            $data['state'],
            $data['pincode'],
            $data['status']
        ]);
    }

    public static function update($id, $data) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("UPDATE training_centers 
                               SET partner_id = ?, center_name = ?, contact_person = ?, email = ?, phone = ?, 
                                   address = ?, city = ?, state = ?, pincode = ?, status = ?, updated_at = NOW() 
                               WHERE center_id = ?");
        return $stmt->execute([
            $data['partner_id'],
            $data['center_name'],
            $data['contact_person'],
            $data['email'],
            $data['phone'],
            $data['address'],
            $data['city'],
            $data['state'],
            $data['pincode'],
            $data['status'],
            $id
        ]);
    }

    public static function delete($id) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("DELETE FROM training_centers WHERE center_id = ?");
        return $stmt->execute([$id]);
    }
}

// Sectors CRUD
class Sector {
    public static function getAll() {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT * FROM sectors WHERE status = 'active' ORDER BY sector_id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT * FROM sectors WHERE sector_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO sectors (sector_name, description, status, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        return $stmt->execute([$data['sector_name'], $data['description'], $data['status']]);
    }

    public static function update($id, $data) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("UPDATE sectors SET sector_name = ?, description = ?, status = ?, updated_at = NOW() WHERE sector_id = ?");
        return $stmt->execute([$data['sector_name'], $data['description'], $data['status'], $id]);
    }

    public static function delete($id) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("DELETE FROM sectors WHERE sector_id = ?");
        return $stmt->execute([$id]);
    }
}

// Schemes CRUD
class Scheme {
    public static function getAll() {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT * FROM schemes WHERE status = 'active'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT * FROM schemes WHERE scheme_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO schemes (scheme_name, description, status, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        return $stmt->execute([$data['scheme_name'], $data['description'], $data['status']]);
    }

    public static function update($id, $data) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("UPDATE schemes SET scheme_name = ?, description = ?, status = ?, updated_at = NOW() WHERE scheme_id = ?");
        return $stmt->execute([$data['scheme_name'], $data['description'], $data['status'], $id]);
    }

    public static function delete($id) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("DELETE FROM schemes WHERE scheme_id = ?");
        return $stmt->execute([$id]);
    }
}

// Courses CRUD
class Course {
    public static function getAll($sectorId = null, $schemeId = null) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("CALL sp_get_courses(?, ?)");
        $stmt->execute([$sectorId, $schemeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT c.*, s.sector_name, sch.scheme_name 
                               FROM courses c
                               JOIN sectors s ON c.sector_id = s.sector_id
                               JOIN schemes sch ON c.scheme_id = sch.scheme_id
                               WHERE c.course_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO courses (sector_id, scheme_id, course_code, course_name, duration_hours, description, status, created_at, updated_at) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        return $stmt->execute([
            $data['sector_id'],
            $data['scheme_id'],
            $data['course_code'],
            $data['course_name'],
            $data['duration_hours'],
            $data['description'],
            $data['status']
        ]);
    }

    public static function update($id, $data) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("UPDATE courses 
                               SET sector_id = ?, scheme_id = ?, course_code = ?, course_name = ?, duration_hours = ?, description = ?, status = ?, updated_at = NOW() 
                               WHERE course_id = ?");
        return $stmt->execute([
            $data['sector_id'],
            $data['scheme_id'],
            $data['course_code'],
            $data['course_name'],
            $data['duration_hours'],
            $data['description'],
            $data['status'],
            $id
        ]);
    }

    public static function delete($id) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("DELETE FROM courses WHERE course_id = ?");
        return $stmt->execute([$id]);
    }
}

// Batches CRUD
class Batch {
    public static function getAll($centerId = null, $courseId = null, $status = null) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("CALL sp_get_batches(?, ?, ?)");
        $stmt->execute([$centerId, $courseId, $status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT b.*, c.course_name, tc.center_name 
                               FROM batches b
                               JOIN courses c ON b.course_id = c.course_id
                               JOIN training_centers tc ON b.center_id = tc.center_id
                               WHERE b.batch_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO batches (center_id, course_id, batch_code, start_date, end_date, capacity, status, created_at, updated_at) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        return $stmt->execute([
            $data['center_id'],
            $data['course_id'],
            $data['batch_code'],
            $data['start_date'],
            $data['end_date'],
            $data['capacity'],
            $data['status']
        ]);
    }

    public static function update($id, $data) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("UPDATE batches 
                               SET center_id = ?, course_id = ?, batch_code = ?, start_date = ?, end_date = ?, 
                                   capacity = ?, status = ?, updated_at = NOW() 
                               WHERE batch_id = ?");
        return $stmt->execute([
            $data['center_id'],
            $data['course_id'],
            $data['batch_code'],
            $data['start_date'],
            $data['end_date'],
            $data['capacity'],
            $data['status'],
            $id
        ]);
    }

    public static function delete($id) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("DELETE FROM batches WHERE batch_id = ?");
        return $stmt->execute([$id]);
    }
}

// Students CRUD
class Student {
    public static function getAll() {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT * FROM students");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO students (enrollment_no, first_name, last_name, email, mobile, date_of_birth, gender, address, created_at, updated_at) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        return $stmt->execute([
            $data['enrollment_no'],
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['mobile'],
            $data['date_of_birth'],
            $data['gender'],
            $data['address']
        ]);
    }

    public static function update($id, $data) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("UPDATE students 
                               SET first_name = ?, last_name = ?, email = ?, mobile = ?, 
                                   date_of_birth = ?, gender = ?, address = ?, updated_at = NOW() 
                               WHERE student_id = ?");
        return $stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['mobile'],
            $data['date_of_birth'],
            $data['gender'],
            $data['address'],
            $id
        ]);
    }

    public static function delete($id) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
        return $stmt->execute([$id]);
    }

    public static function enroll($data) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("CALL sp_enroll_student(?, ?, ?)");
        return $stmt->execute([
            $data['student_id'],
            $data['batch_id'],
            $data['enrollment_date']
        ]);
    }
}

// Assessments CRUD
class Assessment {
    public static function getAll($studentId = null) {
        $conn = getDBConnection();
        if ($studentId) {
            $stmt = $conn->prepare("CALL sp_get_student_assessments(?)");
            $stmt->execute([$studentId]);
        } else {
            $stmt = $conn->prepare("SELECT a.*, s.first_name, s.last_name, c.course_name, b.batch_code
                                   FROM assessments a
                                   JOIN student_batch_enrollment e ON a.enrollment_id = e.enrollment_id
                                   JOIN students s ON e.student_id = s.student_id
                                   JOIN batches b ON e.batch_id = b.batch_id
                                   JOIN courses c ON b.course_id = c.course_id");
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT a.*, s.first_name, s.last_name, c.course_name, b.batch_code
                               FROM assessments a
                               JOIN student_batch_enrollment e ON a.enrollment_id = e.enrollment_id
                               JOIN students s ON e.student_id = s.student_id
                               JOIN batches b ON e.batch_id = b.batch_id
                               JOIN courses c ON b.course_id = c.course_id
                               WHERE a.assessment_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("CALL sp_create_assessment(?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['enrollment_id'],
            $data['assessment_type'],
            $data['assessment_date'],
            $data['score'],
            $data['remarks']
        ]);
    }

    public static function update($id, $data) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("UPDATE assessments 
                               SET assessment_type = ?, assessment_date = ?, score = ?, 
                                   remarks = ?, status = ? 
                               WHERE assessment_id = ?");
        return $stmt->execute([
            $data['assessment_type'],
            $data['assessment_date'],
            $data['score'],
            $data['remarks'],
            $data['status'],
            $id
        ]);
    }

    public static function delete($id) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("DELETE FROM assessments WHERE assessment_id = ?");
        return $stmt->execute([$id]);
    }
}

// Fees CRUD
class Fee {
    public static function getAll($studentId = null) {
        $conn = getDBConnection();
        if ($studentId) {
            $stmt = $conn->prepare("CALL sp_get_student_fees(?)");
            $stmt->execute([$studentId]);
        } else {
            $stmt = $conn->prepare("SELECT f.*, s.first_name, s.last_name, c.course_name, b.batch_code
                                   FROM fees f
                                   JOIN student_batch_enrollment e ON f.enrollment_id = e.enrollment_id
                                   JOIN students s ON e.student_id = s.student_id
                                   JOIN batches b ON e.batch_id = b.batch_id
                                   JOIN courses c ON b.course_id = c.course_id");
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT f.*, s.first_name, s.last_name, c.course_name, b.batch_code
                               FROM fees f
                               JOIN student_batch_enrollment e ON f.enrollment_id = e.enrollment_id
                               JOIN students s ON e.student_id = s.student_id
                               JOIN batches b ON e.batch_id = b.batch_id
                               JOIN courses c ON b.course_id = c.course_id
                               WHERE f.fee_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("CALL sp_record_fee_payment(?, ?, ?, ?)");
        return $stmt->execute([
            $data['enrollment_id'],
            $data['amount'],
            $data['payment_mode'],
            $data['transaction_id']
        ]);
    }

    public static function update($id, $data) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("UPDATE fees 
                               SET amount = ?, payment_date = ?, payment_mode = ?, 
                                   transaction_id = ?, status = ? 
                               WHERE fee_id = ?");
        return $stmt->execute([
            $data['amount'],
            $data['payment_date'],
            $data['payment_mode'],
            $data['transaction_id'],
            $data['status'],
            $id
        ]);
    }

    public static function delete($id) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("DELETE FROM fees WHERE fee_id = ?");
        return $stmt->execute([$id]);
    }
}

// Certificates CRUD
class Certificate {
    public static function getAll() {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT c.*, s.first_name, s.last_name, co.course_name
                               FROM certificates c
                               JOIN student_batch_enrollment e ON c.enrollment_id = e.enrollment_id
                               JOIN students s ON e.student_id = s.student_id
                               JOIN batches b ON e.batch_id = b.batch_id
                               JOIN courses co ON b.course_id = co.course_id");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT c.*, s.first_name, s.last_name, co.course_name
                               FROM certificates c
                               JOIN student_batch_enrollment e ON c.enrollment_id = e.enrollment_id
                               JOIN students s ON e.student_id = s.student_id
                               JOIN batches b ON e.batch_id = b.batch_id
                               JOIN courses co ON b.course_id = co.course_id
                               WHERE c.certificate_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("CALL sp_issue_certificate(?, ?, ?)");
        return $stmt->execute([
            $data['enrollment_id'],
            $data['certificate_no'],
            $data['valid_years']
        ]);
    }

    public static function update($id, $data) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("UPDATE certificates 
                               SET certificate_no = ?, issue_date = ?, valid_until = ?, status = ? 
                               WHERE certificate_id = ?");
        return $stmt->execute([
            $data['certificate_no'],
            $data['issue_date'],
            $data['valid_until'],
            $data['status'],
            $id
        ]);
    }

    public static function delete($id) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("DELETE FROM certificates WHERE certificate_id = ?");
        return $stmt->execute([$id]);
    }
}

// Dashboard Statistics
class Dashboard {
    public static function getStats() {
        $conn = getDBConnection();
        $stmt = $conn->prepare("CALL sp_get_dashboard_stats()");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getBatchReport($batchId) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("CALL sp_generate_batch_report(?)");
        $stmt->execute([$batchId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Roles CRUD
class Role {
    public static function getAll() {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT r.role_id, r.role_name, r.description, r.created_at, r.permissions, COUNT(u.user_id) as user_count FROM roles r LEFT JOIN users u ON r.role_id = u.role_id GROUP BY r.role_id ORDER BY r.role_id ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($roleId) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT * FROM roles WHERE role_id = ?");
        $stmt->execute([$roleId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($roleName, $description, $permissions = []) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO roles (role_name, description, permissions) VALUES (?, ?, ?)");
        $stmt->execute([$roleName, $description, json_encode($permissions)]);
        return $conn->lastInsertId();
    }

    public static function update($roleId, $roleName, $description, $permissions = []) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("UPDATE roles SET role_name = ?, description = ?, permissions = ? WHERE role_id = ?");
        return $stmt->execute([$roleName, $description, json_encode($permissions), $roleId]);
    }

    public static function delete($roleId) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("DELETE FROM roles WHERE role_id = ?");
        return $stmt->execute([$roleId]);
    }
}
?>