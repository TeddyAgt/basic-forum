// Éléments du DOM ******************************
latestsLists = document.querySelectorAll(".latests-list");
const seeLatestsBtns = document.querySelectorAll(".see-latests-btn");
const seeLatestsArticles = document.querySelectorAll(".latests-article");

// Event Listeners
seeLatestsBtns.forEach((btn) =>
  btn.addEventListener("click", (e) => toggleLatests(e))
);

// Fonctions ******************************
async function fetchActivities() {
  const response = await fetch("./get-activities.php");

  if (response.ok) {
    const activities = await response.json();

    displayUserActivities(activities);
  }
}
fetchActivities();

function displayUserActivities(activities) {
  // discussions
  if (!activities.discussions.length) {
    latestsLists[0].innerHTML =
      "<li>Vous n'avez pas encore créé de discussion.</li>";
  } else {
    activities.discussions.forEach((a) => {
      latestsLists[0].innerHTML += `
        <li class="latests-list__item index-list__item">
          <h3 class="latests-list__item-title">
            <a href="/discussion.php?id=${a.id}&page=1&limit=10"><i class="${
        a.category_icon
      }" aria-hidden="true"></i>${a.title}</a>
          </h3>
          <div>
            <p>${a.creation_date}</p>
            <p>${a.nb_responses} ${
        a.nb_responses > 1 ? "réponses" : "réponse"
      }</p>
          </div>
        </li>
      `;
    });
  }

  // messages
  if (!activities.messages.length) {
    latestsLists[1].innerHTML =
      "<li>Vous n'avez pas encore posté de message.</li>";
  } else {
    activities.messages.forEach((m) => {
      latestsLists[1].innerHTML += `
        <li class="latests-list__item index-list__item">
          <h3 class="latests-list__item-title">
            <a href="/discussion.php?id=${m.discussion_id}&page=1&limit=10">${m.discussion_title}</a>
          </h3>
          <div class="">
            <p>${m.text}</p>
            <p>${m.creation_date}</p>
          </div>
        </li>
      `;
    });
  }
}

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
