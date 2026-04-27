import { api } from "./client";

export type SosHistoryItem = {
    id: number;
    geo: string;
    audio_url: string | null;
    created_at: string;
};

export type SosHistoryResponse = {
    success: boolean;
    data: SosHistoryItem[];
};

export function getSosHistory() {
    return api<SosHistoryResponse>("/sos", {
        method: "GET",
        auth: true,
    });
}
