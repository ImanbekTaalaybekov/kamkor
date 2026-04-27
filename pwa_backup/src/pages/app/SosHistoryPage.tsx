import { useEffect, useState } from "react";
import { getSosHistory, type SosHistoryItem } from "../../api/sosHistory";

type Props = {
  onBack: () => void;
};

function formatDate(value: string) {
  const date = new Date(value);
  return date.toLocaleDateString("ru-RU", { day: "numeric", month: "long", year: "numeric" });
}

export default function SosHistoryPage({ onBack }: Props) {
  const [items, setItems] = useState<SosHistoryItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    void loadHistory();
  }, []);

  async function loadHistory() {
    setLoading(true);
    setError("");
    try {
      const result = await getSosHistory();
      setItems(result.data || []);
    } catch (err) {
      setError(err instanceof Error ? err.message : "Не удалось загрузить историю");
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="content-page standard-page sos-history-page">
      <button type="button" className="back-link" onClick={onBack}>← Назад</button>
      <div className="green-page-header">История SOS сигналов</div>
      {loading ? <div className="simple-card">Загрузка...</div> : null}
      {error ? <div className="status-red">{error}</div> : null}
      {!loading && !error && items.length === 0 ? <div className="simple-card">История пока пуста</div> : null}
      <div className="sos-history-list">
        {items.map((item) => (
          <div className="sos-history-row" key={item.id}>
            <div>
              <div className="sos-history-date">{formatDate(item.created_at)}</div>
              <div className="sos-history-address">{item.geo || "Адрес не указан"}</div>
            </div>
            <div className="history-play">▶</div>
          </div>
        ))}
      </div>
    </div>
  );
}
