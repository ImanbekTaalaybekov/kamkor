/* Shows the PIN form only for a valid personal link. */
(() => {
  'use strict';

  const requiredMessage = 'Пожалуйста, обратитесь в ваш районный РОВД за доступом.';
  const ACCESS_KEY = 'kamkor_personal_access_link_v1';
  const SESSION_KEY = 'kamkor_access_link_session_v1';
  const suppliedAccess = new URLSearchParams(window.location.search).get('access') || '';
  let accessToken = suppliedAccess;
  let blocked = false;

  try {
    if (suppliedAccess) localStorage.setItem(ACCESS_KEY, suppliedAccess);
    if (!accessToken) accessToken = localStorage.getItem(ACCESS_KEY) || '';
  } catch (_) {}

  function hasAuthorizedSession() {
    try { return Boolean(localStorage.getItem(SESSION_KEY)); } catch (_) { return false; }
  }

  function clearSavedAccess() {
    try { localStorage.removeItem(ACCESS_KEY); } catch (_) {}
  }

  function findAuthPanel() {
    return document.querySelector('.auth-form-panel');
  }

  function blockAccess() {
    if (hasAuthorizedSession()) return true;

    blocked = true;
    const panel = findAuthPanel();
    if (!panel) return false;

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
    if (hasAuthorizedSession()) return;

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
        clearSavedAccess();
        observerUntilRendered();
      }
    } catch (_) {
      observerUntilRendered();
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    if (blocked) return;
    void validateAccessLink();
  }, { once: true });
})();
