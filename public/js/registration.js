document.addEventListener('DOMContentLoaded', function() {
    let registrationForm = document.getElementById('registrationForm');

    if (registrationForm !== null) {
    registrationForm.addEventListener('submit', function(event) {
        let isValid = true;
        let email = document.querySelector('#registrationForm #email').value;
        let password = document.querySelector('#registrationForm #password').value;
        let confirmPassword = document.getElementById('confirm_password').value;

        // Reset error messages
        document.getElementById('emailError').textContent = '';
        document.getElementById('passwordError').textContent = '';
        document.getElementById('confirmPasswordError').textContent = '';

        // Validation de l'adresse e-mail
        const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        if (!emailPattern.test(email)) {
            document.getElementById('emailError').textContent = 'Please enter a valid email address.';
            isValid = false;
        }

        // Validation de la longueur du mot de passe
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$#!%*?&])[A-Za-z\d@$#!%*?&]{12,}$/;
        if (!passwordRegex.test(password)) {
            document.getElementById('passwordError').textContent = 'The password must be at least 12 characters long and include at least one lowercase letter, one uppercase letter, one number and one special character.';
            isValid = false;
        }

        // Comparaison entre le mot de passe et la confirmation du mot de passe
        if (password !== confirmPassword) {
            document.getElementById('confirmPasswordError').textContent = 'Passwords do not match.';
            isValid = false;
        }

        // Empêcher la soumission du formulaire si les données ne sont pas valides
        if (!isValid) {
            event.preventDefault();
        }
    });
    }
});
