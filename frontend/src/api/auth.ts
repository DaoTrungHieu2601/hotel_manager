import api from "@/lib/axios";
import { User } from "@/types";

export const authApi = {
  login: (email: string, password: string) =>
    api.post<{ user: User; token: string }>("/login", { email, password }),

  register: (data: { name: string; email: string; password: string; password_confirmation: string; phone?: string }) =>
    api.post<{ user: User; token: string }>("/register", data),

  logout: () => api.post("/logout"),

  me: () => api.get<User>("/me"),

  updateProfile: (data: Partial<Pick<User, "name" | "phone" | "address">>) =>
    api.patch<User>("/me", data),

  updatePassword: (data: { current_password: string; password: string; password_confirmation: string }) =>
    api.put("/me/password", data),
};
