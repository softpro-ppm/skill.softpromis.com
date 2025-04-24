<?php
require_once '../../config/config.php';
include '../../components/auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results - Softpro Skill Solutions</title>
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
                <h1>Results</h1>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="window.location.href='add.php'">
                        <i class="fas fa-plus"></i>
                        Add New Result
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="filters">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search results...">
                    <i class="fas fa-search"></i>
                </div>
                <div class="filter-group">
                    <select id="assessmentFilter" class="form-control">
                        <option value="">All Assessments</option>
                        <option value="A001">Web Development Basics</option>
                        <option value="A002">HTML & CSS</option>
                        <option value="A003">JavaScript Fundamentals</option>
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

            <!-- Results List -->
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Result ID</th>
                            <th>Student</th>
                            <th>Assessment</th>
                            <th>Center</th>
                            <th>Date</th>
                            <th>Score</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>R001</td>
                            <td>John Doe</td>
                            <td>Web Development Basics</td>
                            <td>Tech Solutions HQ</td>
                            <td>15 Jan 2024</td>
                            <td>85%</td>
                            <td><span class="badge badge-success">Passed</span></td>
                            <td>
                                <a href="view.php?id=R001" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=R001" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deleteResult('R001')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>R002</td>
                            <td>Jane Smith</td>
                            <td>HTML & CSS</td>
                            <td>Tech Solutions East</td>
                            <td>30 Jan 2024</td>
                            <td>90%</td>
                            <td><span class="badge badge-success">Passed</span></td>
                            <td>
                                <a href="view.php?id=R002" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=R002" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deleteResult('R002')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>R003</td>
                            <td>Mike Johnson</td>
                            <td>JavaScript Fundamentals</td>
                            <td>Global Education HQ</td>
                            <td>15 Feb 2024</td>
                            <td>75%</td>
                            <td><span class="badge badge-warning">Failed</span></td>
                            <td>
                                <a href="view.php?id=R003" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=R003" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deleteResult('R003')">
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
        function deleteResult(id) {
            if (confirm('Are you sure you want to delete this result?')) {
                // TODO: Implement delete functionality
                alert('Result deleted: ' + id);
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
        document.getElementById('assessmentFilter').addEventListener('change', applyFilters);
        document.getElementById('centerFilter').addEventListener('change', applyFilters);

        function applyFilters() {
            const assessment = document.getElementById('assessmentFilter').value.toLowerCase();
            const center = document.getElementById('centerFilter').value.toLowerCase();
            const rows = document.querySelectorAll('.data-table tbody tr');
            
            rows.forEach(row => {
                const rowAssessment = row.cells[2].textContent.toLowerCase();
                const rowCenter = row.cells[3].textContent.toLowerCase();
                
                const assessmentMatch = !assessment || rowAssessment === assessment;
                const centerMatch = !center || rowCenter === center;
                
                row.style.display = assessmentMatch && centerMatch ? '' : 'none';
            });
        }
    </script>
</body>
</html> 