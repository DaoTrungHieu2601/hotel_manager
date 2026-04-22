"use client";

import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { roomsApi } from "@/api/rooms";
import { Room } from "@/types";
import { PageHeader } from "@/components/shared/PageHeader";
import { RoomStatusBadge } from "@/components/shared/StatusBadge";
import { LoadingSpinner } from "@/components/shared/LoadingSpinner";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

const STATUS_OPTIONS: { value: Room["status"]; label: string }[] = [
  { value: "available",   label: "Trống" },
  { value: "cleaning",    label: "Dọn dẹp" },
  { value: "maintenance", label: "Bảo trì" },
];

export default function ReceptionRoomsPage() {
  const qc = useQueryClient();

  const { data: rooms, isLoading } = useQuery({
    queryKey: ["reception-rooms"],
    queryFn: () => roomsApi.adminRooms().then((r) => r.data),
    refetchInterval: 30000,
  });

  const updateStatus = useMutation({
    mutationFn: ({ id, status }: { id: number; status: Room["status"] }) =>
      roomsApi.updateRoomStatus(id, status),
    onSuccess: () => qc.invalidateQueries({ queryKey: ["reception-rooms"] }),
  });

  if (isLoading) return <LoadingSpinner />;

  return (
    <div>
      <PageHeader title="Quản lý phòng" description="Cập nhật trạng thái phòng nhanh" />
      <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
        {(rooms ?? []).map((room) => (
          <Card key={room.id} className="overflow-hidden">
            <CardHeader className="py-3 pb-2">
              <div className="flex items-center justify-between">
                <CardTitle className="text-base">{room.code}</CardTitle>
                <RoomStatusBadge status={room.status} />
              </div>
              <p className="text-xs text-muted-foreground">Tầng {room.floor} · {room.room_type?.name}</p>
            </CardHeader>
            <CardContent className="pb-3">
              <Select
                value={room.status}
                onValueChange={(v) => updateStatus.mutate({ id: room.id, status: v as Room["status"] })}
                disabled={room.status === "occupied" || room.status === "booked"}
              >
                <SelectTrigger className="h-7 text-xs">
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  {STATUS_OPTIONS.map((o) => (
                    <SelectItem key={o.value} value={o.value}>{o.label}</SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </CardContent>
          </Card>
        ))}
      </div>
    </div>
  );
}
