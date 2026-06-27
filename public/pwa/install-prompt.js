/* PWA installation control for Calculator. */
(() => {
  'use strict';

  let deferredInstallPrompt = null;
  let installationFinished = isInstalled();
  let modal = null;

  const ios = /iPad|iPhone|iPod/.test(navigator.userAgent)
    || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

  function isInstalled() {
    return window.matchMedia('(display-mode: standalone)').matches
      || window.navigator.standalone === true;
  }

  function buttonMarkup() {
    if (installationFinished || isInstalled()) {
      return `
        <section class="pwa-install-card pwa-install-card--done" data-pwa-install-card>
          <div class="pwa-install-title">Приложение установлено</div>
          <div class="pwa-install-text">Calculator уже добавлен на главный экран этого устройства.</div>
        </section>`;
    }

    return `
      <section class="pwa-install-card" data-pwa-install-card>
        <div class="pwa-install-title">Установить приложение</div>
        <div class="pwa-install-text">Добавьте Calculator на главный экран, чтобы открывать его как обычное приложение.</div>
        <button class="button secondary pwa-install-button" type="button" data-pwa-install>
          Установить
        </button>
        <div class="pwa-install-hint" data-pwa-install-hint aria-live="polite"></div>
      </section>`;
  }

  function mountInstallControl() {
    document.querySelectorAll('.profile-page').forEach((profilePage) => {
      if (profilePage.querySelector('[data-pwa-install-card]')) return;

      const container = document.createElement('div');
      container.className = 'pwa-install-container';
      container.innerHTML = buttonMarkup();

      const logout = profilePage.querySelector('.logout-button');
      if (logout) {
        profilePage.insertBefore(container, logout);
      } else {
        profilePage.appendChild(container);
      }
    });
  }

  function refreshInstallControl() {
    document.querySelectorAll('.pwa-install-container').forEach((container) => {
      container.innerHTML = buttonMarkup();
    });
  }

  function setHint(text, isError = false) {
    const hint = document.querySelector('[data-pwa-install-hint]');
    if (!hint) return;
    hint.textContent = text;
    hint.classList.toggle('pwa-install-hint--error', isError);
  }

  function closeIosInstructions() {
    if (modal) {
      modal.remove();
      modal = null;
    }
  }

  function openIosInstructions() {
    closeIosInstructions();
    modal = document.createElement('div');
    modal.className = 'pwa-install-modal';
    modal.setAttribute('role', 'dialog');
    modal.setAttribute('aria-modal', 'true');
    modal.setAttribute('aria-label', 'Инструкция по установке приложения');
    modal.innerHTML = `
      <div class="pwa-install-modal__backdrop" data-pwa-install-close></div>
      <div class="pwa-install-modal__dialog">
        <button type="button" class="pwa-install-modal__close" data-pwa-install-close aria-label="Закрыть">×</button>
        <div class="pwa-install-modal__title">Установка Calculator</div>
        <ol class="pwa-install-modal__steps">
          <li>Нажмите кнопку <strong>«Поделиться»</strong> внизу браузера Safari.</li>
          <li>Выберите <strong>«На экран «Домой»»</strong>.</li>
          <li>Нажмите <strong>«Добавить»</strong>.</li>
        </ol>
      </div>`;
    document.body.appendChild(modal);
  }

  function openBrowserInstructions() {
    setHint('Откройте меню браузера и выберите «Установить приложение» или «Добавить на главный экран».');
  }

  async function install() {
    if (installationFinished || isInstalled()) return;

    if (deferredInstallPrompt) {
      const promptEvent = deferredInstallPrompt;
      deferredInstallPrompt = null;
      promptEvent.prompt();

      try {
        const choice = await promptEvent.userChoice;
        if (choice && choice.outcome === 'accepted') {
          setHint('Установка подтверждена.');
        } else {
          setHint('Установка отменена.');
        }
      } catch (_) {
        setHint('Не удалось открыть окно установки. Откройте меню браузера и выберите установку приложения.', true);
      }
      return;
    }

    if (ios) {
      openIosInstructions();
      return;
    }

    openBrowserInstructions();
  }

  window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();
    deferredInstallPrompt = event;
    refreshInstallControl();
  });

  window.addEventListener('appinstalled', () => {
    installationFinished = true;
    deferredInstallPrompt = null;
    refreshInstallControl();
    closeIosInstructions();
  });

  document.addEventListener('click', (event) => {
    const installButton = event.target.closest('[data-pwa-install]');
    if (installButton) {
      event.preventDefault();
      void install();
      return;
    }

    if (event.target.closest('[data-pwa-install-close]')) {
      closeIosInstructions();
    }
  });

  const observer = new MutationObserver(() => mountInstallControl());
  observer.observe(document.documentElement, { childList: true, subtree: true });

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mountInstallControl, { once: true });
  } else {
    mountInstallControl();
  }
})();
