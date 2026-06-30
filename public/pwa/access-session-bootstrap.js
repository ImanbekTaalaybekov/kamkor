/* Keeps a personal access link available for the installed PWA. */
(() => {
  'use strict';

  const ACCESS_KEY = 'kamkor_personal_access_link_v1';
  const page = new URL(window.location.href);
  const supplied = page.searchParams.get('access');

  if (supplied) {
    try { localStorage.setItem(ACCESS_KEY, supplied); } catch (_) {}
    return;
  }

  try {
    const saved = localStorage.getItem(ACCESS_KEY);
    if (!saved) return;
    page.searchParams.set('access', saved);
    window.history.replaceState({}, document.title, page.pathname + '?' + page.searchParams.toString() + page.hash);
  } catch (_) {
    // The access gate will show the standard ROVD message when storage is unavailable.
  }
})();
