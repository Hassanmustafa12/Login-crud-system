// ==========================================================
// script.js — small UX enhancements
// (Delete confirmations are handled inline with onclick="confirm(...)"
//  in the PHP files — that's plain JS too, just written inline.)
// ==========================================================

document.addEventListener("DOMContentLoaded", function () {
    // Automatically fade out success/error alerts after 4 seconds
    var alerts = document.querySelectorAll(".alert");
    alerts.forEach(function (alertBox) {
        setTimeout(function () {
            alertBox.style.transition = "opacity 0.5s ease";
            alertBox.style.opacity = "0";
            setTimeout(function () {
                alertBox.remove();
            }, 500);
        }, 4000);
    });
});
