<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Softpro Skill Solutions - Forgot Password</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
  <!-- Toastr -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    .login-page {
      background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
    }
    .login-box {
      margin-top: 0;
      padding-top: 100px;
    }
    .login-logo {
      font-size: 2.1rem;
      font-weight: 300;
      margin-bottom: 0.9rem;
      text-align: center;
      color: #fff;
    }
    .login-logo img {
      max-width: 150px;
      margin-bottom: 1rem;
    }
    .login-card-body {
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    .input-group-text {
      background-color: transparent;
      border-right: none;
    }
    .form-control {
      border-left: none;
    }
    .form-control:focus {
      box-shadow: none;
      border-color: #ced4da;
    }
    .btn-primary {
      background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
      border: none;
      padding: 10px 20px;
    }
    .btn-primary:hover {
      background: linear-gradient(135deg, #0056b3 0%, #520dc2 100%);
    }
  </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <img src="https://via.placeholder.com/150" alt="Logo">
    <br>
    <b>Softpro</b> Skill Solutions
  </div>
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">You forgot your password? Here you can easily retrieve a new password.</p>

      <form id="forgotPasswordForm">
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text">
              <i class="fas fa-envelope"></i>
            </span>
          </div>
          <input type="email" class="form-control" placeholder="Email" name="email" required>
        </div>
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Request new password</button>
          </div>
        </div>
      </form>

      <p class="mt-3 mb-1">
        <a href="index.php">Back to Login</a>
      </p>
    </div>
  </div>
</div>

<!-- Required Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
$(document).ready(function() {
  // Configure Toastr
  toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: "toast-top-right",
    timeOut: 3000
  };

  // Form submission
  $('#forgotPasswordForm').on('submit', function(e) {
    e.preventDefault();
    
    // Get email
    const email = $('input[name="email"]').val();

    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      toastr.error('Please enter a valid email address');
      return;
    }

    // Show loading state
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Sending...').prop('disabled', true);

    // Simulate server request
    setTimeout(() => {
      // Check if email exists (this would be replaced with actual server validation)
      if (email === 'admin@example.com') {
        // Show success message
        Swal.fire({
          icon: 'success',
          title: 'Reset Link Sent!',
          text: 'Please check your email for password reset instructions.',
          confirmButtonText: 'OK'
        }).then(() => {
          window.location.href = 'index.html';
        });
      } else {
        toastr.error('No account found with this email address');
        submitBtn.html(originalText).prop('disabled', false);
      }
    }, 1500);
  });
});
</script>
</body>
</html> 
