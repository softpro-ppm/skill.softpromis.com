<?php
require_once '../../config/config.php';
include '../../components/auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batches - Softpro Skill Solutions</title>
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
                <h1>Batches</h1>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="window.location.href='add.php'">
                        <i class="fas fa-plus"></i>
                        Add New Batch
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="filters">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search batches...">
                    <i class="fas fa-search"></i>
                </div>
                <div class="filter-group">
                    <select id="statusFilter" class="form-control">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                        <option value="upcoming">Upcoming</option>
                    </select>
                </div>
                <div class="filter-group">
                    <select id="programFilter" class="form-control">
                        <option value="">All Programs</option>
                        <option value="P001">Web Development</option>
                        <option value="P002">Mobile Development</option>
                        <option value="P003">Data Science</option>
                    </select>
                </div>
            </div>

            <!-- Batches List -->
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Batch ID</th>
                            <th>Name</th>
                            <th>Program</th>
                            <th>Center</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Students</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>B001</td>
                            <td>Web Development Batch 1</td>
                            <td>Web Development</td>
                            <td>Tech Solutions HQ</td>
                            <td>01 Jan 2024</td>
                            <td>30 Jun 2024</td>
                            <td>15</td>
                            <td><span class="badge badge-success">Active</span></td>
                            <td>
                                <a href="view.php?id=B001" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=B001" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deleteBatch('B001')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>B002</td>
                            <td>Mobile Development Batch 1</td>
                            <td>Mobile Development</td>
                            <td>Tech Solutions East</td>
                            <td>01 Mar 2024</td>
                            <td>31 Aug 2024</td>
                            <td>20</td>
                            <td><span class="badge badge-success">Active</span></td>
                            <td>
                                <a href="view.php?id=B002" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=B002" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deleteBatch('B002')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>B003</td>
                            <td>Data Science Batch 1</td>
                            <td>Data Science</td>
                            <td>Global Education HQ</td>
                            <td>01 Jul 2024</td>
                            <td>31 Dec 2024</td>
                            <td>10</td>
                            <td><span class="badge badge-warning">Upcoming</span></td>
                            <td>
                                <a href="view.php?id=B003" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=B003" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deleteBatch('B003')">
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
        function deleteBatch(id) {
            if (confirm('Are you sure you want to delete this batch?')) {
                // TODO: Implement delete functionality
                alert('Batch deleted: ' + id);
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
        document.getElementById('programFilter').addEventListener('change', applyFilters);

        function applyFilters() {
            const status = document.getElementById('statusFilter').value.toLowerCase();
            const program = document.getElementById('programFilter').value.toLowerCase();
            const rows = document.querySelectorAll('.data-table tbody tr');
            
            rows.forEach(row => {
                const rowStatus = row.querySelector('.badge').textContent.toLowerCase();
                const rowProgram = row.cells[2].textContent.toLowerCase();
                
                const statusMatch = !status || rowStatus === status;
                const programMatch = !program || rowProgram === program;
                
                row.style.display = statusMatch && programMatch ? '' : 'none';
            });
        }
    </script>
</body>
</html> 