const TOKEN_KEY = "kamkor_auth_token";
const PENDING_VERIFICATION_KEY = "kamkor_pending_verification";

export function getToken(): string | null {
  return localStorage.getItem(TOKEN_KEY);
}

export function setToken(token: string): void {
  localStorage.setItem(TOKEN_KEY, token);
}

export function clearToken(): void {
  localStorage.removeItem(TOKEN_KEY);
}

export function setPendingVerification(data: { user_id: number; mode: "login" | "register" }): void {
  localStorage.setItem(PENDING_VERIFICATION_KEY, JSON.stringify(data));
}

export function getPendingVerification(): { user_id: number; mode: "login" | "register" } | null {
  const raw = localStorage.getItem(PENDING_VERIFICATION_KEY);
  if (!raw) return null;

  try {
    return JSON.parse(raw);
  } catch {
    localStorage.removeItem(PENDING_VERIFICATION_KEY);
    return null;
  }
}

export function clearPendingVerification(): void {
  localStorage.removeItem(PENDING_VERIFICATION_KEY);
}
