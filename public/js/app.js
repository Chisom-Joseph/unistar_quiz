// Client validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    form.addEventListener('submit', (e) => {
        let valid = true;
        // Check email, password, etc.
        const email = form.querySelector('[name="email"]').value;
        if (!email.match(/^\S+@\S+\.\S+$/)) { valid = false; alert('Invalid email'); }
        const pass = form.querySelector('[name="password"]').value;
        if (pass.length < 8 || !/[A-Z]/.test(pass) || !/\d/.test(pass) || !/[@$!%*?&]/.test(pass)) {
            valid = false; alert('Weak password');
        }
        if (!valid) e.preventDefault();
    });
}

// Quiz timer
function startTimer(minutes) {
    let time = minutes * 60;
    const bar = document.querySelector('.progress-bar');
    const interval = setInterval(() => {
        time--;
        const width = (time / (minutes * 60)) * 100;
        bar.style.width = width + '%';
        if (time <= 0) {
            clearInterval(interval);
            // Auto-submit form
            document.getElementById('quiz-form').submit();
        }
    }, 1000);
}

// Shuffle options (if flag, but server does, JS for display)
function shuffleOptions(options) {
    // Fisher-Yates
    for (let i = options.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [options[i], options[j]] = [options[j], options[i]];
    }
    return options;
}

// Disable back button
window.history.pushState(null, null, window.location.href);
window.onpopstate = function () {
    window.history.pushState(null, null, window.location.href);
};

// Onboarding tour (simple modals)
function startTour() {
    // Show popups for new users
    const steps = ['Welcome!', 'Click here for dashboard', ...];
    // Implement sequential alerts or divs
}

// Keyboard nav for quiz
document.addEventListener('keydown', (e) => {
    if (e.key >= '1' && e.key <= '9') {
        // Select option 1-9
        document.querySelector(`input[value="${e.key-1}"]`).checked = true;
    }
});

// Toast
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// Call on load: validateForm('register-form'); if (flag timer) startTimer(quizzes.timer_minutes);