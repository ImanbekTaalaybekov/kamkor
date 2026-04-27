import { useState } from "react";
import { confirmVerification } from "../auth/session";
import type { AuthMode, SessionData } from "../auth/auth.types";

type Props = {
  userId: number;
  mode: AuthMode;
  onAuthorized: (session: SessionData) => void;
  onBack: () => void;
};

function detectDevice() {
  const ua = navigator.userAgent || "";
  if (ua.includes("Android")) return "pwa-android";
  if (ua.includes("iPhone") || ua.includes("iPad")) return "pwa-ios";
  return "pwa-web";
}

export default function VerifySmsPage({ userId, mode, onAuthorized, onBack }: Props) {
  const [code, setCode] = useState("");
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    if (!code.trim()) {
      setError("Введите код подтверждения");
      return;
    }

    setLoading(true);
    setError("");

    try {
      const session = await confirmVerification({
        userId,
        code: code.trim(),
        device: detectDevice(),
      });
      onAuthorized(session);
    } catch (err) {
      setError(err instanceof Error ? err.message : "Не удалось подтвердить код");
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="auth-shell auth-mobile-shell verify-page-shell">
      <div className="auth-mobile-screen verify-screen">
        <div className="verify-card">
          <div className="verify-badge">ЖА</div>
          <h1 className="verify-title">Подтверждение SMS</h1>
          <p className="verify-subtitle">
            Введите код из SMS, чтобы завершить {mode === "login" ? "вход" : "регистрацию"}
          </p>

          {error ? <div className="error-box auth-error">{error}</div> : null}
          {loading ? <div className="status-green">Проверяем код...</div> : null}

          <form onSubmit={handleSubmit}>
            <label className="field verify-field">
              <span className="label verify-label">Код подтверждения</span>
              <input
                className="input verify-input"
                type="text"
                value={code}
                onChange={(e) => setCode(e.target.value)}
                placeholder="Введите код"
                autoComplete="one-time-code"
                inputMode="text"
              />
            </label>

            <button className="button primary verify-submit-button" type="submit" disabled={loading}>
              {loading ? "Подтверждение..." : "Подтвердить"}
            </button>

            <button className="button secondary verify-back-button" type="button" onClick={onBack}>
              Назад
            </button>
          </form>
        </div>
      </div>
    </div>
  );
}
