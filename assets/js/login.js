// Login function to handle user authentication
function handleLogin(event) {
    event.preventDefault();

    // Get form data
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const rememberMe = document.getElementById('remember').checked;

    // Validate form data
    if (!email || !password) {
        toastr.error('Please fill in all required fields');
        return;
    }

    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        toastr.error('Please enter a valid email address');
        return;
    }

    // Show loading state
    const submitBtn = document.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
    submitBtn.disabled = true;

    // Simulate server request
    setTimeout(() => {
        // This would be replaced with actual API call
        const mockUsers = [
            {
                email: 'admin@example.com',
                password: 'Admin@123',
                role: 'Administrator',
                name: 'Admin User'
            },
            {
                email: 'manager@example.com',
                password: 'Manager@123',
                role: 'Center Manager',
                name: 'Manager User'
            },
            {
                email: 'trainer@example.com',
                password: 'Trainer@123',
                role: 'Trainer',
                name: 'Trainer User'
            }
        ];

        const user = mockUsers.find(u => u.email === email && u.password === password);

        if (user) {
            // Store user data in session storage
            sessionStorage.setItem('user', JSON.stringify({
                email: user.email,
                role: user.role,
                name: user.name
            }));

            // If remember me is checked, store in localStorage
            if (rememberMe) {
                localStorage.setItem('user', JSON.stringify({
                    email: user.email,
                    role: user.role,
                    name: user.name
                }));
            }

            // Show success message
            toastr.success('Login successful! Redirecting...');

            // Redirect based on role
            setTimeout(() => {
                switch (user.role) {
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
            }, 1000);
        } else {
            toastr.error('Invalid email or password');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    }, 1500);
}

// Check if user is already logged in
function checkAuth() {
    const user = JSON.parse(sessionStorage.getItem('user') || localStorage.getItem('user'));
    if (user) {
        // Redirect based on role
        switch (user.role) {
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
        timeOut: 3000
    };
}); 