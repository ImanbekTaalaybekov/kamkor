export type TabKey = "psych" | "contacts" | "home" | "template" | "profile";

type Props = {
  activeTab: TabKey;
  onChange: (tab: TabKey) => void;
};

const items: { key: TabKey; icon: string; label: string }[] = [
  { key: "psych", icon: "♡", label: "Психол." },
  { key: "contacts", icon: "◉", label: "Контакты" },
  { key: "home", icon: "⌂", label: "Главная" },
  { key: "template", icon: "▤", label: "Шаблоны" },
  { key: "profile", icon: "◎", label: "Профиль" },
];

export default function BottomNav({ activeTab, onChange }: Props) {
  return (
    <div className="bottom-nav">
      {items.map((item) => (
        <button
          key={item.key}
          type="button"
          className={activeTab === item.key ? "bottom-nav-button active" : "bottom-nav-button"}
          onClick={() => onChange(item.key)}
        >
          <span className="bottom-nav-icon">{item.icon}</span>
          <span className="bottom-nav-text">{item.label}</span>
        </button>
      ))}
    </div>
  );
}
