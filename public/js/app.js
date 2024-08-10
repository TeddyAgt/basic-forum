// Éléments du DOM
const mobileNavigationToggler = document.querySelector(
  ".mobile-navigation-toggler"
);
const navigationMenu = document.querySelector("#main-navigation");

// Variables globales
isMenuVisible = false;

// Écouteurs d'événements
mobileNavigationToggler.addEventListener("click", () => {
  isMenuVisible = !isMenuVisible;
  toggleNavigationMenu();
});

function toggleNavigationMenu() {
  if (isMenuVisible) {
    navigationMenu.classList.add("main-navigation--mobile-active");
    mobileNavigationToggler.ariaLabel = "Fermer le menu de navigation";
    mobileNavigationToggler.ariaExpanded = "true";
    mobileNavigationToggler.children[0].classList =
      "fa-solid fa-xmark mobile-navigation__icon";
  } else {
    navigationMenu.classList.remove("main-navigation--mobile-active");
    mobileNavigationToggler.ariaLabel = "Ouvrir le menu de navigation";
    mobileNavigationToggler.ariaExpanded = "false";
    mobileNavigationToggler.children[0].classList =
      "fa-solid fa-bars mobile-navigation__icon";
  }
}
