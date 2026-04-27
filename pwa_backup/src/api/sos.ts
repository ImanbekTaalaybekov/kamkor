import { api } from "./client";
import type { SosResponse } from "../auth/auth.types";

type SendSosOptions = {
  signal?: AbortSignal;
};

export async function sendSos(geo: string, options: SendSosOptions = {}) {
  try {
    return await api<SosResponse>("/sos", {
      method: "POST",
      auth: true,
      signal: options.signal,
      body: JSON.stringify({ geolocation: geo }),
    });
  } catch {
    return api<SosResponse>("/sos/create", {
      method: "POST",
      auth: true,
      signal: options.signal,
      body: JSON.stringify({ geo }),
    });
  }
}

export async function uploadSosAudioStub(sosId: number, options: SendSosOptions = {}) {
  try {
    return await api<{ success: boolean; message: string }>(`/sos/upload-record/${sosId}`, {
      method: "POST",
      auth: true,
      signal: options.signal,
      body: JSON.stringify({ audio: "stub" }),
    });
  } catch {
    return {
      success: true,
      message: "Аудио временно отключено",
    };
  }
}
