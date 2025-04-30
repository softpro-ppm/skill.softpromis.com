<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Softpro Skill Solutions - Bulk Upload</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css">
  <!-- Toastr -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light fixed-top">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="index.php" role="button">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </li>
    </ul>
  </nav>

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="dashboard.php" class="brand-link">
      <span class="brand-text font-weight-light">Softpro Skill Solutions</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="dashboard.php" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <!-- ... existing menu items ... -->
          <li class="nav-item">
            <a href="#" class="nav-link active">
              <i class="nav-icon fas fa-users"></i>
              <p>
                Students
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="students.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Manage Students</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="bulk-upload.php" class="nav-link active">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Bulk Upload</p>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Bulk Student Upload</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item"><a href="#">Students</a></li>
              <li class="breadcrumb-item active">Bulk Upload</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Upload Card -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Upload Student Data</h3>
          </div>
          <div class="card-body">
            <form id="uploadForm">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Training Center</label>
                    <select class="form-control select2" name="trainingCenter" required>
                      <option value="">Select Training Center</option>
                      <option value="1">Center 1</option>
                      <option value="2">Center 2</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Batch</label>
                    <select class="form-control select2" name="batch" required>
                      <option value="">Select Batch</option>
                      <option value="1">Batch 2024-01</option>
                      <option value="2">Batch 2024-02</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Upload File (CSV/Excel)</label>
                    <div class="custom-file">
                      <input type="file" class="custom-file-input" id="studentFile" accept=".csv,.xlsx,.xls" required>
                      <label class="custom-file-label" for="studentFile">Choose file</label>
                    </div>
                    <small class="form-text text-muted">
                      Download the <a href="#" id="downloadTemplate">template file</a> for reference
                    </small>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Upload and Validate
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Preview Card -->
        <div class="card" id="previewCard" style="display: none;">
          <div class="card-header">
            <h3 class="card-title">Data Preview</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-success" id="confirmUpload">
                <i class="fas fa-check"></i> Confirm Upload
              </button>
              <button type="button" class="btn btn-danger" id="cancelUpload">
                <i class="fas fa-times"></i> Cancel
              </button>
            </div>
          </div>
          <div class="card-body">
            <div class="alert alert-info">
              <h5><i class="icon fas fa-info"></i> Validation Results</h5>
              <p id="validationSummary"></p>
            </div>
            <table id="previewTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Row</th>
                  <th>Student ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Phone</th>
                  <th>Status</th>
                  <th>Errors</th>
                </tr>
              </thead>
              <tbody>
                <!-- Preview data will be populated here -->
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
  </div>
</div>

<!-- Required Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
$(document).ready(function() {
  // Initialize Select2
  $('.select2').select2({
    theme: 'bootstrap4'
  });

  // Initialize DataTable
  const previewTable = $('#previewTable').DataTable({
    responsive: true,
    lengthChange: true,
    autoWidth: false,
    pageLength: 10,
    order: [[0, 'asc']]
  });

  // File input change handler
  $('#studentFile').on('change', function() {
    const fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').html(fileName);
  });

  // Download template
  $('#downloadTemplate').click(function(e) {
    e.preventDefault();
    // Create and download template file
    const template = [
      ['Student ID', 'Name', 'Email', 'Phone', 'Address', 'Date of Birth', 'Gender', 'Education', 'Scheme'],
      ['ST001', 'John Doe', 'john@example.com', '1234567890', 'Address Line 1', '1990-01-01', 'Male', 'B.Tech', 'PMKVY']
    ];
    const ws = XLSX.utils.aoa_to_sheet(template);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Template');
    XLSX.writeFile(wb, 'student_upload_template.xlsx');
  });

  // Form submission
  $('#uploadForm').on('submit', function(e) {
    e.preventDefault();
    const file = $('#studentFile')[0].files[0];
    if (!file) {
      toastr.error('Please select a file to upload');
      return;
    }

    // Read and validate file
    const reader = new FileReader();
    reader.onload = function(e) {
      const data = new Uint8Array(e.target.result);
      const workbook = XLSX.read(data, { type: 'array' });
      const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
      const jsonData = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });

      // Validate data
      const validationResults = validateData(jsonData);
      displayPreview(validationResults);
    };
    reader.readAsArrayBuffer(file);
  });

  // Confirm upload
  $('#confirmUpload').click(function() {
    // Get validated data and send to server
    const validatedData = getValidatedData();
    uploadToServer(validatedData);
  });

  // Cancel upload
  $('#cancelUpload').click(function() {
    $('#previewCard').hide();
    $('#uploadForm')[0].reset();
    $('.custom-file-label').html('Choose file');
  });

  // Helper Functions
  function validateData(data) {
    const results = [];
    const headers = data[0];
    const requiredFields = ['Student ID', 'Name', 'Email', 'Phone'];
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const phoneRegex = /^\d{10}$/;

    for (let i = 1; i < data.length; i++) {
      const row = data[i];
      const errors = [];
      let status = 'Valid';

      // Check required fields
      requiredFields.forEach((field, index) => {
        if (!row[index]) {
          errors.push(`${field} is required`);
          status = 'Invalid';
        }
      });

      // Validate email
      if (row[2] && !emailRegex.test(row[2])) {
        errors.push('Invalid email format');
        status = 'Invalid';
      }

      // Validate phone
      if (row[3] && !phoneRegex.test(row[3])) {
        errors.push('Phone number must be 10 digits');
        status = 'Invalid';
      }

      results.push({
        row: i,
        studentId: row[0] || '',
        name: row[1] || '',
        email: row[2] || '',
        phone: row[3] || '',
        status: status,
        errors: errors
      });
    }

    return results;
  }

  function displayPreview(results) {
    // Clear existing data
    previewTable.clear();

    // Add new data
    results.forEach(result => {
      previewTable.row.add([
        result.row,
        result.studentId,
        result.name,
        result.email,
        result.phone,
        `<span class="badge badge-${result.status === 'Valid' ? 'success' : 'danger'}">${result.status}</span>`,
        result.errors.join('<br>')
      ]);
    });

    previewTable.draw();

    // Update validation summary
    const validCount = results.filter(r => r.status === 'Valid').length;
    const invalidCount = results.length - validCount;
    $('#validationSummary').html(`
      Total Records: ${results.length}<br>
      Valid Records: ${validCount}<br>
      Invalid Records: ${invalidCount}
    `);

    // Show preview card
    $('#previewCard').show();
  }

  function getValidatedData() {
    // Get only valid records from the preview table
    const validData = [];
    previewTable.rows().every(function() {
      const data = this.data();
      if (data[5].includes('success')) {
        validData.push({
          studentId: data[1],
          name: data[2],
          email: data[3],
          phone: data[4]
        });
      }
    });
    return validData;
  }

  function uploadToServer(data) {
    // Simulate server upload
    Swal.fire({
      title: 'Uploading...',
      text: 'Please wait while we process your data',
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    setTimeout(() => {
      Swal.fire({
        icon: 'success',
        title: 'Upload Complete',
        text: `${data.length} records have been successfully uploaded`,
        confirmButtonText: 'OK'
      }).then(() => {
        // Reset form and hide preview
        $('#uploadForm')[0].reset();
        $('.custom-file-label').html('Choose file');
        $('#previewCard').hide();
      });
    }, 2000);
  }
});
</script>
</body>
</html> 
