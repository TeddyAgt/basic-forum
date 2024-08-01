// Éléments du DOM ******************************
const likeBtns = [...document.querySelectorAll(".like-btn")];

const quizId = new URLSearchParams(window.location.search).get("id");

// Event listeners ******************************
likeBtns.forEach((b) => b.addEventListener("click", handleClickLikeBtn));

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
        likeBtns.indexOf(e.target)
      ].textContent = nbrOfLike;
      console.log(likeBtns.indexOf(e.target));
    }
  } catch (error) {
    console.log(error);
  }
}
