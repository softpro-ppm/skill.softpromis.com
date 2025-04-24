<?php
require_once '../../config/config.php';
include '../../components/auth_check.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: Add form validation and database insertion
    $center_name = $_POST['center_name'] ?? '';
    $partner_id = $_POST['partner_id'] ?? '';
    $location = $_POST['location'] ?? '';
    $address = $_POST['address'] ?? '';
    $contact_person = $_POST['contact_person'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $capacity = $_POST['capacity'] ?? '';
    $status = $_POST['status'] ?? 'active';
    
    // Simulate successful addition
    header('Location: list.php?success=1');
    exit;
}

// TODO: Fetch partners list from database
$partners = [
    ['id' => 'TP001', 'name' => 'Tech Solutions Inc.'],
    ['id' => 'TP002', 'name' => 'Global Education'],
    ['id' => 'TP003', 'name' => 'Govt Training Dept']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Training Center - Softpro Skill Solutions</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include '../../components/topbar.php'; ?>
    <?php include '../../components/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="page-header">
                <h1>Add Training Center</h1>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="window.location.href='list.php'">
                        <i class="fas fa-arrow-left"></i>
                        Back to List
                    </button>
                </div>
            </div>

            <!-- Add Center Form -->
            <div class="form-container">
                <form method="POST" action="add.php" class="form">
                    <div class="form-group">
                        <label for="center_name">Center Name</label>
                        <input type="text" id="center_name" name="center_name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="partner_id">Training Partner</label>
                        <select id="partner_id" name="partner_id" class="form-control" required>
                            <option value="">Select Partner</option>
                            <?php foreach ($partners as $partner): ?>
                            <option value="<?php echo $partner['id']; ?>"><?php echo $partner['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="contact_person">Contact Person</label>
                        <input type="text" id="contact_person" name="contact_person" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="capacity">Student Capacity</label>
                        <input type="number" id="capacity" name="capacity" class="form-control" min="1" required>
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Save Center
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-redo"></i>
                            Reset Form
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="<?php echo $base_url; ?>assets/js/main.js"></script>
</body>
</html> 