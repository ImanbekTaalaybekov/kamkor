import { api } from "./client";
import type {
  LoginPayload,
  LoginResponse,
  MeResponse,
  RegisterPayload,
  RegisterResponse,
  VerifySmsPayload,
  VerifySmsResponse,
} from "../auth/auth.types";

export function login(payload: LoginPayload) {
  return api<LoginResponse>("/user/auth", {
    method: "POST",
    body: JSON.stringify(payload),
  });
}

export function register(payload: RegisterPayload) {
  return api<RegisterResponse>("/user/register", {
    method: "POST",
    body: JSON.stringify(payload),
  });
}

export function verifySms(payload: VerifySmsPayload) {
  return api<VerifySmsResponse>("/user/verify-sms", {
    method: "POST",
    body: JSON.stringify(payload),
  });
}

export function me() {
  return api<MeResponse>("/user/me", {
    method: "GET",
    auth: true,
  });
}

export function logout() {
  return api<{ message: string }>("/user/logout", {
    method: "POST",
    auth: true,
  });
}
