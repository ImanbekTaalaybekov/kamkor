import { useState } from "react";
import AuthLayout from "../components/AuthLayout";
import Input from "../components/Input";
import { startLogin } from "../auth/session";

type Props = {
  onNeedVerification: (userId: number) => void;
  onSwitchToRegister: () => void;
};

function detectDevice() {
  const ua = navigator.userAgent || "";
  if (ua.includes("Android")) return "pwa-android";
  if (ua.includes("iPhone") || ua.includes("iPad")) return "pwa-ios";
  return "pwa-web";
}

export default function LoginPage({ onNeedVerification, onSwitchToRegister }: Props) {
  const [pin, setPin] = useState("");
  const [phoneNumber, setPhoneNumber] = useState("");
  const [password, setPassword] = useState("");
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);
    setError("");

    try {
      const result = await startLogin({
        pin,
        phone_number: phoneNumber,
        password,
        device: detectDevice(),
      });
      onNeedVerification(result.user_id);
    } catch (err) {
      setError(err instanceof Error ? err.message : "Не удалось войти");
    } finally {
      setLoading(false);
    }
  }

  return (
    <AuthLayout
      title="Вход"
      footer={
        <>
          <button className="auth-link" type="button">Забыли пароль?</button>
          <button className="auth-link auth-link-strong" type="button" onClick={onSwitchToRegister}>
            Регистрация
          </button>
        </>
      }
    >
      {error ? <div className="error-box auth-error">{error}</div> : null}

      <form onSubmit={handleSubmit}>
        <Input value={pin} onChange={setPin} placeholder="ИНН" />
        <Input value={phoneNumber} onChange={setPhoneNumber} placeholder="Номер телефона" />
        <Input value={password} onChange={setPassword} placeholder="Пароль" type="password" />

        <button className="button primary flat-button" type="submit" disabled={loading}>
          {loading ? "ВХОД..." : "ДАЛЕЕ"}
        </button>
      </form>
    </AuthLayout>
  );
}
