document.querySelector("#is_walker").addEventListener("change", function () {
  document.getElementById("walker_info").style.display = this.checked
    ? "block"
    : "none";
});

document
  .getElementById("registrationForm")
  .addEventListener("submit", function (event) {
    event.preventDefault();
    const formData = new FormData(this);

    fetch("register.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.text())
      .then((data) => {
        console.log(data); // Ellenőrizzük, hogy milyen adatot kapunk vissza
        if (data.includes("Sikeres regisztráció!")) {
          this.reset();
          document.getElementById("walker_info").style.display = "none";
          let successModal = new bootstrap.Modal(
            document.getElementById("successModal")
          );
          successModal.show();
        } else {
          // Hibakezelés - az üzenetet megjeleníthetjük az oldal egy kijelölt helyén
          document.getElementById(
            "message"
          ).innerHTML = `<div class="alert alert-danger">${data}</div>`;
        }
      })
      .catch((error) => console.error("Error:", error));
  });
