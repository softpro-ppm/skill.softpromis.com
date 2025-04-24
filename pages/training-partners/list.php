<?php
require_once '../../config/config.php';
include '../../components/auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training Partners - Softpro Skill Solutions</title>
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
                <h1>Training Partners</h1>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="window.location.href='add.php'">
                        <i class="fas fa-plus"></i>
                        Add New Partner
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="filters">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search partners...">
                    <i class="fas fa-search"></i>
                </div>
                <div class="filter-group">
                    <select id="statusFilter" class="form-control">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="filter-group">
                    <select id="typeFilter" class="form-control">
                        <option value="">All Types</option>
                        <option value="corporate">Corporate</option>
                        <option value="educational">Educational</option>
                        <option value="government">Government</option>
                    </select>
                </div>
            </div>

            <!-- Partners List -->
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Partner ID</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Contact Person</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>TP001</td>
                            <td>Tech Solutions Inc.</td>
                            <td>Corporate</td>
                            <td>John Smith</td>
                            <td>john@techsolutions.com</td>
                            <td><span class="badge badge-success">Active</span></td>
                            <td>
                                <a href="view.php?id=TP001" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=TP001" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deletePartner('TP001')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>TP002</td>
                            <td>Global Education</td>
                            <td>Educational</td>
                            <td>Sarah Johnson</td>
                            <td>sarah@globaledu.com</td>
                            <td><span class="badge badge-success">Active</span></td>
                            <td>
                                <a href="view.php?id=TP002" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=TP002" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deletePartner('TP002')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>TP003</td>
                            <td>Govt Training Dept</td>
                            <td>Government</td>
                            <td>Mike Brown</td>
                            <td>mike@govtdept.com</td>
                            <td><span class="badge badge-warning">Inactive</span></td>
                            <td>
                                <a href="view.php?id=TP003" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=TP003" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deletePartner('TP003')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <button class="btn btn-sm" disabled>Previous</button>
                <button class="btn btn-sm active">1</button>
                <button class="btn btn-sm">2</button>
                <button class="btn btn-sm">3</button>
                <button class="btn btn-sm">Next</button>
            </div>
        </div>
    </main>

    <script src="<?php echo $base_url; ?>assets/js/main.js"></script>
    <script>
        function deletePartner(id) {
            if (confirm('Are you sure you want to delete this partner?')) {
                // TODO: Implement delete functionality
                alert('Partner deleted: ' + id);
            }
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchText = this.value.toLowerCase();
            const rows = document.querySelectorAll('.data-table tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchText) ? '' : 'none';
            });
        });

        // Filter functionality
        document.getElementById('statusFilter').addEventListener('change', applyFilters);
        document.getElementById('typeFilter').addEventListener('change', applyFilters);

        function applyFilters() {
            const status = document.getElementById('statusFilter').value.toLowerCase();
            const type = document.getElementById('typeFilter').value.toLowerCase();
            const rows = document.querySelectorAll('.data-table tbody tr');
            
            rows.forEach(row => {
                const rowStatus = row.querySelector('.badge').textContent.toLowerCase();
                const rowType = row.cells[2].textContent.toLowerCase();
                
                const statusMatch = !status || rowStatus === status;
                const typeMatch = !type || rowType === type;
                
                row.style.display = statusMatch && typeMatch ? '' : 'none';
            });
        }
    </script>
</body>
</html> 