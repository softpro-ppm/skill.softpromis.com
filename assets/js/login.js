// Login function to handle user authentication
function handleLogin(event) {
    event.preventDefault();

    // Get form data
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const rememberMe = document.getElementById('remember').checked;

    // Validate form data
    if (!email || !password) {
        showError('Please fill in all required fields');
        return;
    }

    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showError('Please enter a valid email address');
        return;
    }

    // Show loading state
    const submitBtn = document.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
    submitBtn.disabled = true;

    console.log('Sending login request to:', 'inc/ajax/login_ajax.php');
    
    // Make AJAX request to login endpoint
    $.ajax({
        url: 'inc/ajax/login_ajax.php',
        type: 'POST',
        data: {
            email: email,
            password: password,
            remember: rememberMe
        },
        dataType: 'json',
        success: function(response) {
            console.log('Login response:', response);
            if (response.success) {
                // Store user data
                storeUserData(response.user, rememberMe);
                
                // Show success message
                showSuccess('Login successful! Redirecting...');

                // Redirect based on role
                setTimeout(() => {
                    redirectBasedOnRole(response.user.role);
                }, 1000);
            } else {
                showError(response.message || 'Invalid email or password');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        },
        error: function(xhr, status, error) {
            console.error('Login error details:', {
                status: status,
                error: error,
                responseText: xhr.responseText,
                statusCode: xhr.status,
                readyState: xhr.readyState
            });
            showError('An error occurred. Please try again later.');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });
}

// Store user data in storage
function storeUserData(user, rememberMe) {
    const userData = {
        email: user.email,
        role: user.role,
        name: user.name,
        token: user.token // If using JWT or other token-based auth
    };

    // Store in session storage
    sessionStorage.setItem('user', JSON.stringify(userData));

    // If remember me is checked, store in localStorage
    if (rememberMe) {
        localStorage.setItem('user', JSON.stringify(userData));
    }
}

// Redirect based on user role
function redirectBasedOnRole(role) {
    switch (role) {
        case 'Administrator':
            window.location.href = 'dashboard.php';
            break;
        case 'Center Manager':
            window.location.href = 'training-centers.php';
            break;
        case 'Trainer':
            window.location.href = 'courses.php';
            break;
        default:
            window.location.href = 'dashboard.php';
    }
}

// Check if user is already logged in
function checkAuth() {
    const user = JSON.parse(sessionStorage.getItem('user') || localStorage.getItem('user'));
    if (user) {
        // Verify token if using token-based auth
        verifyToken(user.token).then(isValid => {
            if (isValid) {
                redirectBasedOnRole(user.role);
            } else {
                clearUserData();
            }
        }).catch(() => {
            clearUserData();
        });
    }
}

// Verify token (if using token-based auth)
function verifyToken(token) {
    return new Promise((resolve) => {
        $.ajax({
            url: 'inc/ajax/verify_token.php',
            type: 'POST',
            data: { token: token },
            dataType: 'json',
            success: function(response) {
                resolve(response.valid);
            },
            error: function() {
                resolve(false);
            }
        });
    });
}

// Clear user data from storage
function clearUserData() {
    sessionStorage.removeItem('user');
    localStorage.removeItem('user');
}

// Toggle password visibility
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.querySelector('.toggle-password i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Show error message
function showError(message) {
    toastr.error(message);
}

// Show success message
function showSuccess(message) {
    toastr.success(message);
}

// Handle logout
function handleLogout() {
    // Show loading state
    const logoutBtn = document.querySelector('.logout-btn');
    if (logoutBtn) {
        const originalText = logoutBtn.innerHTML;
        logoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging out...';
        logoutBtn.disabled = true;
    }

    // Clear local storage and session storage
    localStorage.removeItem('user');
    sessionStorage.removeItem('user');

    // Make AJAX request to logout endpoint
    $.ajax({
        url: 'logout.php',
        type: 'POST',
        success: function() {
            // Show success message
            showSuccess('Logged out successfully');
            
            // Redirect to login page
            setTimeout(() => {
                window.location.href = 'index.php';
            }, 1000);
        },
        error: function(xhr, status, error) {
            console.error('Logout error:', error);
            // Still redirect to login page even if there's an error
            window.location.href = 'index.php';
        }
    });
}

// Initialize login page
document.addEventListener('DOMContentLoaded', function() {
    // Check if user is already logged in
    checkAuth();

    // Add event listener to login form
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }

    // Add event listener to password toggle
    const togglePasswordBtn = document.querySelector('.toggle-password');
    if (togglePasswordBtn) {
        togglePasswordBtn.addEventListener('click', togglePassword);
    }

    // Configure Toastr
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        timeOut: 3000,
        preventDuplicates: true
    };

    // Add enter key support for form submission
    document.getElementById('password').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            handleLogin(e);
        }
    });

    // Add event listener to logout button
    const logoutBtn = document.querySelector('.logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            handleLogout();
        });
    }
}); 