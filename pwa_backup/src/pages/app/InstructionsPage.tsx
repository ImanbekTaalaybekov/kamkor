import { useEffect, useState } from "react";
import { getInstruction, getInstructions, type EmergencyInstruction } from "../../api/instructions";

type Props = {
  onBack: () => void;
};

export default function InstructionsPage({ onBack }: Props) {
  const [items, setItems] = useState<EmergencyInstruction[]>([]);
  const [selected, setSelected] = useState<EmergencyInstruction | null>(null);
  const [loading, setLoading] = useState(true);
  const [detailsLoading, setDetailsLoading] = useState(false);
  const [error, setError] = useState("");

  useEffect(() => {
    void loadInstructions();
  }, []);

  async function loadInstructions() {
    setLoading(true);
    setError("");

    try {
      const data = await getInstructions();
      setItems(data);
      if (data.length > 0) {
        await openInstruction(data[0].id);
      }
    } catch (err) {
      setError(err instanceof Error ? err.message : "Не удалось загрузить инструкции");
    } finally {
      setLoading(false);
    }
  }

  async function openInstruction(id: number) {
    setDetailsLoading(true);
    setError("");

    try {
      const data = await getInstruction(id);
      setSelected(data);
    } catch (err) {
      setError(err instanceof Error ? err.message : "Не удалось открыть инструкцию");
    } finally {
      setDetailsLoading(false);
    }
  }

  if (loading) {
    return <div className="content-page standard-page"><div className="simple-card">Загрузка инструкций...</div></div>;
  }

  if (selected) {
    return (
      <div className="content-page standard-page instructions-page">
        <button type="button" className="back-link" onClick={() => setSelected(null)}>
          Назад
        </button>
        <div className="green-page-header large">Инструкции в экстренных ситуациях</div>
        {error ? <div className="status-red">{error}</div> : null}
        {detailsLoading ? <div className="simple-card">Загрузка...</div> : <div className="instruction-content-text">{selected.content}</div>}
      </div>
    );
  }

  return (
    <div className="content-page standard-page instructions-page">
      <button type="button" className="back-link" onClick={onBack}>← Назад</button>
      <div className="green-page-header">Инструкции</div>
      {error ? <div className="status-red">{error}</div> : null}
      <div className="instructions-list">
        {items.map((item) => (
          <button key={item.id} type="button" className="instruction-list-card" onClick={() => void openInstruction(item.id)}>
            {item.title}
          </button>
        ))}
      </div>
    </div>
  );
}
