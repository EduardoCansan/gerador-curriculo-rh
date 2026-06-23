function togglePassword() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('eye-icon');
    if (input.type === 'password') {
        input.type     = 'text';
        icon.className = 'fa-regular fa-eye-slash';
    } else {
        input.type     = 'password';
        icon.className = 'fa-regular fa-eye';
    }
}