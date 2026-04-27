import { useEffect, useState } from "react";
import { getPsychologicalHelpArticle, getPsychologicalHelpList, type PsychologicalHelpArticle } from "../../api/psychologicalHelp";

type Props = {
  onBack: () => void;
};

export default function PsychologicalHelpPage({ onBack }: Props) {
  const [items, setItems] = useState<PsychologicalHelpArticle[]>([]);
  const [openId, setOpenId] = useState<number | null>(null);
  const [openArticle, setOpenArticle] = useState<PsychologicalHelpArticle | null>(null);
  const [loading, setLoading] = useState(true);
  const [opening, setOpening] = useState(false);
  const [error, setError] = useState("");

  useEffect(() => {
    void loadArticles();
  }, []);

  async function loadArticles() {
    setLoading(true);
    setError("");

    try {
      const data = await getPsychologicalHelpList();
      setItems(data);
      if (data.length > 0) {
        await handleToggle(data[0].id, true);
      }
    } catch (err) {
      setError(err instanceof Error ? err.message : "Не удалось загрузить психологическую помощь");
    } finally {
      setLoading(false);
    }
  }

  async function handleToggle(id: number, forceOpen = false) {
    if (!forceOpen && openId === id) {
      setOpenId(null);
      setOpenArticle(null);
      return;
    }

    setOpening(true);
    setError("");

    try {
      const article = await getPsychologicalHelpArticle(id);
      setOpenId(id);
      setOpenArticle(article);
    } catch (err) {
      setError(err instanceof Error ? err.message : "Не удалось открыть статью");
    } finally {
      setOpening(false);
    }
  }

  return (
    <div className="content-page psych-page standard-page">
      <button type="button" className="back-link" onClick={onBack}>← Назад</button>
      <div className="green-page-header large">Психологическая помощь</div>
      {loading ? <div className="simple-card">Загрузка...</div> : null}
      {error ? <div className="status-red">{error}</div> : null}
      <div className="psych-list">
        {items.map((item) => {
          const isOpen = openId === item.id;
          return (
            <div className="psych-card" key={item.id}>
              <button type="button" className="psych-card-header" onClick={() => void handleToggle(item.id)}>
                <span className="psych-card-title">{item.title}</span>
                <span className={isOpen ? "psych-arrow open" : "psych-arrow"}>⌃</span>
              </button>
              {isOpen ? (
                <div className="psych-card-body">
                  <div className="psych-card-text">{opening ? "Загрузка..." : openArticle?.content || ""}</div>
                </div>
              ) : null}
            </div>
          );
        })}
      </div>
    </div>
  );
}
