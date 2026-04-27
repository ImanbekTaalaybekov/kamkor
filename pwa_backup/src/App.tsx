import { useEffect, useState } from "react";
import { restoreSession } from "./auth/session";
import type { AuthMode, SessionData } from "./auth/auth.types";
import LoginPage from "./pages/LoginPage";
import RegisterPage from "./pages/RegisterPage";
import VerifySmsPage from "./pages/VerifySmsPage";
import MainMenuPage from "./pages/app/MainMenuPage";

type Screen = "login" | "register" | "verify" | "home";

export default function App() {
  const [booting, setBooting] = useState(true);
  const [screen, setScreen] = useState<Screen>("login");
  const [authMode, setAuthMode] = useState<AuthMode>("login");
  const [verifyUserId, setVerifyUserId] = useState<number | null>(null);
  const [session, setSession] = useState<SessionData | null>(null);

  useEffect(() => {
    const run = async () => {
      const result = await restoreSession();

      if (result.status === "authorized") {
        setSession(result.session);
        setScreen("home");
      } else if (result.status === "pending") {
        setVerifyUserId(result.userId);
        setAuthMode(result.mode);
        setScreen("verify");
      } else {
        setScreen("login");
      }

      window.setTimeout(() => setBooting(false), 800);
    };

    void run();
  }, []);

  if (booting) {
    return (
      <div className="splash-screen">
        <div className="splash-logo">⛨</div>
        <div className="splash-title">KAMKOR</div>
      </div>
    );
  }

  if (screen === "login") {
    return <LoginPage onSwitchToRegister={() => setScreen("register")} onNeedVerification={(userId) => {
      setVerifyUserId(userId);
      setAuthMode("login");
      setScreen("verify");
    }} />;
  }

  if (screen === "register") {
    return <RegisterPage onSwitchToLogin={() => setScreen("login")} onNeedVerification={(userId) => {
      setVerifyUserId(userId);
      setAuthMode("register");
      setScreen("verify");
    }} />;
  }

  if (screen === "verify" && verifyUserId) {
    return <VerifySmsPage userId={verifyUserId} mode={authMode} onBack={() => setScreen(authMode === "login" ? "login" : "register")} onAuthorized={(newSession) => {
      setSession(newSession);
      setScreen("home");
    }} />;
  }

  return <MainMenuPage session={session} onLoggedOut={() => {
    setSession(null);
    setVerifyUserId(null);
    setScreen("login");
  }} />;
}
