import type { SessionData } from "../../auth/auth.types";
import { destroySession } from "../../auth/session";

type Props = {
  session: SessionData | null;
  onBack: () => void;
  onLoggedOut: () => void;
};

export default function ProfilePage({ session, onBack, onLoggedOut }: Props) {
  const user = session?.user;

  async function handleLogout() {
    await destroySession();
    onLoggedOut();
  }

  return (
    <div className="content-page profile-page">
      <button type="button" className="back-link" onClick={onBack}>
        ← Назад
      </button>

      <div className="green-page-header">Личная страница</div>

      <div className="profile-block">
        <div className="profile-name-big">{user?.name || "Жаркынай"} {user?.surname || "Алматова"}</div>
        <div className="profile-role">Аккаунт пользователя</div>

        <div className="profile-line">
          <span>Номер телефона:</span>
          <strong>{user?.phone_number || "+996 777 111 111"}</strong>
        </div>
      </div>

      <button className="button secondary logout-button" type="button" onClick={handleLogout}>
        Выйти
      </button>
    </div>
  );
}
