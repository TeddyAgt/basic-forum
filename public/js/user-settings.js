// Éléments du DOM
const navLinks = [...document.querySelectorAll(".aside-navigation-link")];
const settingsContainers = [
  ...document.querySelectorAll(".account-settings-article"),
];

// Écouteurs d'événements
navLinks.forEach((link) => link.addEventListener("click", handleClickLink));

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
}
