<?php
require_once '../../config/config.php';
include '../../components/auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance - Softpro Skill Solutions</title>
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
                <h1>Attendance</h1>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="window.location.href='add.php'">
                        <i class="fas fa-plus"></i>
                        Add New Attendance
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="filters">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search attendance...">
                    <i class="fas fa-search"></i>
                </div>
                <div class="filter-group">
                    <select id="batchFilter" class="form-control">
                        <option value="">All Batches</option>
                        <option value="B001">Web Development Batch 1</option>
                        <option value="B002">Mobile Development Batch 1</option>
                        <option value="B003">Data Science Batch 1</option>
                    </select>
                </div>
                <div class="filter-group">
                    <input type="date" id="dateFilter" class="form-control" placeholder="Select Date">
                </div>
            </div>

            <!-- Attendance List -->
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Attendance ID</th>
                            <th>Date</th>
                            <th>Batch</th>
                            <th>Total Students</th>
                            <th>Present</th>
                            <th>Absent</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>A001</td>
                            <td>15 Jan 2024</td>
                            <td>Web Development Batch 1</td>
                            <td>15</td>
                            <td>12</td>
                            <td>3</td>
                            <td><span class="badge badge-success">Completed</span></td>
                            <td>
                                <a href="view.php?id=A001" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=A001" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deleteAttendance('A001')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>A002</td>
                            <td>16 Jan 2024</td>
                            <td>Mobile Development Batch 1</td>
                            <td>20</td>
                            <td>18</td>
                            <td>2</td>
                            <td><span class="badge badge-success">Completed</span></td>
                            <td>
                                <a href="view.php?id=A002" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=A002" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deleteAttendance('A002')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>A003</td>
                            <td>17 Jan 2024</td>
                            <td>Data Science Batch 1</td>
                            <td>10</td>
                            <td>8</td>
                            <td>2</td>
                            <td><span class="badge badge-warning">Pending</span></td>
                            <td>
                                <a href="view.php?id=A003" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=A003" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deleteAttendance('A003')">
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
        function deleteAttendance(id) {
            if (confirm('Are you sure you want to delete this attendance record?')) {
                // TODO: Implement delete functionality
                alert('Attendance deleted: ' + id);
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
        document.getElementById('batchFilter').addEventListener('change', applyFilters);
        document.getElementById('dateFilter').addEventListener('change', applyFilters);

        function applyFilters() {
            const batch = document.getElementById('batchFilter').value.toLowerCase();
            const date = document.getElementById('dateFilter').value;
            const rows = document.querySelectorAll('.data-table tbody tr');
            
            rows.forEach(row => {
                const rowBatch = row.cells[2].textContent.toLowerCase();
                const rowDate = row.cells[1].textContent;
                
                const batchMatch = !batch || rowBatch === batch;
                const dateMatch = !date || rowDate === date;
                
                row.style.display = batchMatch && dateMatch ? '' : 'none';
            });
        }
    </script>
</body>
</html> 