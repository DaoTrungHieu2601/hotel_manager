import api from "@/lib/axios";
import { Booking, Paginated } from "@/types";

export const bookingsApi = {
  // Customer
  myBookings: (page = 1) =>
    api.get<Paginated<Booking>>("/my/bookings", { params: { page } }),

  myBooking: (id: number) =>
    api.get<Booking>(`/my/bookings/${id}`),

  createBooking: (data: {
    room_type_id: number;
    check_in: string;
    check_out: string;
    guests: number;
    guest_notes?: string;
  }) => api.post<Booking>("/my/bookings", data),

  cancelBooking: (id: number) =>
    api.post(`/my/bookings/${id}/cancel`),

  // Admin
  adminBookings: (params?: { status?: string; search?: string; page?: number }) =>
    api.get<Paginated<Booking>>("/admin/bookings", { params }),

  adminStats: () =>
    api.get<{
      pending: number;
      confirmed: number;
      checked_in: number;
      today_checkins: number;
      today_checkouts: number;
    }>("/admin/bookings/stats"),

  // Reception
  reservations: (params?: { status?: string; page?: number }) =>
    api.get<Paginated<Booking>>("/reception/reservations", { params }),

  reservation: (id: number) =>
    api.get<Booking>(`/reception/reservations/${id}`),

  confirmReservation: (id: number) =>
    api.post(`/reception/reservations/${id}/confirm`),

  cancelReservation: (id: number) =>
    api.post(`/reception/reservations/${id}/cancel`),

  checkIn: (id: number, data: { room_id: number; cccd?: string }) =>
    api.post<Booking>(`/reception/reservations/${id}/check-in`, data),

  checkOut: (id: number) =>
    api.post<Booking>(`/reception/reservations/${id}/check-out`),

  addService: (id: number, data: { service_id: number; quantity: number }) =>
    api.post(`/reception/reservations/${id}/services`, data),
};
