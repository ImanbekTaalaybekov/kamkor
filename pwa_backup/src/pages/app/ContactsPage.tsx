import { useEffect, useState } from "react";
import { addContact, deleteContact, getContacts, updateContact, type TrustedContact } from "../../api/contacts";
import type { SessionData } from "../../auth/auth.types";

type Props = {
  session: SessionData | null;
  onBack: () => void;
};

type EditingContact = {
  id: number | null;
  name: string;
  phone_number: string;
};

export default function ContactsPage({ session, onBack }: Props) {
  const [contacts, setContacts] = useState<TrustedContact[]>([]);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState("");
  const [showForm, setShowForm] = useState(false);
  const [editing, setEditing] = useState<EditingContact>({ id: null, name: "", phone_number: "" });

  useEffect(() => {
    void loadContacts();
  }, []);

  async function loadContacts() {
    setLoading(true);
    setError("");
    try {
      const data = await getContacts();
      setContacts(data);
    } catch (err) {
      setError(err instanceof Error ? err.message : "Не удалось загрузить контакты");
    } finally {
      setLoading(false);
    }
  }

  function openAddForm() {
    setEditing({ id: null, name: "", phone_number: "" });
    setShowForm(true);
  }

  function openEditForm(contact: TrustedContact) {
    setEditing({ id: contact.id, name: contact.name, phone_number: contact.phone_number });
    setShowForm(true);
  }

  function closeForm() {
    setShowForm(false);
    setEditing({ id: null, name: "", phone_number: "" });
  }

  async function handleSave(e: React.FormEvent) {
    e.preventDefault();
    setSaving(true);
    setError("");

    try {
      if (editing.id) {
        const updated = await updateContact(editing.id, { name: editing.name, phone_number: editing.phone_number });
        setContacts((prev) => prev.map((item) => (item.id === updated.id ? updated : item)));
      } else {
        const created = await addContact({ name: editing.name, phone_number: editing.phone_number });
        setContacts((prev) => [...prev, created].sort((a, b) => a.name.localeCompare(b.name)));
      }
      closeForm();
    } catch (err) {
      setError(err instanceof Error ? err.message : "Не удалось сохранить контакт");
    } finally {
      setSaving(false);
    }
  }

  async function handleDelete(id: number) {
    const confirmed = window.confirm("Удалить контакт?");
    if (!confirmed) return;
    try {
      await deleteContact(id);
      setContacts((prev) => prev.filter((item) => item.id !== id));
    } catch (err) {
      setError(err instanceof Error ? err.message : "Не удалось удалить контакт");
    }
  }

  return (
    <div className="content-page contacts-page standard-page">
      <div className="app-topbar compact">
        <button type="button" className="back-badge" onClick={onBack}>←</button>
        <div className="profile-name">{session?.user.name || ""} {session?.user.surname || ""}</div>
        <div className="country-code">KG</div>
      </div>

      {error ? <div className="status-red">{error}</div> : null}

      {loading ? (
        <div className="simple-card">Загрузка контактов...</div>
      ) : (
        <div className="contacts-list">
          {contacts.map((contact) => (
            <div className="contact-card" key={contact.id}>
              <div className="contact-main">
                <div className="contact-name">{contact.name}</div>
                <div className="contact-phone">{contact.phone_number}</div>
              </div>
              <div className="contact-actions">
                <button type="button" className="icon-btn edit" onClick={() => openEditForm(contact)} title="Редактировать">✎</button>
                <button type="button" className="icon-btn delete" onClick={() => handleDelete(contact.id)} title="Удалить">🗑</button>
              </div>
            </div>
          ))}
        </div>
      )}

      <div className="floating-add-wrap">
        <button type="button" className="floating-add-button" onClick={openAddForm}>+</button>
      </div>

      {showForm ? (
        <div className="modal-backdrop" onClick={closeForm}>
          <div className="modal-card" onClick={(e) => e.stopPropagation()}>
            <div className="modal-title">{editing.id ? "Редактировать контакт" : "Добавить контакт"}</div>
            <form onSubmit={handleSave}>
              <label className="field">
                <span className="label">Имя</span>
                <input className="input" value={editing.name} onChange={(e) => setEditing((prev) => ({ ...prev, name: e.target.value }))} placeholder="Введите имя" />
              </label>
              <label className="field">
                <span className="label">Телефон</span>
                <input className="input" value={editing.phone_number} onChange={(e) => setEditing((prev) => ({ ...prev, phone_number: e.target.value }))} placeholder="+996..." />
              </label>
              <button className="button primary flat-button" type="submit" disabled={saving}>{saving ? "СОХРАНЯЕМ..." : "СОХРАНИТЬ"}</button>
              <button className="button secondary flat-button secondary-light" type="button" onClick={closeForm}>ОТМЕНА</button>
            </form>
          </div>
        </div>
      ) : null}
    </div>
  );
}
