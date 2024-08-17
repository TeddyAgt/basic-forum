// Global **************************************************
const ERROR_REQUIRED = "Ce champs est requis";
const ERROR_TITLE_TOO_SHORT = "Le titre doit faire 3 caractères minimum";
const toastContainer = document.querySelector(".toast-container");

function showResponseToast(message, background) {
  const toast = document.createElement("p");
  toast.classList.add("toast", `toast--${background}`);
  toast.textContent = message;
  toastContainer.appendChild(toast);

  setTimeout(() => {
    toast.remove();
  }, 2500);
}

// Ajout d'une catégorie **************************************************
const addCategoryForm = document.querySelector(".add-category__form");
addCategoryForm.addEventListener("submit", handleSubmitAddCategoryForm);

function handleSubmitAddCategoryForm(e) {
  e.preventDefault();
  const inputs = addCategoryForm.querySelectorAll("input");
  const inputErrors = addCategoryForm.querySelectorAll(".form-error");
  const categoryName = inputs[0].value;
  const categoryIcon = inputs[1].value;
  let isValid = true;

  if (!categoryName) {
    isValid = false;
    inputErrors[0].textContent = ERROR_REQUIRED;
  } else if (categoryName.length < 3) {
    isValid = false;
    inputErrors[0].textContent = ERROR_TITLE_TOO_SHORT;
  } else {
    inputErrors[0].textContent = "";
  }

  if (!categoryIcon) {
    isValid = false;
    inputErrors[1].textContent = ERROR_REQUIRED;
  } else {
    inputErrors[1].textContent = "";
  }

  if (isValid) {
    createNewCategory(categoryName, categoryIcon);
    addCategoryForm.reset();
  }
}

async function createNewCategory(categoryName, categoryIcon) {
  try {
    const response = await fetch("./actions/add-category.php", {
      method: "POST",
      headers: {
        "Content-type": "application/json",
      },
      body: JSON.stringify({
        name: categoryName,
        icon: categoryIcon,
      }),
    });

    if (response.ok) {
      showResponseToast("Catégorie créée avec succès", "green");
    }
  } catch (e) {
    console.log(e);
  }
}
