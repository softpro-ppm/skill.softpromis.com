<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Softpro Skill Solutions - Reset Password</title>

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
    .password-strength {
      height: 5px;
      margin-top: 5px;
      border-radius: 2px;
      transition: all 0.3s ease;
    }
    .password-strength.weak {
      background-color: #dc3545;
      width: 25%;
    }
    .password-strength.medium {
      background-color: #ffc107;
      width: 50%;
    }
    .password-strength.strong {
      background-color: #28a745;
      width: 75%;
    }
    .password-strength.very-strong {
      background-color: #20c997;
      width: 100%;
    }
    .password-requirements {
      font-size: 0.8rem;
      color: #6c757d;
      margin-top: 5px;
    }
    .password-requirements ul {
      list-style: none;
      padding-left: 0;
      margin-bottom: 0;
    }
    .password-requirements li {
      margin-bottom: 2px;
    }
    .password-requirements li.valid {
      color: #28a745;
    }
    .password-requirements li.valid::before {
      content: '✓';
      margin-right: 5px;
    }
    .password-requirements li.invalid {
      color: #dc3545;
    }
    .password-requirements li.invalid::before {
      content: '×';
      margin-right: 5px;
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
      <p class="login-box-msg">Reset your password</p>

      <form id="resetPasswordForm">
        <input type="hidden" name="token" id="resetToken">
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text">
              <i class="fas fa-lock"></i>
            </span>
          </div>
          <input type="password" class="form-control" placeholder="New Password" name="password" required>
          <div class="input-group-append">
            <span class="input-group-text">
              <i class="fas fa-eye toggle-password"></i>
            </span>
          </div>
        </div>
        <div class="password-strength"></div>
        <div class="password-requirements">
          <ul>
            <li class="invalid" data-requirement="length">At least 8 characters long</li>
            <li class="invalid" data-requirement="uppercase">Contains uppercase letter</li>
            <li class="invalid" data-requirement="lowercase">Contains lowercase letter</li>
            <li class="invalid" data-requirement="number">Contains number</li>
            <li class="invalid" data-requirement="special">Contains special character</li>
          </ul>
        </div>
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text">
              <i class="fas fa-lock"></i>
            </span>
          </div>
          <input type="password" class="form-control" placeholder="Confirm Password" name="confirmPassword" required>
        </div>
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
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

  // Get token from URL
  const urlParams = new URLSearchParams(window.location.search);
  const token = urlParams.get('token');
  if (!token) {
    Swal.fire({
      icon: 'error',
      title: 'Invalid Reset Link',
      text: 'The password reset link is invalid or has expired.',
      confirmButtonText: 'OK'
    }).then(() => {
      window.location.href = 'forgot-password.html';
    });
  }
  $('#resetToken').val(token);

  // Toggle password visibility
  $('.toggle-password').click(function() {
    const passwordInput = $('input[name="password"]');
    const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
    passwordInput.attr('type', type);
    $(this).toggleClass('fa-eye fa-eye-slash');
  });

  // Password strength checker
  $('input[name="password"]').on('input', function() {
    const password = $(this).val();
    const strengthBar = $('.password-strength');
    const requirements = $('.password-requirements li');

    // Reset requirements
    requirements.removeClass('valid invalid');

    // Check password requirements
    const checks = {
      length: password.length >= 8,
      uppercase: /[A-Z]/.test(password),
      lowercase: /[a-z]/.test(password),
      number: /[0-9]/.test(password),
      special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
    };

    // Update requirement indicators
    Object.keys(checks).forEach(requirement => {
      const element = $(`[data-requirement="${requirement}"]`);
      if (checks[requirement]) {
        element.addClass('valid').removeClass('invalid');
      } else {
        element.addClass('invalid').removeClass('valid');
      }
    });

    // Calculate strength
    const strength = Object.values(checks).filter(Boolean).length;
    strengthBar.removeClass('weak medium strong very-strong');
    
    if (strength <= 2) {
      strengthBar.addClass('weak');
    } else if (strength <= 3) {
      strengthBar.addClass('medium');
    } else if (strength <= 4) {
      strengthBar.addClass('strong');
    } else {
      strengthBar.addClass('very-strong');
    }
  });

  // Form submission
  $('#resetPasswordForm').on('submit', function(e) {
    e.preventDefault();
    
    const password = $('input[name="password"]').val();
    const confirmPassword = $('input[name="confirmPassword"]').val();
    const token = $('#resetToken').val();

    // Validate password match
    if (password !== confirmPassword) {
      toastr.error('Passwords do not match');
      return;
    }

    // Validate password strength
    const checks = {
      length: password.length >= 8,
      uppercase: /[A-Z]/.test(password),
      lowercase: /[a-z]/.test(password),
      number: /[0-9]/.test(password),
      special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
    };

    if (Object.values(checks).some(check => !check)) {
      toastr.error('Please meet all password requirements');
      return;
    }

    // Show loading state
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Resetting...').prop('disabled', true);

    // Simulate server request
    setTimeout(() => {
      // Check token validity (this would be replaced with actual server validation)
      if (token === 'valid-token') {
        // Show success message
        Swal.fire({
          icon: 'success',
          title: 'Password Reset Successful!',
          text: 'Your password has been reset successfully. Please login with your new password.',
          confirmButtonText: 'OK'
        }).then(() => {
          window.location.href = 'index.html';
        });
      } else {
        toastr.error('Invalid or expired reset token');
        submitBtn.html(originalText).prop('disabled', false);
      }
    }, 1500);
  });
});
</script>
</body>
</html> 
