/**
 * Dulify Platform - Main JavaScript File
 */

document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            let isValid = true;
            
            // Validate required fields
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                    
                    // Add error message if it doesn't exist
                    let errorElement = field.nextElementSibling;
                    if (!errorElement || !errorElement.classList.contains('error-message')) {
                        errorElement = document.createElement('span');
                        errorElement.classList.add('error-message');
                        errorElement.style.color = 'red';
                        errorElement.style.fontSize = '14px';
                        field.parentNode.insertBefore(errorElement, field.nextSibling);
                    }
                    
                    errorElement.textContent = 'This field is required';
                } else {
                    field.classList.remove('is-invalid');
                    
                    // Remove error message if it exists
                    const errorElement = field.nextElementSibling;
                    if (errorElement && errorElement.classList.contains('error-message')) {
                        errorElement.textContent = '';
                    }
                }
            });
            
            // If form is invalid, prevent submission
            if (!isValid) {
                event.preventDefault();
            }
        });
    });
    
    // Password strength indicator
    const passwordFields = document.querySelectorAll('input[type="password"][name="password"]');
    passwordFields.forEach(field => {
        // Create strength indicator
        const strengthIndicator = document.createElement('div');
        strengthIndicator.classList.add('password-strength');
        strengthIndicator.innerHTML = `
            <div class="strength-meter">
                <div class="strength-meter-fill"></div>
            </div>
            <div class="strength-text"></div>
        `;
        
        // Insert after password field
        field.parentNode.insertBefore(strengthIndicator, field.nextSibling);
        
        const strengthMeterFill = strengthIndicator.querySelector('.strength-meter-fill');
        const strengthText = strengthIndicator.querySelector('.strength-text');
        
        // Check password strength
        field.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            let feedback = '';
            
            if (password.length >= 8) strength += 1;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength += 1;
            if (password.match(/\d/)) strength += 1;
            if (password.match(/[^a-zA-Z\d]/)) strength += 1;
            
            // Update strength meter
            switch (strength) {
                case 0:
                    strengthMeterFill.style.width = '0%';
                    strengthMeterFill.style.backgroundColor = '#dc3545';
                    feedback = 'Very weak';
                    break;
                case 1:
                    strengthMeterFill.style.width = '25%';
                    strengthMeterFill.style.backgroundColor = '#dc3545';
                    feedback = 'Weak';
                    break;
                case 2:
                    strengthMeterFill.style.width = '50%';
                    strengthMeterFill.style.backgroundColor = '#ffc107';
                    feedback = 'Medium';
                    break;
                case 3:
                    strengthMeterFill.style.width = '75%';
                    strengthMeterFill.style.backgroundColor = '#28a745';
                    feedback = 'Strong';
                    break;
                case 4:
                    strengthMeterFill.style.width = '100%';
                    strengthMeterFill.style.backgroundColor = '#28a745';
                    feedback = 'Very strong';
                    break;
            }
            
            strengthText.textContent = feedback;
        });
    });
    
    // Confirm password validation
    const confirmPasswordFields = document.querySelectorAll('input[name="confirm_password"]');
    confirmPasswordFields.forEach(field => {
        field.addEventListener('input', function() {
            const password = document.querySelector('input[name="password"]').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.classList.add('is-invalid');
                
                // Add error message if it doesn't exist
                let errorElement = field.nextElementSibling;
                if (!errorElement || !errorElement.classList.contains('error-message')) {
                    errorElement = document.createElement('span');
                    errorElement.classList.add('error-message');
                    errorElement.style.color = 'red';
                    errorElement.style.fontSize = '14px';
                    field.parentNode.insertBefore(errorElement, field.nextSibling);
                }
                
                errorElement.textContent = 'Passwords do not match';
            } else {
                this.classList.remove('is-invalid');
                
                // Remove error message if it exists
                const errorElement = field.nextElementSibling;
                if (errorElement && errorElement.classList.contains('error-message')) {
                    errorElement.textContent = '';
                }
            }
        });
    });
    
    // Add smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

