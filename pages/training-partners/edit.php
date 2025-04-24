<?php
require_once '../../config/config.php';
include '../../components/auth_check.php';

// Get partner ID from URL
$partner_id = $_GET['id'] ?? '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: Add form validation and database update
    $partner_name = $_POST['partner_name'] ?? '';
    $type = $_POST['type'] ?? '';
    $contact_person = $_POST['contact_person'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $status = $_POST['status'] ?? 'active';
    
    // Simulate successful update
    header('Location: list.php?success=2');
    exit;
}

// TODO: Fetch partner details from database
$partner = [
    'id' => $partner_id,
    'name' => 'Tech Solutions Inc.',
    'type' => 'corporate',
    'contact_person' => 'John Smith',
    'email' => 'john@techsolutions.com',
    'phone' => '+1 234 567 8900',
    'address' => '123 Tech Street, Silicon Valley, CA 94043',
    'status' => 'active'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Training Partner - Softpro Skill Solutions</title>
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
                <h1>Edit Training Partner</h1>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="window.location.href='list.php'">
                        <i class="fas fa-arrow-left"></i>
                        Back to List
                    </button>
                </div>
            </div>

            <!-- Edit Partner Form -->
            <div class="form-container">
                <form method="POST" action="edit.php?id=<?php echo $partner_id; ?>" class="form">
                    <div class="form-group">
                        <label for="partner_name">Partner Name</label>
                        <input type="text" id="partner_name" name="partner_name" class="form-control" value="<?php echo $partner['name']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="type">Partner Type</label>
                        <select id="type" name="type" class="form-control" required>
                            <option value="">Select Type</option>
                            <option value="corporate" <?php echo $partner['type'] === 'corporate' ? 'selected' : ''; ?>>Corporate</option>
                            <option value="educational" <?php echo $partner['type'] === 'educational' ? 'selected' : ''; ?>>Educational</option>
                            <option value="government" <?php echo $partner['type'] === 'government' ? 'selected' : ''; ?>>Government</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="contact_person">Contact Person</label>
                        <input type="text" id="contact_person" name="contact_person" class="form-control" value="<?php echo $partner['contact_person']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo $partner['email']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo $partner['phone']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" class="form-control" rows="3" required><?php echo $partner['address']; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="active" <?php echo $partner['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $partner['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Update Partner
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