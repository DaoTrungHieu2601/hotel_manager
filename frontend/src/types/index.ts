export interface User {
  id: number;
  name: string;
  email: string;
  role: string;
  phone: string | null;
  address: string | null;
  cccd: string | null;
  permissions: string[];
  can_access_admin: boolean;
  can_access_reception: boolean;
  is_customer: boolean;
  created_at: string;
}

export interface RoomType {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  facilities: string | null;
  amenities: string | null;
  default_price: number;
  beds: number;
  max_occupancy: number;
  image_path: string | null;
  rooms_count?: number;
}

export interface Room {
  id: number;
  room_type_id: number;
  code: string;
  floor: string;
  status: "available" | "occupied" | "booked" | "cleaning" | "maintenance";
  notes: string | null;
  room_type?: RoomType;
}

export interface Service {
  id: number;
  name: string;
  price: number;
  is_active: boolean;
}

export interface Booking {
  id: number;
  user_id: number;
  room_type_id: number;
  room_id: number | null;
  check_in: string;
  check_out: string;
  guests: number;
  status: "draft" | "pending" | "confirmed" | "checked_in" | "checked_out" | "cancelled";
  deposit_amount: number;
  rate_per_night: number;
  guest_notes: string | null;
  confirmed_at: string | null;
  checked_in_at: string | null;
  checked_out_at: string | null;
  created_at: string;
  user?: User;
  room_type?: RoomType;
  room?: Room;
  services?: Service[];
  invoice?: Invoice;
}

export interface Invoice {
  id: number;
  booking_id: number;
  invoice_number: string;
  nights: number;
  room_subtotal: number;
  services_subtotal: number;
  deposit: number;
  total: number;
  issued_at: string;
}

export interface ChatConversation {
  id: number;
  user_id: number | null;
  guest_key: string | null;
  last_message_at: string;
  user?: User;
  messages?: ChatMessage[];
}

export interface ChatMessage {
  id: number;
  chat_conversation_id: number;
  sender_id: number | null;
  is_admin: boolean;
  body: string;
  read_at: string | null;
  created_at: string;
  sender?: User;
}

export interface Paginated<T> {
  data: T[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}
