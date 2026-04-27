import { api } from "./client";

export type TrustedContact = {
    id: number;
    name: string;
    phone_number: string;
};

export function getContacts() {
    return api<TrustedContact[]>("/my-contacts", {
        method: "GET",
        auth: true,
    });
}

export function addContact(payload: { name: string; phone_number: string }) {
    return api<TrustedContact>("/my-contacts", {
        method: "POST",
        auth: true,
        body: JSON.stringify(payload),
    });
}

export function updateContact(
    id: number,
    payload: { name?: string; phone_number?: string }
) {
    return api<TrustedContact>(`/my-contacts/${id}`, {
        method: "PATCH",
        auth: true,
        body: JSON.stringify(payload),
    });
}

export function deleteContact(id: number) {
    return api<{ message: string }>(`/my-contacts/${id}`, {
        method: "DELETE",
        auth: true,
    });
}
