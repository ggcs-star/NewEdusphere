document.addEventListener('DOMContentLoaded', () => {

    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');

    if (passwordInput && togglePassword) {

        togglePassword.addEventListener('click', () => {

            const isPassword =
                passwordInput.getAttribute('type') === 'password';

            passwordInput.setAttribute(
                'type',
                isPassword ? 'text' : 'password'
            );

            togglePassword.textContent =
                isPassword ? '🙈' : '👁';

            togglePassword.setAttribute(
                'aria-label',
                isPassword
                    ? 'Hide password'
                    : 'Show password'
            );
        });
    }


    const loginForm = document.getElementById('loginForm');
    const loginButton = document.getElementById('loginButton');

    if (loginForm && loginButton) {

        loginForm.addEventListener('submit', () => {

            loginButton.disabled = true;

            const buttonText =
                loginButton.querySelector('.button-text');

            const buttonArrow =
                loginButton.querySelector('.button-arrow');

            const buttonLoader =
                loginButton.querySelector('.button-loader');

            if (buttonText) {
                buttonText.hidden = true;
            }

            if (buttonArrow) {
                buttonArrow.hidden = true;
            }

            if (buttonLoader) {
                buttonLoader.hidden = false;
            }
        });
    }

});