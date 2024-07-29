// Éléments du DOM
const toggleShowPasswordBtn = document.querySelector("#show-password");
const passwordInputs = document.querySelectorAll("input[type='password']");

// Event listeners
toggleShowPasswordBtn.addEventListener("click", togglePasswordVisibility);

// Afficher / masquer le mot de passe
function togglePasswordVisibility() {
  if (passwordInputs[0].type === "password") {
    passwordInputs.forEach((input) => (input.type = "text"));
    toggleShowPasswordBtn.innerHTML =
      '<i class="fa-regular fa-eye-slash" aria-hidden="true"></i>';
    toggleShowPasswordBtn.ariaLabel = "Masquer le mot de passe";
  } else {
    passwordInputs.forEach((input) => (input.type = "password"));
    toggleShowPasswordBtn.innerHTML =
      '<i class="fa-regular fa-eye" aria-hidden="true"></i>';
    toggleShowPasswordBtn.ariaLabel = "Afficher le mot de passe";
  }
}
