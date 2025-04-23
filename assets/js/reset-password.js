document.addEventListener('DOMContentLoaded', function() {
    const resetPasswordForm = document.querySelector('.login-form');
    const newPasswordInput = document.getElementById('new-password');
    const confirmPasswordInput = document.getElementById('confirm-password');
    
    // Password validation function
    function validatePassword(password) {
        const minLength = 8;
        const hasUpperCase = /[A-Z]/.test(password);
        const hasLowerCase = /[a-z]/.test(password);
        const hasNumbers = /\d/.test(password);
        const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
        
        return password.length >= minLength && 
               hasUpperCase && 
               hasLowerCase && 
               hasNumbers && 
               hasSpecialChar;
    }
    
    resetPasswordForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const newPassword = newPasswordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        // Validate password
        if (!validatePassword(newPassword)) {
            alert('Password must be at least 8 characters long and include uppercase, lowercase, number, and special character');
            return;
        }
        
        // Check if passwords match
        if (newPassword !== confirmPassword) {
            alert('Passwords do not match');
            return;
        }
        
        // Here you would typically make an API call to reset password
        // For now, we'll just log the new password and show a success message
        console.log('Password reset requested');
        
        // Show success message
        alert('Your password has been successfully reset.');
        
        // Redirect to login
        window.location.href = '../pages/login.html';
    });
}); 