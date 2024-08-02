// Éléments du DOM
const seeLatestsBtns = document.querySelectorAll(".see-latests-btn");
const seeLatestsArticles = document.querySelectorAll(".latests-article");

// Event Listeners
seeLatestsBtns.forEach((btn) =>
  btn.addEventListener("click", (e) => toggleLatests(e))
);

function toggleLatests(e) {
  if (e.target.dataset.latests === "discussions") {
    seeLatestsArticles[0].classList.add("active");
    seeLatestsBtns[0].classList.add("see-latests-btn--active");
    seeLatestsArticles[1].classList.remove("active");
    seeLatestsBtns[1].classList.remove("see-latests-btn--active");
  } else {
    seeLatestsArticles[0].classList.remove("active");
    seeLatestsBtns[0].classList.remove("see-latests-btn--active");
    seeLatestsArticles[1].classList.add("active");
    seeLatestsBtns[1].classList.add("see-latests-btn--active");
  }
}
