import { api } from "./client";

export type CrisisCenter = {
  id: number;
  name: string;
  address: string;
  phone_number: string;
};

export function getCrisisCenters() {
  return api<CrisisCenter[]>("/crisis-centers", {
    method: "GET",
  });
}
