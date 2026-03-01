import ApiHelper from '@js/helpers/ApiHelper';

const form = document.querySelector('#auth-register-form');

form.addEventListener('submit', async function (e) {
    e.preventDefault();

    const email = form.querySelector('.email').value.trim();
    const password = form.querySelector('.password').value;
    const csrf_token = form.querySelector('.csrf_token').value;

    // Validation côté front
    const emailError = validateEmail(email);
    const passwordError = validatePassword(password);


    if (emailError) {
        displayFormError(emailError);
        return;
    }
    if (passwordError) {
        displayFormError(passwordError);
        return;
    }

    // Envoi JSON
    const response = await ApiHelper.fetch('/user/registerJson', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            email,
            password,
            csrf_token
        })
    });

    if (response.success) {
        displayFormSuccess(response.message ?? "Inscription réussie.");
        setTimeout(() => {
            window.location.href = '/auth/login';
        }, 5000); // 1000ms = 1 secondes
    } else {
        displayFormError(response.message ?? "Erreur inconnue.");
    }
});

form.addEventListener('input', () => {
    const errorDiv = document.querySelector('#form-error');
    if (errorDiv.style.display === 'block') {
        errorDiv.style.display = 'none';
    }
});

function validateEmail(email) {
    if (!email) return "L'email ne peut pas être vide.";
    if (email.length > 30) return "L'email est trop long (max 30 caractères).";
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;// Regex simple pour vérifier le format
    if (!emailPattern.test(email)) return "Format d'email invalide.";

    return null; // ok
}

function validatePassword(password) {
    if (!password) return "Le mot de passe ne peut pas être vide.";
    if (password.length < 8) return "Le mot de passe doit contenir au moins 8 caractères.";
    if (password.length > 20) return "Le mot de passe ne peut pas dépasser 20 caractères.";
    const passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]+$/; // Vérifie au moins une lettre et un chiffre
    if (!passwordPattern.test(password)) return "Le mot de passe doit contenir au moins une lettre et un chiffre.";

    return null; // ok
}

function displayFormError(message) {
    const errorDiv = document.querySelector('#form-error');
    errorDiv.textContent = message;  // injecte le message
    errorDiv.style.display = 'block'; // rend visible
}

function displayFormSuccess(message) {
    const successDiv = document.querySelector('#form-success');
    successDiv.textContent = message;  // injecte le message
    successDiv.style.display = 'block'; // rend visible
}
