function togglePasswordVisibility(inputId, toggleIcon) {
  const inputElement = document.getElementById(inputId);
  const icon = toggleIcon.querySelector('i');

  if (inputElement.type === "password") {
    inputElement.type = "text";
    icon.classList.remove('fa-eye');
    icon.classList.add('fa-eye-slash'); // Change icon to "Hide"
  } else {
    inputElement.type = "password";
    icon.classList.remove('fa-eye-slash');
    icon.classList.add('fa-eye'); // Change icon back to "Show"
  }
}