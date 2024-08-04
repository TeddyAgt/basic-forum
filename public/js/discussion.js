// Éléments du DOM ******************************
const likeMessageBtns = [...document.querySelectorAll(".like-btn")];
const overlay = document.querySelector(".overlay");
const deleteMessageBtns = document.querySelectorAll(".body__delete-btn");
const deleteMessageForm = document.querySelector(
  ".delete-message-confirmation-popup"
);
const cancelDeleteMessageBtn = document.querySelector(
  "#cancel-delete-message-btn"
);
const deleteMessageConfirmationModale = document.querySelector(
  ".delete-message-confirmation-popup"
);
const deleteMessageError = document.querySelector(
  "#delete-message-popup-error"
);

// Constantes et variables globales ******************************
const quizId = new URLSearchParams(window.location.search).get("id");

// Event listeners ******************************
likeMessageBtns.forEach((b) => b.addEventListener("click", handleClickLikeBtn));
deleteMessageBtns.forEach((b) =>
  b.addEventListener("click", showDeleteMessageModale)
);
deleteMessageConfirmationModale.addEventListener("click", (e) =>
  e.stopPropagation()
);

// Fonctions ******************************
async function handleClickLikeBtn(e) {
  try {
    const response = await fetch("./handle-like.php", {
      method: "POST",
      headers: {
        "Content-type": "application/json",
      },
      body: JSON.stringify({ messageId: e.target.dataset.message }),
    });

    if (response.ok) {
      const data = await response.json();
      const nbrOfLike = data.count;
      [...document.querySelectorAll(".nbr-of-likes")][
        likeMessageBtns.indexOf(e.target)
      ].textContent = nbrOfLike;
      console.log(likeMessageBtns.indexOf(e.target));
    }
  } catch (error) {
    console.log(error);
  }
}

function showDeleteMessageModale(e) {
  overlay.classList.add("active");
  deleteMessageForm.action = "./delete-message.php";
  document.querySelector("#message-id").value = e.target.dataset.message;
  overlay.addEventListener("click", hideDeleteMessageModale);
  cancelDeleteMessageBtn.addEventListener("click", hideDeleteMessageModale);
  deleteMessageForm.addEventListener("submit", submitDeleteMessage);
}

function hideDeleteMessageModale(e) {
  overlay.classList.remove("active");
  deleteMessageForm.action = "";
  document.querySelector("#message-id").value = "";
  overlay.removeEventListener("click", hideDeleteMessageModale);
  cancelDeleteMessageBtn.removeEventListener("click", hideDeleteMessageModale);
  deleteMessageForm.removeEventListener("submit", submitDeleteMessage);
}

async function submitDeleteMessage(e) {
  // if (!e.target[2].value) {
  //   deleteMessageError.textContent =
  //     "Entrez votre mot de passe pour confirmer la suppression";
  //   e.preventDefault();
  // } else {
  // }
}
