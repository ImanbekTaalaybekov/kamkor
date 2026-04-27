export type User = {
  id: number;
  name: string;
  surname: string;
  pin: string;
  phone_number: string;
  region?: string;
  uvd?: string;
  address?: string;
  icon?: string;
};

export type AuthMode = "login" | "register";

export type LoginPayload = {
  pin: string;
  phone_number: string;
  password: string;
  device: string;
};

export type RegisterPayload = {
  pin: string;
  name: string;
  surname: string;
  phone_number: string;
  password: string;
  device: string;
};

export type LoginResponse = {
  message: string;
  requires_verification: boolean;
  user_id: number;
};

export type RegisterResponse = {
  message: string;
  requires_verification: boolean;
  user_id: number;
};

export type VerifySmsPayload = {
  user_id: number;
  code: string;
  device: string;
};

export type VerifySmsResponse = {
  auth_token: string;
  user: User;
};

export type MeResponse = {
  user: User;
  sos_button_available: boolean;
};

export type SessionData = {
  user: User;
  sos_button_available: boolean;
};

export type SosResponse = {
  success: boolean;
  sos_id: number;
  message: string;
};
