document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('.login-form');
    
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        const rememberMe = document.querySelector('input[name="remember"]').checked;
        
        // Here you would typically make an API call to authenticate
        // For now, we'll just log the values and redirect
        console.log('Login attempt:', {
            username,
            password,
            rememberMe
        });
        
        // Simulate successful login and redirect
        // In a real application, this would be handled by the authentication response
        window.location.href = '../index.html';
    });
    
    // Handle forgot password click
    const forgotPassword = document.querySelector('.forgot-password');
    forgotPassword.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Here you would typically show a modal or redirect to a password reset page
        alert('Password reset functionality will be implemented here.');
    });
}); 