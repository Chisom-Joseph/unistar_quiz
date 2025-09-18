// js/script.js
// Progress bar: during payment/quiz
function showProgress() {
  /* ... */
}

// Tooltips
document.querySelectorAll("[data-tooltip]").forEach((el) => {
  el.addEventListener("mouseover", () => {
    /* show tooltip */
  });
});

// Timer for quiz
if (timer > 0) {
  let countdown = setInterval(() => {
    /* update, auto-submit on 0 */
  }, 1000);
}

// Disable back button
history.pushState(null, null, location.href);
window.onpopstate = () => history.go(1);

// Onboarding tour (popups)
if (first_login) {
  /* show steps */
}

// Keyboard navigation
document.addEventListener("keydown", (e) => {
  /* next question */
});
