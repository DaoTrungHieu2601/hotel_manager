import { Badge } from "@/components/ui/badge";

const bookingStatusMap: Record<string, { label: string; variant: "default" | "secondary" | "destructive" | "outline" }> = {
  draft:       { label: "Nháp",        variant: "outline" },
  pending:     { label: "Chờ xác nhận", variant: "secondary" },
  confirmed:   { label: "Đã xác nhận", variant: "default" },
  checked_in:  { label: "Đang ở",      variant: "default" },
  checked_out: { label: "Đã trả phòng", variant: "outline" },
  cancelled:   { label: "Đã hủy",      variant: "destructive" },
};

const roomStatusMap: Record<string, { label: string; variant: "default" | "secondary" | "destructive" | "outline" }> = {
  available:   { label: "Trống",       variant: "default" },
  occupied:    { label: "Đang ở",      variant: "secondary" },
  booked:      { label: "Đã đặt",      variant: "outline" },
  cleaning:    { label: "Dọn dẹp",     variant: "secondary" },
  maintenance: { label: "Bảo trì",     variant: "destructive" },
};

export function BookingStatusBadge({ status }: { status: string }) {
  const config = bookingStatusMap[status] ?? { label: status, variant: "outline" as const };
  return <Badge variant={config.variant}>{config.label}</Badge>;
}

export function RoomStatusBadge({ status }: { status: string }) {
  const config = roomStatusMap[status] ?? { label: status, variant: "outline" as const };
  return <Badge variant={config.variant}>{config.label}</Badge>;
}
