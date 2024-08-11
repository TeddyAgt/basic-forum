// Éléments du DOM
const navLinks = [...document.querySelectorAll(".aside-navigation-link")];
const settingsContainers = [
  ...document.querySelectorAll(".account-settings-article"),
];
const aboutTextarea = document.querySelector("#about");
const textareaCounter = document.querySelector(".textarea-counter");
textareaCounter.textContent = `${250 - aboutTextarea.value.length} restants`;
const deleteAccountBtn = document.querySelector("#delete-account-btn");
const deleteAccountContainer = document.querySelector(
  ".delete-account-container"
);
const mobileSettingsMenuToggler = document.querySelector(
  ".mobile-settings-menu-toggler"
);
const settingsMenu = document.querySelector(".account-settings__aside");

// Constantes et variables globales
let sec = new URLSearchParams(window.location.search).get("sec");
displayUI(navLinks[sec], settingsContainers[sec]);
let isSettingsMenuVisible = false;

// Écouteurs d'événements
navLinks.forEach((link) => link.addEventListener("click", handleClickLink));
aboutTextarea.addEventListener("input", handleInputTextarea);
deleteAccountBtn.addEventListener("click", handleClickDeleteBtn);
mobileSettingsMenuToggler.addEventListener("click", () => {
  toggleSettingsMenu();
});

// Fonctions
function handleClickLink(e) {
  const targetLink = e.target;
  const targetContainer = settingsContainers[navLinks.indexOf(targetLink)];
  navLinks.forEach((link) => {
    link.classList.remove("aside-navigation-link--active");
    link.ariaExpanded = "false";
  });
  targetLink.classList.add("aside-navigation-link--active");
  targetLink.ariaExpanded = "true";

  settingsContainers.forEach((container) => {
    container.classList.remove("account-settings-article--active");
    container.ariaHidden = "true";
  });
  targetContainer.classList.add("account-settings-article--active");
  targetContainer.ariaHidden = "false";
  toggleSettingsMenu();
}

function displayUI(targetLink, targetContainer) {
  navLinks.forEach((link) => {
    link.classList.remove("aside-navigation-link--active");
    link.ariaExpanded = "false";
  });
  targetLink.classList.add("aside-navigation-link--active");
  targetLink.ariaExpanded = "true";

  settingsContainers.forEach((container) => {
    container.classList.remove("account-settings-article--active");
    container.ariaHidden = "true";
  });
  targetContainer.classList.add("account-settings-article--active");
  targetContainer.ariaHidden = "false";
}

function handleInputTextarea() {
  textareaCounter.textContent = `${250 - aboutTextarea.value.length} restants`;
}

function handleClickDeleteBtn() {
  deleteAccountContainer.classList.add("delete-account-container--active");
}

function toggleSettingsMenu() {
  if (!isSettingsMenuVisible) {
    settingsMenu.classList.add("account-settings__aside--active");
    mobileSettingsMenuToggler.ariaLabel =
      "Fermer le menu de catégorie des paramètres";
    mobileSettingsMenuToggler.ariaExpanded = "true";
  } else {
    settingsMenu.classList.remove("account-settings__aside--active");
    mobileSettingsMenuToggler.ariaLabel =
      "Ouvrir le menu de catégorie des paramètres";
    mobileSettingsMenuToggler.ariaExpanded = "false";
  }
  isSettingsMenuVisible = !isSettingsMenuVisible;
}
