import { useEffect, useState } from "react";
import { getTemplate, updateTemplate } from "../../api/template";

type Props = {
  onBack: () => void;
};

export default function TemplatePage({ onBack }: Props) {
  const [messageText, setMessageText] = useState("");
  const [geoSignature, setGeoSignature] = useState("");
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [status, setStatus] = useState("");
  const [error, setError] = useState("");

  useEffect(() => {
    void loadTemplate();
  }, []);

  async function loadTemplate() {
    setLoading(true);
    setError("");
    setStatus("");

    try {
      const data = await getTemplate();
      setMessageText(data.message_text || "");
      setGeoSignature(data.geo_signature || "");
    } catch (err) {
      setError(err instanceof Error ? err.message : "Не удалось загрузить шаблон");
    } finally {
      setLoading(false);
    }
  }

  async function handleSave(e: React.FormEvent) {
    e.preventDefault();
    setSaving(true);
    setError("");
    setStatus("");

    try {
      const result = await updateTemplate({
        message_text: messageText,
        geo_signature: geoSignature,
      });
      setStatus(result.message || "Шаблон обновлен");
    } catch (err) {
      setError(err instanceof Error ? err.message : "Не удалось сохранить шаблон");
    } finally {
      setSaving(false);
    }
  }

  return (
    <div className="content-page standard-page">
      <button type="button" className="back-link" onClick={onBack}>
        ← Назад
      </button>

      <div className="template-info-box">
        Это текст срочного сообщения, которое получат ваши доверенные контакты из списка экстренной помощи.
      </div>

      {loading ? (
        <div className="simple-card">Загрузка шаблона...</div>
      ) : (
        <form onSubmit={handleSave} className="template-form">
          <label className="field">
            <span className="template-label">Текст сообщения</span>
            <textarea
              className="template-textarea"
              value={messageText}
              onChange={(e) => setMessageText(e.target.value)}
              maxLength={500}
              placeholder="Я в беде, помоги!"
            />
            <div className="template-hint">Не более 20 символов</div>
          </label>

          <label className="field">
            <span className="template-label">Подпись для геолокации</span>
            <textarea
              className="template-textarea"
              value={geoSignature}
              onChange={(e) => setGeoSignature(e.target.value)}
              maxLength={200}
              placeholder="Я нахожусь здесь"
            />
            <div className="template-hint">Не более 20 символов</div>
          </label>

          {error ? <div className="status-red">{error}</div> : null}
          {status ? <div className="status-green">{status}</div> : null}

          <button className="template-save-button" type="submit" disabled={saving}>
            {saving ? "СОХРАНЕНИЕ..." : "СОХРАНИТЬ"}
          </button>
        </form>
      )}
    </div>
  );
}
