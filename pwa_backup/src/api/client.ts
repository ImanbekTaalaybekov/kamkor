import { clearToken, getToken } from "../auth/token";

const API_BASE = "https://kamkor.mvd.kg/api";

type ApiOptions = RequestInit & {
  auth?: boolean;
};

export async function api<T>(path: string, options: ApiOptions = {}): Promise<T> {
  const headers = new Headers(options.headers || {});

  if (!(options.body instanceof FormData)) {
    headers.set("Content-Type", "application/json");
  }

  if (options.auth) {
    const token = getToken();
    if (token) {
      headers.set("Authorization", `Bearer ${token}`);
    }
  }

  const response = await fetch(`${API_BASE}${path}`, {
    ...options,
    headers,
  });

  const data = await response.json().catch(() => null);

  if (response.status === 401) {
    clearToken();
  }

  if (!response.ok) {
    const message = data?.message || data?.error || "Ошибка запроса";
    throw new Error(message);
  }

  return data as T;
}
