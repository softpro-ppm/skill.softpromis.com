<?php
require_once '../../config/config.php';
include '../../components/auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training Programs - Softpro Skill Solutions</title>
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
                <h1>Training Programs</h1>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="window.location.href='add.php'">
                        <i class="fas fa-plus"></i>
                        Add New Program
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="filters">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search programs...">
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
                    <select id="categoryFilter" class="form-control">
                        <option value="">All Categories</option>
                        <option value="web">Web Development</option>
                        <option value="mobile">Mobile Development</option>
                        <option value="data">Data Science</option>
                    </select>
                </div>
            </div>

            <!-- Programs List -->
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Program ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Duration</th>
                            <th>Students</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>TP001</td>
                            <td>Web Development Bootcamp</td>
                            <td>Web Development</td>
                            <td>6 months</td>
                            <td>45</td>
                            <td><span class="badge badge-success">Active</span></td>
                            <td>
                                <a href="view.php?id=TP001" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=TP001" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deleteProgram('TP001')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>TP002</td>
                            <td>Mobile App Development</td>
                            <td>Mobile Development</td>
                            <td>4 months</td>
                            <td>30</td>
                            <td><span class="badge badge-success">Active</span></td>
                            <td>
                                <a href="view.php?id=TP002" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=TP002" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deleteProgram('TP002')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>TP003</td>
                            <td>Data Science Fundamentals</td>
                            <td>Data Science</td>
                            <td>5 months</td>
                            <td>25</td>
                            <td><span class="badge badge-warning">Inactive</span></td>
                            <td>
                                <a href="view.php?id=TP003" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=TP003" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deleteProgram('TP003')">
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
        function deleteProgram(id) {
            if (confirm('Are you sure you want to delete this program?')) {
                // TODO: Implement delete functionality
                alert('Program deleted: ' + id);
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
        document.getElementById('categoryFilter').addEventListener('change', applyFilters);

        function applyFilters() {
            const status = document.getElementById('statusFilter').value.toLowerCase();
            const category = document.getElementById('categoryFilter').value.toLowerCase();
            const rows = document.querySelectorAll('.data-table tbody tr');
            
            rows.forEach(row => {
                const rowStatus = row.querySelector('.badge').textContent.toLowerCase();
                const rowCategory = row.cells[2].textContent.toLowerCase();
                
                const statusMatch = !status || rowStatus === status;
                const categoryMatch = !category || rowCategory === category;
                
                row.style.display = statusMatch && categoryMatch ? '' : 'none';
            });
        }
    </script>
</body>
</html> 