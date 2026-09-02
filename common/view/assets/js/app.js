document.addEventListener("DOMContentLoaded", function () {
    var toggleButtons = document.querySelectorAll("[data-toggle-password]");

    toggleButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            var passwordId = button.getAttribute("data-toggle-password");
            var passwordInput = document.getElementById(passwordId);

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                button.textContent = "Hide";
            } else {
                passwordInput.type = "password";
                button.textContent = "Show";
            }
        });
    });
});
