import { useState } from "react";
import AuthLayout from "../components/AuthLayout";
import Input from "../components/Input";
import { startRegister } from "../auth/session";

type Props = {
  onNeedVerification: (userId: number) => void;
  onSwitchToLogin: () => void;
};

function detectDevice() {
  const ua = navigator.userAgent || "";
  if (ua.includes("Android")) return "pwa-android";
  if (ua.includes("iPhone") || ua.includes("iPad")) return "pwa-ios";
  return "pwa-web";
}

export default function RegisterPage({ onNeedVerification, onSwitchToLogin }: Props) {
  const [name, setName] = useState("");
  const [surname, setSurname] = useState("");
  const [pin, setPin] = useState("");
  const [phoneNumber, setPhoneNumber] = useState("");
  const [password, setPassword] = useState("");
  const [consent, setConsent] = useState(true);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    if (!consent) {
      setError("Подтвердите согласие на обработку персональных данных");
      return;
    }

    setLoading(true);
    setError("");

    try {
      const result = await startRegister({
        name,
        surname,
        pin,
        phone_number: phoneNumber,
        password,
        device: detectDevice(),
      });
      onNeedVerification(result.user_id);
    } catch (err) {
      setError(err instanceof Error ? err.message : "Не удалось зарегистрироваться");
    } finally {
      setLoading(false);
    }
  }

  return (
    <AuthLayout
      title="Регистрация"
      footer={
        <button className="auth-link auth-link-strong" type="button" onClick={onSwitchToLogin}>
          Есть аккаунт?
        </button>
      }
    >
      {error ? <div className="error-box auth-error">{error}</div> : null}

      <form onSubmit={handleSubmit}>
        <Input value={name} onChange={setName} placeholder="Имя" />
        <Input value={surname} onChange={setSurname} placeholder="Фамилия" />
        <Input value={pin} onChange={setPin} placeholder="ИНН" />
        <Input value={phoneNumber} onChange={setPhoneNumber} placeholder="Номер телефона" error={!phoneNumber && false} />
        <Input value={password} onChange={setPassword} placeholder="Пароль" type="password" />

        <label className="consent-line">
          <input type="checkbox" checked={consent} onChange={() => setConsent((prev) => !prev)} />
          <span>
            Даю согласие на <span className="auth-link-inline">обработку персональных данных</span>
          </span>
        </label>

        <button className="button primary flat-button" type="submit" disabled={loading}>
          {loading ? "СОЗДАНИЕ..." : "ДАЛЕЕ"}
        </button>
      </form>
    </AuthLayout>
  );
}
