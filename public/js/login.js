document.addEventListener('DOMContentLoaded', function () {
    let loginForm = document.getElementById('loginForm');
    let email = document.querySelector('#loginForm #email');
    let password = document.querySelector('#loginForm #password')
    let generalError = document.getElementById('generalError');

    if (loginForm !== null) {
        loginForm.addEventListener('submit', function(event) {
            let isValid = true;

            if (email.value.trim() === '') {
                isValid = false;
            } else if (!isValidEmail(email.value)) {
                errorMessage = 'Veuillez entrer une adresse e-mail valide.';
                isValid = false;
            }

            if (password.value.trim() === '') {
                isValid = false;
            }

            if (! isValid) {
            generalError.textContent = 'Invalid email address or/and password !';
            event.preventDefault();
            } else {
                generalError.textContent = '';
            }
        });
    }
});