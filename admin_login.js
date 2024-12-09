document.addEventListener("DOMContentLoaded", function () {
    const errorMessage = document.getElementById('errorMessage');
    if (errorMessage) {
        setTimeout(() => {
            errorMessage.style.transition = "opacity 0.5s ease";
            errorMessage.style.opacity = "0";

            setTimeout(() => errorMessage.remove(), 500); // Wait for fade-out effect to complete
        }, 3000); // Wait for 3 seconds
    }
});
