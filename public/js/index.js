// Éléments du DOM
const seeLatestsBtns = document.querySelectorAll(".see-latests-btn");
const seeLatestsArticles = document.querySelectorAll(".latests-article");

// Event Listeners
seeLatestsBtns.forEach((btn) =>
  btn.addEventListener("click", (e) => toggleLatests(e))
);

function toggleLatests(e) {
  if (e.target.dataset.latests === "subjects") {
    seeLatestsArticles[0].classList.add("active");
    seeLatestsArticles[1].classList.remove("active");
  } else {
    seeLatestsArticles[0].classList.remove("active");
    seeLatestsArticles[1].classList.add("active");
  }
}
