import { api } from "./client";

export type PsychologicalHelpArticle = {
    id: number;
    title: string;
    content: string;
    created_at: string;
};

export function getPsychologicalHelpList() {
    return api<PsychologicalHelpArticle[]>("/psychological-help", {
        method: "GET",
    });
}

export function getPsychologicalHelpArticle(id: number) {
    return api<PsychologicalHelpArticle>(`/psychological-help/${id}`, {
        method: "GET",
    });
}
