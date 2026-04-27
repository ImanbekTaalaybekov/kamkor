import { api } from "./client";

export type EmergencyInstruction = {
    id: number;
    title: string;
    content: string;
    created_at: string;
};

export function getInstructions() {
    return api<EmergencyInstruction[]>("/emergency-instructions", {
        method: "GET",
    });
}

export function getInstruction(id: number) {
    return api<EmergencyInstruction>(`/emergency-instructions/${id}`, {
        method: "GET",
    });
}
