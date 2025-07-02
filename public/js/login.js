/*=============== SHOW HIDE PASSWORD LOGIN ===============*/
const togglePasswordVisibility = (inputId, iconId) => {
    const input = document.getElementById(inputId),
        iconEye = document.getElementById(iconId);

    iconEye.addEventListener('click', () => {
        // Toggle input type
        input.type = input.type === 'password' ? 'text' : 'password';

        // Toggle icon classes
        iconEye.classList.toggle('ri-eye-fill');
        iconEye.classList.toggle('ri-eye-off-fill');
    });
};

// Login password toggle
togglePasswordVisibility('password', 'loginPassword');

// Create account password toggle
togglePasswordVisibility('passwordCreate', 'loginPasswordCreate');

// Confirm password toggle
togglePasswordVisibility('confirmPasswordCreate', 'confirmPasswordToggle');

/*=============== SHOW HIDE LOGIN & CREATE ACCOUNT ===============*/
const loginAcessRegister = document.getElementById('loginAccessRegister'),
    buttonRegister = document.getElementById('loginButtonRegister'),
    buttonAccess = document.getElementById('loginButtonAccess');

buttonRegister.addEventListener('click', () => {
    loginAcessRegister.classList.add('active');
});

buttonAccess.addEventListener('click', () => {
    loginAcessRegister.classList.remove('active');
});
