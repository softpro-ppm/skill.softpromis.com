<?php
require_once '../../config/config.php';
include '../../components/auth_check.php';

// Get center ID from URL
$center_id = $_GET['id'] ?? '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: Add form validation and database update
    $center_name = $_POST['center_name'] ?? '';
    $partner_id = $_POST['partner_id'] ?? '';
    $location = $_POST['location'] ?? '';
    $address = $_POST['address'] ?? '';
    $contact_person = $_POST['contact_person'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $capacity = $_POST['capacity'] ?? '';
    $status = $_POST['status'] ?? 'active';
    
    // Simulate successful update
    header('Location: list.php?success=2');
    exit;
}

// TODO: Fetch partners list from database
$partners = [
    ['id' => 'TP001', 'name' => 'Tech Solutions Inc.'],
    ['id' => 'TP002', 'name' => 'Global Education'],
    ['id' => 'TP003', 'name' => 'Govt Training Dept']
];

// TODO: Fetch center details from database
$center = [
    'id' => $center_id,
    'name' => 'Tech Solutions HQ',
    'partner_id' => 'TP001',
    'location' => 'Silicon Valley, CA',
    'address' => '123 Tech Street, Silicon Valley, CA 94043',
    'contact_person' => 'John Smith',
    'email' => 'john@techsolutions.com',
    'phone' => '+1 234 567 8900',
    'capacity' => 50,
    'status' => 'active'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Training Center - Softpro Skill Solutions</title>
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
                <h1>Edit Training Center</h1>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="window.location.href='list.php'">
                        <i class="fas fa-arrow-left"></i>
                        Back to List
                    </button>
                </div>
            </div>

            <!-- Edit Center Form -->
            <div class="form-container">
                <form method="POST" action="edit.php?id=<?php echo $center_id; ?>" class="form">
                    <div class="form-group">
                        <label for="center_name">Center Name</label>
                        <input type="text" id="center_name" name="center_name" class="form-control" value="<?php echo $center['name']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="partner_id">Training Partner</label>
                        <select id="partner_id" name="partner_id" class="form-control" required>
                            <option value="">Select Partner</option>
                            <?php foreach ($partners as $partner): ?>
                            <option value="<?php echo $partner['id']; ?>" <?php echo $partner['id'] === $center['partner_id'] ? 'selected' : ''; ?>>
                                <?php echo $partner['name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" class="form-control" value="<?php echo $center['location']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" class="form-control" rows="3" required><?php echo $center['address']; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="contact_person">Contact Person</label>
                        <input type="text" id="contact_person" name="contact_person" class="form-control" value="<?php echo $center['contact_person']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo $center['email']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo $center['phone']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="capacity">Student Capacity</label>
                        <input type="number" id="capacity" name="capacity" class="form-control" min="1" value="<?php echo $center['capacity']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="active" <?php echo $center['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $center['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Update Center
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