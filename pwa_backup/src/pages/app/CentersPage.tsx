import { useEffect, useState } from "react";
import { getCrisisCenters, type CrisisCenter } from "../../api/crisisCenters";

type Props = {
  onBack: () => void;
};

export default function CentersPage({ onBack }: Props) {
  const [centers, setCenters] = useState<CrisisCenter[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    void loadCenters();
  }, []);

  async function loadCenters() {
    setLoading(true);
    setError("");
    try {
      const data = await getCrisisCenters();
      setCenters(data || []);
    } catch (err) {
      setError(err instanceof Error ? err.message : "Не удалось загрузить кризисные центры");
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="content-page standard-page">
      <button type="button" className="back-link" onClick={onBack}>
        ← Назад
      </button>

      <div className="green-page-header">Кризисные центры</div>

      {loading ? <div className="simple-card centers-state-card">Загрузка...</div> : null}
      {error ? <div className="status-red centers-state-card">{error}</div> : null}
      {!loading && !error && centers.length === 0 ? <div className="simple-card centers-state-card">Список пока пуст</div> : null}

      <div className="centers-list">
        {centers.map((center) => (
          <div className="center-card" key={center.id}>
            <div>
              <div className="center-card-title">{center.name}</div>
              <div className="center-card-address">{center.address}</div>
              <div className="center-card-phone">{center.phone_number}</div>
            </div>
            <a className="center-call" href={`tel:${center.phone_number}`} aria-label={`Позвонить в ${center.name}`}>
              ☎
            </a>
          </div>
        ))}
      </div>
    </div>
  );
}
