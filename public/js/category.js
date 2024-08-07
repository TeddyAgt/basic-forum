// // Éléments du DOM
// const browseCategoriesSection = document.querySelector(
//   ".browse-categories-section"
// );

// // Constantes et variables globales ******************************
// const categoryId = new URLSearchParams(window.location.search).get("id") ?? "";

// // Event Listeners

// // Fonctions
// async function fetchCategory() {
//   try {
//     const response = await fetch(
//       `./actions/get-categories.php${categoryId ? "?id=" + categoryId : ""}`
//     );

//     if (response.ok) {
//       const categories = await response.json();
//       console.log(categories);
//       categories.forEach((category) => displayCategory(category));
//     }
//   } catch (e) {
//     console.log(e);
//   }
// }
// fetchCategory();
