import { useEffect, useRef, useState } from "react";
import { sendSos, uploadSosAudioStub } from "../../api/sos";
import type { SessionData } from "../../auth/auth.types";

type Props = {
  session: SessionData | null;
  onOpenInstructions: () => void;
  onOpenHistory: () => void;
  onOpenPsychologicalHelp: () => void;
  onOpenCenters: () => void;
};

type SosUiState = "idle" | "countdown" | "sending" | "success" | "error";

function getInitials(name?: string, surname?: string) {
  const first = (name || "").trim().charAt(0);
  const second = (surname || "").trim().charAt(0);
  const value = `${first}${second}`.toUpperCase();
  return value || "--";
}

function getGeolocation(): Promise<string> {
  return new Promise((resolve, reject) => {
    if (!navigator.geolocation) {
      reject(new Error("Геолокация не поддерживается этим браузером"));
      return;
    }

    navigator.geolocation.getCurrentPosition(
      (position) => {
        resolve(`${position.coords.latitude},${position.coords.longitude}`);
      },
      () => {
        reject(new Error("Не удалось получить геолокацию"));
      },
      {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0,
      }
    );
  });
}

export default function DashboardPage({
  session,
  onOpenInstructions,
  onOpenHistory,
  onOpenPsychologicalHelp,
  onOpenCenters,
}: Props) {
  const [sosState, setSosState] = useState<SosUiState>("idle");
  const [countdown, setCountdown] = useState(3);
  const [statusMessage, setStatusMessage] = useState("");
  const [lastSosId, setLastSosId] = useState<number | null>(null);
  const timerRef = useRef<number | null>(null);
  const requestAbortRef = useRef<AbortController | null>(null);
  const sosEnabled = true;

  useEffect(() => {
    if (sosState !== "countdown") return;
    if (countdown <= 0) {
      void triggerSos();
      return;
    }

    timerRef.current = window.setTimeout(() => {
      setCountdown((prev) => prev - 1);
    }, 1000);

    return () => {
      if (timerRef.current) {
        window.clearTimeout(timerRef.current);
      }
    };
  }, [sosState, countdown]);

  useEffect(() => {
    return () => {
      if (timerRef.current) {
        window.clearTimeout(timerRef.current);
      }
      requestAbortRef.current?.abort();
    };
  }, []);

  function startCountdown() {
    if (!sosEnabled) return;
    setStatusMessage("");
    setLastSosId(null);
    setCountdown(3);
    setSosState("countdown");
  }

  function cancelSos() {
    if (timerRef.current) {
      window.clearTimeout(timerRef.current);
    }
    requestAbortRef.current?.abort();
    requestAbortRef.current = null;
    setCountdown(3);
    setSosState("idle");
    setStatusMessage("Отправка SOS отменена");
  }

  async function triggerSos() {
    setSosState("sending");
    setStatusMessage("Идет оповещение доверенных контактов");
    const controller = new AbortController();
    requestAbortRef.current = controller;

    try {
      const geolocation = await getGeolocation();
      const result = await sendSos(geolocation, { signal: controller.signal });
      setLastSosId(result.sos_id);
      await uploadSosAudioStub(result.sos_id, { signal: controller.signal });
      setSosState("success");
      setStatusMessage(result.message || "SOS-сигнал отправлен");
    } catch (err) {
      if (err instanceof DOMException && err.name === "AbortError") {
        setSosState("idle");
        setStatusMessage("Отправка SOS отменена");
        return;
      }
      setSosState("error");
      setStatusMessage(err instanceof Error ? err.message : "Не удалось отправить SOS");
    } finally {
      requestAbortRef.current = null;
    }
  }

  const showPrimaryButton = sosState === "idle" || sosState === "success" || sosState === "error";
  const buttonLabel = sosState === "countdown" ? `ОТМЕНА ${String(countdown).padStart(2, "0")}` : sosState === "sending" ? "ОТМЕНА" : "SOS";

  return (
    <div className="main-page">
      <div className="app-topbar">
        <div className="brand-badge">{getInitials(session?.user.name, session?.user.surname)}</div>
        <div className="profile-name">{session?.user.name || "Жаркынай"} {session?.user.surname || "Алматова"}</div>
        <div className="country-code">KG</div>
      </div>

      <div className="hero-zone">
        {showPrimaryButton ? (
          <>
            <button className={sosEnabled ? "sos-button" : "sos-button disabled"} type="button" onClick={startCountdown} disabled={!sosEnabled}>
              SOS
            </button>
            <div className="hero-caption">Отправьте сигнал о помощи своим доверенным контактам и запишите голосовое сообщение о случившемся</div>
          </>
        ) : (
          <>
            <button className="sos-button cancel-button-inside" type="button" onClick={cancelSos}>
              <span>{buttonLabel}</span>
            </button>
            <div className="countdown-text">{sosState === "countdown" ? "Начался обратный отсчет" : "Идет оповещение доверенных контактов"}</div>
          </>
        )}

        {statusMessage ? (
          <div className={sosState === "error" ? "status-red compact-status" : "status-green compact-status"}>
            {statusMessage}{lastSosId ? ` #${lastSosId}` : ""}
          </div>
        ) : null}
      </div>

      <button type="button" className="action-button" onClick={onOpenInstructions}>
        ИНСТРУКЦИИ В ЭКСТРЕННЫХ СИТУАЦИЯХ
      </button>

      <div className="feature-grid">
        <button type="button" className="feature-card" onClick={onOpenHistory}>
          <span>История SOS сигналов</span>
        </button>
        <button type="button" className="feature-card" onClick={onOpenPsychologicalHelp}>
          <span>Психологическая помощь</span>
        </button>
        <button type="button" className="feature-card wide" onClick={onOpenCenters}>
          <span>Кризисные центры</span>
        </button>
      </div>
    </div>
  );
}
