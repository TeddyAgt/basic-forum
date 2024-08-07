// Éléments du DOM

// Constantes et variables globales ******************************
const categoryId = new URLSearchParams(window.location.search).get("id") ?? "";

// Event Listeners

// Fonctions
async function fetchCategory() {
  try {
    const response = await fetch(
      `./actions/get-categories.php${categoryId ? "?id=" + categoryId : ""}`
    );

    if (response.ok) {
      const category = await response.json();
      console.log(category);
    }
  } catch (e) {
    console.log(e);
  }
}
fetchCategory();
