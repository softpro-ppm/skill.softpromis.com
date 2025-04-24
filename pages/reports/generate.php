<?php
require_once '../../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Report - Softpro Skill Solutions</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/report.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include '../../components/topbar.php'; ?>
    <?php include '../../components/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="page-header">
                <h1>Generate Report</h1>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="window.location.href='dashboard.php'">
                        <i class="fas fa-arrow-left"></i>
                        Back to Dashboard
                    </button>
                </div>
            </div>

            <!-- Report Generation Form -->
            <div class="report-form">
                <form id="generateReportForm" action="view.php" method="GET">
                    <div class="form-group">
                        <label for="reportType">Report Type</label>
                        <select id="reportType" name="type" class="form-control" required>
                            <option value="">Select Report Type</option>
                            <optgroup label="Attendance Reports">
                                <option value="attendance-monthly">Monthly Attendance Report</option>
                                <option value="attendance-daily">Daily Attendance Report</option>
                            </optgroup>
                            <optgroup label="Performance Reports">
                                <option value="performance-student">Student Performance Report</option>
                                <option value="performance-program">Program Effectiveness Report</option>
                            </optgroup>
                            <optgroup label="Financial Reports">
                                <option value="financial-monthly">Monthly Financial Report</option>
                                <option value="financial-analysis">Financial Analysis Report</option>
                            </optgroup>
                            <optgroup label="Center & Partner Reports">
                                <option value="center">Training Center Report</option>
                                <option value="partner">Training Partner Report</option>
                            </optgroup>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="dateRange">Date Range</label>
                        <div class="date-range">
                            <input type="date" id="startDate" name="start_date" class="form-control" required>
                            <span>to</span>
                            <input type="date" id="endDate" name="end_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="program">Program</label>
                        <select id="program" name="program" class="form-control">
                            <option value="">All Programs</option>
                            <option value="web-dev">Web Development</option>
                            <option value="mobile-dev">Mobile Development</option>
                            <option value="data-science">Data Science</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="batch">Batch</label>
                        <select id="batch" name="batch" class="form-control">
                            <option value="">All Batches</option>
                            <option value="batch-2024-1">Batch 2024-1</option>
                            <option value="batch-2024-2">Batch 2024-2</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="format">Report Format</label>
                        <select id="format" name="format" class="form-control" required>
                            <option value="html">HTML</option>
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-file-alt"></i>
                            Generate Report
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('generateReportForm');
            const reportType = document.getElementById('reportType');
            const programSelect = document.getElementById('program');
            const batchSelect = document.getElementById('batch');

            // Set default dates
            const today = new Date();
            const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            const lastDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 0);

            document.getElementById('startDate').value = lastMonth.toISOString().split('T')[0];
            document.getElementById('endDate').value = lastDayOfMonth.toISOString().split('T')[0];

            // Handle form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(form);
                const queryString = new URLSearchParams(formData).toString();
                window.location.href = 'view.php?' + queryString;
            });

            // Update batch options based on program selection
            programSelect.addEventListener('change', function() {
                const program = this.value;
                batchSelect.innerHTML = '<option value="">All Batches</option>';
                
                if (program === 'web-dev') {
                    batchSelect.innerHTML += `
                        <option value="web-2024-1">Web Dev Batch 2024-1</option>
                        <option value="web-2024-2">Web Dev Batch 2024-2</option>
                    `;
                } else if (program === 'mobile-dev') {
                    batchSelect.innerHTML += `
                        <option value="mobile-2024-1">Mobile Dev Batch 2024-1</option>
                        <option value="mobile-2024-2">Mobile Dev Batch 2024-2</option>
                    `;
                }
            });
        });
    </script>
</body>
</html> 