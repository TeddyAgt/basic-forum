// Éléments du DOM ******************************
latestsLists = document.querySelectorAll(".latests-list");
const seeLatestsBtns = document.querySelectorAll(".see-latests-btn");
const seeLatestsArticles = document.querySelectorAll(".latests-article");
const followBtn = document.querySelector("#header-follow-btn") ?? "";

// Constantes globales
const userId = new URLSearchParams(window.location.search).get("id") ?? "";

// Event Listeners
seeLatestsBtns.forEach((btn) =>
  btn.addEventListener("click", (e) => toggleLatests(e))
);

if (followBtn) {
  followBtn.addEventListener("click", handleClickFollowBtn);
}

// Fonctions ******************************
async function fetchActivities() {
  const response = await fetch(`./get-activities.php?id=${userId}`);

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

async function handleClickFollowBtn(e) {
  const response = await fetch("./actions/handle-followup.php", {
    method: "POST",
    headers: {
      "Content-type": "application/json",
    },
    body: JSON.stringify({
      isFollowing: e.target.dataset.following,
      followeeId: e.target.dataset.followeeId,
    }),
  });

  if (response.ok) {
    if (e.target.dataset.following === "true") {
      e.target.textContent = "Suivre";
      e.target.dataset.following = "false";
    } else {
      e.target.textContent = "Suivi(e)";
      e.target.dataset.following = "true";
    }
  }
}

// Récupération des follow ups **************************************************
const followUpsOverlay = document.querySelector(".follow-ups__overlay");
const followUpsPopup = document.querySelector(".follow-ups__article");
const showFollowUpsBtns = [
  ...document.querySelectorAll(".show-follow-ups__btn"),
];
const toggleFollowUpsBtns = [
  ...document.querySelectorAll(".follow-ups__nav__btn"),
];
const followUpsLists = [...document.querySelectorAll(".follow-ups__list")];
const closePopupBtn = document.querySelector(".close-popup-btn");

let followingList = [];
let followedbyList = [];

showFollowUpsBtns.forEach((btn) =>
  btn.addEventListener("click", displayFollowUpList)
);
toggleFollowUpsBtns.forEach((btn) =>
  btn.addEventListener("click", displayFollowUpList)
);
followUpsPopup.addEventListener("click", (e) => e.stopPropagation());

async function displayFollowUpList(e) {
  const action = e.target.dataset.role;
  toggleyOverlay(true);

  if (action === "following") {
    followUpsLists[0].innerHTML = "";
    followUpsLists[0].classList.add("follow-ups__list--active");
    followUpsLists[1].classList.remove("follow-ups__list--active");
    toggleFollowUpsBtns[0].classList.add("follow-ups__nav__btn--active");
    toggleFollowUpsBtns[1].classList.remove("follow-ups__nav__btn--active");
    followingList = await getFollowUps("follower", userId);
    const listItems = createListItems(action, followingList);
    followUpsLists[0].append(...listItems);
  } else {
    followUpsLists[1].innerHTML = "";
    followUpsLists[1].classList.add("follow-ups__list--active");
    followUpsLists[0].classList.remove("follow-ups__list--active");
    toggleFollowUpsBtns[1].classList.add("follow-ups__nav__btn--active");
    toggleFollowUpsBtns[0].classList.remove("follow-ups__nav__btn--active");
    followedbyList = await getFollowUps("followee", userId);
    const listItems = createListItems(action, followedbyList);
    followUpsLists[1].append(...listItems);
  }
}

async function getFollowUps(action, userId) {
  try {
    const response = await fetch(
      `./actions/get-follow-ups.php?action=${action}&id=${userId}`
    );

    if (response.ok) {
      return (data = await response.json());
    }
  } catch (e) {
    console.log(e);
  }
}

function toggleyOverlay(show) {
  if (show) {
    followUpsOverlay.classList.add("follow-ups__overlay--active");
    followUpsOverlay.addEventListener("click", () => toggleyOverlay(false));
    closePopupBtn.addEventListener("click", () => toggleyOverlay(false));
  } else {
    followUpsOverlay.classList.remove("follow-ups__overlay--active");
    followUpsOverlay.removeEventListener("click", () => toggleyOverlay(false));
    closePopupBtn.removeEventListener("click", () => toggleyOverlay(false));
  }
}

function createListItems(action, list) {
  if (action === "following") {
    list = list.map((user) => {
      const li = document.createElement("li");
      li.classList.add("follow_ups__list-item");
      li.innerHTML = `
        <a href="./profile.php?id=${user.id}" class="item__link">
          <div class="item__profile-picture">
            <img src="${user.avatar}" alt="">
          </div>
          <span>${user.username}</span>
        </a>
        `;
      const btn = document.createElement("button");
      btn.classList.add("btn", "btn--follow", "btn--follow-true");
      btn.dataset.following = "true";
      btn.dataset.followeeId = user.id;
      btn.textContent = "Suivi(e)";
      btn.addEventListener("click", handleClickFollowBtn);
      li.appendChild(btn);
      return li;
    });
  } else {
    list = list.map((user) => {
      const li = document.createElement("li");
      li.classList.add("follow_ups__list-item");
      li.innerHTML = `
        <a href="./profile.php?id=${user.id}" class="item__link">
          <div class="item__profile-picture">
            <img src="${user.avatar}" alt="">
          </div>
          <span>${user.username}</span>
        </a>
      `;
      return li;
    });
  }
  return list;
}
