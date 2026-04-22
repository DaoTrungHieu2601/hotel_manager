"use client";

import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { bookingsApi } from "@/api/bookings";
import { PageHeader } from "@/components/shared/PageHeader";
import { DataTable } from "@/components/shared/DataTable";
import { BookingStatusBadge } from "@/components/shared/StatusBadge";
import { LoadingSpinner } from "@/components/shared/LoadingSpinner";
import { Input } from "@/components/ui/input";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { useDebounce } from "@/hooks/useDebounce";
import { Booking } from "@/types";

const STATUS_OPTIONS = [
  { value: "all",         label: "Tất cả" },
  { value: "pending",     label: "Chờ xác nhận" },
  { value: "confirmed",   label: "Đã xác nhận" },
  { value: "checked_in",  label: "Đang ở" },
  { value: "checked_out", label: "Đã trả phòng" },
  { value: "cancelled",   label: "Đã hủy" },
];

export default function AdminBookingsPage() {
  const [search, setSearch] = useState("");
  const [status, setStatus] = useState("all");
  const [page, setPage] = useState(1);
  const debouncedSearch = useDebounce(search);

  const { data, isLoading } = useQuery({
    queryKey: ["admin-bookings", debouncedSearch, status, page],
    queryFn: () =>
      bookingsApi.adminBookings({
        search: debouncedSearch || undefined,
        status: status === "all" ? undefined : status,
        page,
      }).then((r) => r.data),
  });

  const columns = [
    { key: "id", header: "#", render: (r: Booking) => `#${r.id}` },
    { key: "user", header: "Khách", render: (r: Booking) => r.user?.name ?? "-" },
    { key: "room_type", header: "Loại phòng", render: (r: Booking) => r.room_type?.name ?? "-" },
    { key: "room", header: "Phòng", render: (r: Booking) => r.room?.code ?? "-" },
    { key: "check_in", header: "Check-in", render: (r: Booking) => r.check_in },
    { key: "check_out", header: "Check-out", render: (r: Booking) => r.check_out },
    { key: "status", header: "Trạng thái", render: (r: Booking) => <BookingStatusBadge status={r.status} /> },
  ];

  return (
    <div>
      <PageHeader title="Đặt phòng" description="Danh sách tất cả đặt phòng" />
      <div className="flex gap-3 mb-4">
        <Input
          placeholder="Tìm theo tên, email khách..."
          value={search}
          onChange={(e) => { setSearch(e.target.value); setPage(1); }}
          className="max-w-xs"
        />
        <Select value={status} onValueChange={(v) => { setStatus(v); setPage(1); }}>
          <SelectTrigger className="w-44">
            <SelectValue />
          </SelectTrigger>
          <SelectContent>
            {STATUS_OPTIONS.map((o) => (
              <SelectItem key={o.value} value={o.value}>{o.label}</SelectItem>
            ))}
          </SelectContent>
        </Select>
      </div>
      {isLoading ? (
        <LoadingSpinner />
      ) : (
        <DataTable
          columns={columns as Parameters<typeof DataTable>[0]["columns"]}
          data={(data?.data ?? []) as Record<string, unknown>[]}
          currentPage={data?.current_page}
          lastPage={data?.last_page}
          onPageChange={setPage}
          emptyMessage="Không có đặt phòng nào"
        />
      )}
    </div>
  );
}
