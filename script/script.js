// Form validation and interactivity

document.addEventListener('DOMContentLoaded', function() {
    // Get all forms on the page
    const loginForm = document.querySelector('.login-form');
    const registerForm = document.querySelector('.register-form');

    // Login form validation
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;

            if (!username) {
                e.preventDefault();
                alert('Please enter your username');
                return;
            }

            if (!password) {
                e.preventDefault();
                alert('Please enter your password');
                return;
            }
        });
    }

    // Registration form validation
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword')?.value || 
                                   document.getElementById('confirm_password')?.value;

            // Validate username
            if (!username || username.length < 3) {
                e.preventDefault();
                alert('Username must be at least 3 characters long');
                return;
            }

            // Validate email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email || !emailRegex.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address');
                return;
            }

            // Validate password
            if (!password || password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long');
                return;
            }

            // Validate password confirmation
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match');
                return;
            }
        });
    }

    // Show/hide password toggle
    const passwordToggleButtons = document.querySelectorAll('.toggle-password');
    passwordToggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const passwordField = this.previousElementSibling;
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                this.textContent = 'Hide';
            } else {
                passwordField.type = 'password';
                this.textContent = 'Show';
            }
        });
    });

    // Dismiss flash messages after 5 seconds
    const alerts = document.querySelectorAll('.alert, .flash-message');
    alerts.forEach(alert => {
        setTimeout(function() {
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 500);
        }, 5000);
    });

    // Smooth navigation
    const navLinks = document.querySelectorAll('a');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href && href.startsWith('http') === false && href !== '#') {
                // Allow natural navigation for internal links
            }
        });
    });
});

// Utility function to show loading state
function showLoadingState(button) {
    button.disabled = true;
    button.textContent = 'Loading...';
    button.style.opacity = '0.6';
}

// Utility function to reset button state
function resetButtonState(button, originalText) {
    button.disabled = false;
    button.textContent = originalText;
    button.style.opacity = '1';
}

// Check password strength
function checkPasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 6) strength++;
    if (password.length >= 10) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[!@#$%^&*]/.test(password)) strength++;
    
    return strength;
}

// Real-time password strength indicator (if element exists)
const passwordInput = document.getElementById('password');
if (passwordInput && document.querySelector('.password-strength')) {
    passwordInput.addEventListener('input', function() {
        const strength = checkPasswordStrength(this.value);
        const strengthIndicator = document.querySelector('.password-strength');
        
        const strengthLabels = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
        const strengthColors = ['#ff4757', '#ffa502', '#ffd93d', '#a4de6c', '#2ed573'];
        
        if (this.value.length === 0) {
            strengthIndicator.style.display = 'none';
        } else {
            strengthIndicator.style.display = 'block';
            strengthIndicator.textContent = `Strength: ${strengthLabels[strength]}`;
            strengthIndicator.style.color = strengthColors[strength];
        }
    });
}
