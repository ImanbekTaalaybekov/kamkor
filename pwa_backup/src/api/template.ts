import { api } from "./client";

export type MessageTemplate = {
    message_text: string;
    geo_signature: string;
};

export function getTemplate() {
    return api<MessageTemplate>("/message-template", {
        method: "GET",
        auth: true,
    });
}

export function updateTemplate(payload: {
    message_text?: string;
    geo_signature?: string;
}) {
    return api<{ success: boolean; message: string }>("/message-template", {
        method: "PUT",
        auth: true,
        body: JSON.stringify(payload),
    });
}
