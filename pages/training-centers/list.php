<?php
require_once '../../config/config.php';
include '../../components/auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training Centers - Softpro Skill Solutions</title>
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
                <h1>Training Centers</h1>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="window.location.href='add.php'">
                        <i class="fas fa-plus"></i>
                        Add New Center
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="filters">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search centers...">
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
                    <select id="partnerFilter" class="form-control">
                        <option value="">All Partners</option>
                        <option value="TP001">Tech Solutions Inc.</option>
                        <option value="TP002">Global Education</option>
                        <option value="TP003">Govt Training Dept</option>
                    </select>
                </div>
            </div>

            <!-- Centers List -->
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Center ID</th>
                            <th>Name</th>
                            <th>Partner</th>
                            <th>Location</th>
                            <th>Students</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>TC001</td>
                            <td>Tech Solutions HQ</td>
                            <td>Tech Solutions Inc.</td>
                            <td>Silicon Valley, CA</td>
                            <td>50</td>
                            <td><span class="badge badge-success">Active</span></td>
                            <td>
                                <a href="view.php?id=TC001" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=TC001" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deleteCenter('TC001')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>TC002</td>
                            <td>Tech Solutions East</td>
                            <td>Tech Solutions Inc.</td>
                            <td>New York, NY</td>
                            <td>40</td>
                            <td><span class="badge badge-success">Active</span></td>
                            <td>
                                <a href="view.php?id=TC002" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=TC002" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deleteCenter('TC002')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>TC003</td>
                            <td>Global Education HQ</td>
                            <td>Global Education</td>
                            <td>London, UK</td>
                            <td>35</td>
                            <td><span class="badge badge-warning">Inactive</span></td>
                            <td>
                                <a href="view.php?id=TC003" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=TC003" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deleteCenter('TC003')">
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
        function deleteCenter(id) {
            if (confirm('Are you sure you want to delete this center?')) {
                // TODO: Implement delete functionality
                alert('Center deleted: ' + id);
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
        document.getElementById('partnerFilter').addEventListener('change', applyFilters);

        function applyFilters() {
            const status = document.getElementById('statusFilter').value.toLowerCase();
            const partner = document.getElementById('partnerFilter').value.toLowerCase();
            const rows = document.querySelectorAll('.data-table tbody tr');
            
            rows.forEach(row => {
                const rowStatus = row.querySelector('.badge').textContent.toLowerCase();
                const rowPartner = row.cells[2].textContent.toLowerCase();
                
                const statusMatch = !status || rowStatus === status;
                const partnerMatch = !partner || rowPartner === partner;
                
                row.style.display = statusMatch && partnerMatch ? '' : 'none';
            });
        }
    </script>
</body>
</html> 