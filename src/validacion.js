document.addEventListener('DOMContentLoaded', () => {

    const form = document.getElementById('contactForm');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const messageInput = document.getElementById('message');
    const privacyPolicyCheckbox = document.getElementById('privacyPolicy');
    const skipValidationCheckbox = document.getElementById('skipValidation');

    let errorContainer = document.createElement('div');
    errorContainer.id = 'formErrores'; 
    errorContainer.style.color = 'red';
    errorContainer.style.marginBottom = '10px';
    
    const submitButton = form.querySelector('button[type="submit"]');
    form.insertBefore(errorContainer, submitButton);


    form.addEventListener('submit', (event) => {

        if (skipValidationCheckbox.checked) {
            return; 
        }

        
        event.preventDefault(); 
        
        errorContainer.innerHTML = '';
        let errors = [];

        
        if (nameInput.value.trim() === '') {
            errors.push('El camp "Nom" és obligatori.');
        }

        if (emailInput.value.trim() === '') {
            errors.push('El camp "Correu electrònic" és obligatori.');
        } else if (!isValidEmail(emailInput.value)) {
            errors.push('El format del "Correu electrònic" no és vàlid.');
        }

        if (messageInput.value.trim() === '') {
            errors.push('El camp "Missatge" és obligatori.');
        }

        if (!privacyPolicyCheckbox.checked) {
            errors.push("Has d'acceptar la política de privacitat.");
        }

        if (errors.length > 0) {
            errorContainer.innerHTML = errors.join('<br>');
        } else {
            console.log('Formulario validado por JS y enviado.');
            form.submit(); 
        }
    });

    function isValidEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }
});