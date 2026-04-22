import api from "@/lib/axios";
import { Service, User, Paginated, ChatConversation, ChatMessage } from "@/types";

export const adminApi = {
  dashboard: () => api.get("/admin/dashboard"),

  // Services
  services: () => api.get<Service[]>("/admin/services"),
  createService: (data: Partial<Service>) => api.post<Service>("/admin/services", data),
  updateService: (id: number, data: Partial<Service>) => api.put<Service>(`/admin/services/${id}`, data),
  deleteService: (id: number) => api.delete(`/admin/services/${id}`),

  // Staff
  staff: (params?: { role?: string; page?: number }) =>
    api.get<Paginated<User>>("/admin/staff", { params }),
  createStaff: (data: Partial<User> & { password: string }) =>
    api.post<User>("/admin/staff", data),
  updateStaff: (id: number, data: Partial<User>) =>
    api.put<User>(`/admin/staff/${id}`, data),
  deleteStaff: (id: number) => api.delete(`/admin/staff/${id}`),

  // Permissions
  permissions: (params?: { role?: string }) =>
    api.get("/admin/permissions", { params }),
  updateRole: (userId: number, role: string) =>
    api.patch(`/admin/permissions/${userId}/role`, { role }),
  updatePermissions: (userId: number, permissions: string[]) =>
    api.patch(`/admin/permissions/${userId}/perms`, { permissions }),
  resetPermissions: (userId: number) =>
    api.delete(`/admin/permissions/${userId}/reset`),

  // Chat
  conversations: (page = 1) =>
    api.get<Paginated<ChatConversation>>("/admin/messages", { params: { page } }),
  conversationMessages: (id: number) =>
    api.get<{ conversation: ChatConversation; messages: ChatMessage[] }>(`/admin/messages/${id}`),
  replyMessage: (conversationId: number, body: string) =>
    api.post<ChatMessage>(`/admin/messages/${conversationId}/reply`, { body }),
};
