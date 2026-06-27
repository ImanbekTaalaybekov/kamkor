/* Gate the PWA PIN field behind a valid personal access link. */
(() => {
  'use strict';

  const requiredMessage = 'Пожалуйста, обратитесь в ваш районный РОВД за доступом.';
  const accessToken = new URLSearchParams(window.location.search).get('access') || '';
  let blocked = false;

  function findAuthPanel() {
    return document.querySelector('.auth-form-panel');
  }

  function blockAccess() {
    blocked = true;
    const panel = findAuthPanel();
    if (!panel) return false;

    // Remove the initial legacy warning and the whole PIN form.
    panel.querySelectorAll('.auth-error, [data-kamkor-access-message]').forEach((node) => node.remove());
    const form = panel.querySelector('form');
    if (form) form.remove();

    const message = document.createElement('div');
    message.className = 'error-box auth-error';
    message.dataset.kamkorAccessMessage = '1';
    message.textContent = requiredMessage;
    panel.prepend(message);
    return true;
  }

  function observerUntilRendered() {
    if (blockAccess()) return;
    const observer = new MutationObserver(() => {
      if (blockAccess()) observer.disconnect();
    });
    observer.observe(document.documentElement, { childList: true, subtree: true });
  }

  async function validateAccessLink() {
    if (!accessToken) {
      observerUntilRendered();
      return;
    }

    try {
      const response = await fetch('/api/user/access-link/validate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ access_token: accessToken }),
      });

      if (!response.ok) {
        observerUntilRendered();
      }
    } catch (_) {
      // An unverified link must not show a PIN input.
      observerUntilRendered();
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    if (blocked) return;
    void validateAccessLink();
  }, { once: true });
})();
