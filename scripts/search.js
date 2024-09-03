document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("searchInput");
  const dogWalkerList = document.getElementById("dogWalkerList");

  searchInput.addEventListener("keyup", function () {
    const searchTerm = searchInput.value.toLowerCase();
    const dogWalkers = dogWalkerList.getElementsByClassName("card");

    Array.from(dogWalkers).forEach(function (dogWalker) {
      const name = dogWalker
        .querySelector(".card-title")
        .textContent.toLowerCase();
      if (name.includes(searchTerm)) {
        dogWalker.style.display = "block";
      } else {
        dogWalker.style.display = "none";
      }
    });
  });
});
