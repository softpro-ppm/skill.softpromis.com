document.addEventListener('DOMContentLoaded', function() {
    const forgotPasswordForm = document.querySelector('.login-form');
    
    forgotPasswordForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const email = document.getElementById('email').value;
        
        // Here you would typically make an API call to send reset link
        // For now, we'll just log the email and show a success message
        console.log('Reset password requested for:', email);
        
        // Show success message
        alert('If an account exists with this email, you will receive a password reset link shortly.');
        
        // Redirect back to login
        window.location.href = '../pages/login.html';
    });
}); 