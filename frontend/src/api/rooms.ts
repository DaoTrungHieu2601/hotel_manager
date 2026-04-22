import api from "@/lib/axios";
import { Room, RoomType, Paginated } from "@/types";

export const roomsApi = {
  // Public
  roomTypes: () => api.get<RoomType[]>("/room-types"),
  roomType: (id: number) => api.get<RoomType>(`/room-types/${id}`),
  searchRooms: (params: { check_in: string; check_out: string; guests: number }) =>
    api.get<RoomType[]>("/search-rooms", { params }),

  // Admin - Room Types
  adminRoomTypes: () => api.get<RoomType[]>("/admin/room-types"),
  createRoomType: (data: Partial<RoomType>) => api.post<RoomType>("/admin/room-types", data),
  updateRoomType: (id: number, data: Partial<RoomType>) => api.put<RoomType>(`/admin/room-types/${id}`, data),
  deleteRoomType: (id: number) => api.delete(`/admin/room-types/${id}`),

  // Admin - Rooms
  adminRooms: (params?: { status?: string; room_type_id?: number }) =>
    api.get<Room[]>("/admin/rooms", { params }),
  createRoom: (data: Partial<Room>) => api.post<Room>("/admin/rooms", data),
  updateRoom: (id: number, data: Partial<Room>) => api.put<Room>(`/admin/rooms/${id}`, data),
  deleteRoom: (id: number) => api.delete(`/admin/rooms/${id}`),
  updateRoomStatus: (id: number, status: Room["status"]) =>
    api.patch<Room>(`/reception/rooms/${id}/status`, { status }),
};
