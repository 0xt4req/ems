// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const toggleIcon = field.parentElement.querySelector('.toggle-icon');

    if (field.type === 'password') {
        field.type = 'text';
        toggleIcon.textContent = '🙈'; // Change icon to "hide"
    } else {
        field.type = 'password';
        toggleIcon.textContent = '👁️'; // Change icon to "show"
    }
}