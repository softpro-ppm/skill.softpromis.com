<?php
require_once '../../config/config.php';
include '../../components/auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students - Softpro Skill Solutions</title>
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
                <h1>Students</h1>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="window.location.href='add.php'">
                        <i class="fas fa-plus"></i>
                        Add New Student
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="filters">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search students...">
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
                    <select id="centerFilter" class="form-control">
                        <option value="">All Centers</option>
                        <option value="TC001">Tech Solutions HQ</option>
                        <option value="TC002">Tech Solutions East</option>
                        <option value="TC003">Global Education HQ</option>
                    </select>
                </div>
            </div>

            <!-- Students List -->
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Center</th>
                            <th>Program</th>
                            <th>Batch</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>S001</td>
                            <td>John Doe</td>
                            <td>Tech Solutions HQ</td>
                            <td>Web Development</td>
                            <td>B001</td>
                            <td><span class="badge badge-success">Active</span></td>
                            <td>
                                <a href="view.php?id=S001" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=S001" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deleteStudent('S001')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>S002</td>
                            <td>Jane Smith</td>
                            <td>Tech Solutions East</td>
                            <td>Mobile Development</td>
                            <td>B002</td>
                            <td><span class="badge badge-success">Active</span></td>
                            <td>
                                <a href="view.php?id=S002" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=S002" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deleteStudent('S002')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>S003</td>
                            <td>Mike Johnson</td>
                            <td>Global Education HQ</td>
                            <td>Data Science</td>
                            <td>B003</td>
                            <td><span class="badge badge-warning">Inactive</span></td>
                            <td>
                                <a href="view.php?id=S003" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=S003" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deleteStudent('S003')">
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
        function deleteStudent(id) {
            if (confirm('Are you sure you want to delete this student?')) {
                // TODO: Implement delete functionality
                alert('Student deleted: ' + id);
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
        document.getElementById('centerFilter').addEventListener('change', applyFilters);

        function applyFilters() {
            const status = document.getElementById('statusFilter').value.toLowerCase();
            const center = document.getElementById('centerFilter').value.toLowerCase();
            const rows = document.querySelectorAll('.data-table tbody tr');
            
            rows.forEach(row => {
                const rowStatus = row.querySelector('.badge').textContent.toLowerCase();
                const rowCenter = row.cells[2].textContent.toLowerCase();
                
                const statusMatch = !status || rowStatus === status;
                const centerMatch = !center || rowCenter === center;
                
                row.style.display = statusMatch && centerMatch ? '' : 'none';
            });
        }
    </script>
</body>
</html> 