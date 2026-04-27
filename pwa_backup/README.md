# KAMKOR PWA redesigned starter

Внутри:
- вход
- регистрация
- подтверждение SMS
- хранение и восстановление сессии
- главная страница ближе к макету
- нижнее меню
- кнопка SOS с отсчётом 3 секунды
- отмена отправки
- отправка на POST /sos
- затычка под аудио

## Главное

В `src/pages/app/DashboardPage.tsx` есть константы:

```ts
const USE_BACKEND_SOS_AVAILABILITY = true;
const FORCE_SOS_AVAILABLE = true;
```

### Как работает
- если `USE_BACKEND_SOS_AVAILABILITY = true`, берётся `sos_button_available` с бэка
- если `USE_BACKEND_SOS_AVAILABILITY = false`, используется `FORCE_SOS_AVAILABLE`

## Запуск

```bash
npm install
npm run dev
```

## Перед запуском

Открой `src/api/client.ts` и замени:

```ts
const API_BASE = "https://your-domain.com/api";
```

на реальный адрес Laravel API.
