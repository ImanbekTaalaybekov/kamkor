import { login, logout, me, register, verifySms } from "../api/auth";
import type { AuthMode, LoginPayload, RegisterPayload, SessionData } from "./auth.types";
import {
  clearPendingVerification,
  clearToken,
  getPendingVerification,
  getToken,
  setPendingVerification,
  setToken,
} from "./token";

export async function startLogin(payload: LoginPayload) {
  const result = await login(payload);
  setPendingVerification({ user_id: result.user_id, mode: "login" });
  return result;
}

export async function startRegister(payload: RegisterPayload) {
  const result = await register(payload);
  setPendingVerification({ user_id: result.user_id, mode: "register" });
  return result;
}

export async function confirmVerification(input: {
  userId: number;
  code: string;
  device: string;
}): Promise<SessionData> {
  const verified = await verifySms({
    user_id: input.userId,
    code: input.code,
    device: input.device,
  });

  setToken(verified.auth_token);
  clearPendingVerification();

  const profile = await me();
  return {
    user: profile.user,
    sos_button_available: profile.sos_button_available,
  };
}

export async function destroySession(): Promise<void> {
  try {
    if (getToken()) {
      await logout();
    }
  } finally {
    clearToken();
    clearPendingVerification();
  }
}

export async function restoreSession():
  Promise<
    | { status: "authorized"; session: SessionData }
    | { status: "pending"; userId: number; mode: AuthMode }
    | { status: "empty" }
  > {
  const token = getToken();
  const pending = getPendingVerification();

  if (token) {
    try {
      const profile = await me();
      return {
        status: "authorized",
        session: {
          user: profile.user,
          sos_button_available: profile.sos_button_available,
        },
      };
    } catch {
      clearToken();
    }
  }

  if (pending) {
    return {
      status: "pending",
      userId: pending.user_id,
      mode: pending.mode,
    };
  }

  return { status: "empty" };
}
