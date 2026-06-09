function setContactButtonState(enabled, message) {
    const button = document.getElementById('contact-submit-button');
    const status = document.getElementById('turnstile-status');

    if (button) {
        button.disabled = !enabled;

        button.classList.toggle('opacity-50', !enabled);
        button.classList.toggle('cursor-not-allowed', !enabled);
        button.classList.toggle('hover:bg-white', enabled);
        button.classList.toggle('hover:text-slate-950', enabled);
    }

    if (status) {
        status.textContent = message;
    }
}

window.onTurnstileSuccess = function () {
    setContactButtonState(true, 'Security check complete.');
};

window.onTurnstileExpired = function () {
    setContactButtonState(false, 'Security check expired. Please refresh or try again.');
};

window.onTurnstileError = function () {
    setContactButtonState(false, 'Security check failed to load. Please refresh the page.');
};

document.addEventListener('DOMContentLoaded', () => {
    const button = document.getElementById('contact-submit-button');
    const turnstileEnabled = document.body.dataset.turnstileEnabled === 'true';

    if (button && turnstileEnabled) {
        setContactButtonState(false, 'Security check loading...');
    }
});