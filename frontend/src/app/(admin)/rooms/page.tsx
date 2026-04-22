"use client";

import { useState } from "react";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { useForm } from "react-hook-form";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import { roomsApi } from "@/api/rooms";
import { Room } from "@/types";
import { PageHeader } from "@/components/shared/PageHeader";
import { DataTable } from "@/components/shared/DataTable";
import { ConfirmDialog } from "@/components/shared/ConfirmDialog";
import { RoomStatusBadge } from "@/components/shared/StatusBadge";
import { LoadingSpinner } from "@/components/shared/LoadingSpinner";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from "@/components/ui/dialog";
import { Plus, Pencil, Trash2 } from "lucide-react";

const schema = z.object({
  room_type_id: z.coerce.number().min(1, "Chọn loại phòng"),
  code: z.string().min(1, "Nhập mã phòng"),
  floor: z.string().min(1, "Nhập tầng"),
  status: z.enum(["available", "occupied", "booked", "cleaning", "maintenance"]),
  notes: z.string().optional(),
});
type FormData = z.infer<typeof schema>;

export default function RoomsPage() {
  const qc = useQueryClient();
  const [open, setOpen] = useState(false);
  const [editing, setEditing] = useState<Room | null>(null);
  const [filterStatus, setFilterStatus] = useState("all");

  const { data: rooms, isLoading } = useQuery({
    queryKey: ["admin-rooms", filterStatus],
    queryFn: () =>
      roomsApi.adminRooms(filterStatus !== "all" ? { status: filterStatus as Room["status"] } : {}).then((r) => r.data),
  });

  const { data: roomTypes } = useQuery({
    queryKey: ["admin-room-types"],
    queryFn: () => roomsApi.adminRoomTypes().then((r) => r.data),
  });

  const { register, handleSubmit, reset, setValue, formState: { errors, isSubmitting } } = useForm<FormData>({
    resolver: zodResolver(schema),
    defaultValues: { status: "available" },
  });

  const upsert = useMutation({
    mutationFn: (d: FormData) => editing ? roomsApi.updateRoom(editing.id, d) : roomsApi.createRoom(d),
    onSuccess: () => { qc.invalidateQueries({ queryKey: ["admin-rooms"] }); closeDialog(); },
  });

  const del = useMutation({
    mutationFn: (id: number) => roomsApi.deleteRoom(id),
    onSuccess: () => qc.invalidateQueries({ queryKey: ["admin-rooms"] }),
  });

  const openCreate = () => { reset({ status: "available" }); setEditing(null); setOpen(true); };
  const openEdit = (r: Room) => {
    reset({ ...r, room_type_id: r.room_type_id, notes: r.notes ?? "" });
    setEditing(r); setOpen(true);
  };
  const closeDialog = () => { setOpen(false); setEditing(null); reset({}); };

  const columns = [
    { key: "code", header: "Mã phòng" },
    { key: "floor", header: "Tầng" },
    { key: "room_type", header: "Loại phòng", render: (r: Room) => r.room_type?.name ?? "-" },
    { key: "status", header: "Trạng thái", render: (r: Room) => <RoomStatusBadge status={r.status} /> },
    { key: "notes", header: "Ghi chú", render: (r: Room) => r.notes ?? "-" },
    {
      key: "actions", header: "",
      render: (r: Room) => (
        <div className="flex gap-1">
          <Button size="icon-sm" variant="ghost" onClick={() => openEdit(r)}><Pencil /></Button>
          <ConfirmDialog
            trigger={<Button size="icon-sm" variant="ghost" className="text-destructive"><Trash2 /></Button>}
            title="Xóa phòng"
            description={`Xóa phòng "${r.code}"?`}
            confirmLabel="Xóa"
            onConfirm={() => del.mutate(r.id)}
          />
        </div>
      ),
    },
  ];

  return (
    <div>
      <PageHeader
        title="Phòng"
        action={<Button size="sm" onClick={openCreate}><Plus className="mr-1.5" />Thêm phòng</Button>}
      />
      <div className="mb-4">
        <Select value={filterStatus} onValueChange={setFilterStatus}>
          <SelectTrigger className="w-44">
            <SelectValue placeholder="Lọc trạng thái" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="all">Tất cả</SelectItem>
            <SelectItem value="available">Trống</SelectItem>
            <SelectItem value="occupied">Đang ở</SelectItem>
            <SelectItem value="booked">Đã đặt</SelectItem>
            <SelectItem value="cleaning">Dọn dẹp</SelectItem>
            <SelectItem value="maintenance">Bảo trì</SelectItem>
          </SelectContent>
        </Select>
      </div>
      {isLoading ? <LoadingSpinner /> : (
        <DataTable
          columns={columns as Parameters<typeof DataTable>[0]["columns"]}
          data={(rooms ?? []) as Record<string, unknown>[]}
          emptyMessage="Chưa có phòng nào"
        />
      )}

      <Dialog open={open} onOpenChange={(v) => !v && closeDialog()}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>{editing ? "Sửa phòng" : "Thêm phòng"}</DialogTitle>
          </DialogHeader>
          <form onSubmit={handleSubmit((d) => upsert.mutate(d))} className="space-y-3">
            <div className="space-y-1">
              <Label>Loại phòng</Label>
              <Select
                defaultValue={editing?.room_type_id?.toString()}
                onValueChange={(v) => setValue("room_type_id", Number(v))}
              >
                <SelectTrigger><SelectValue placeholder="Chọn loại phòng" /></SelectTrigger>
                <SelectContent>
                  {(roomTypes ?? []).map((rt) => (
                    <SelectItem key={rt.id} value={rt.id.toString()}>{rt.name}</SelectItem>
                  ))}
                </SelectContent>
              </Select>
              {errors.room_type_id && <p className="text-xs text-red-500">{errors.room_type_id.message}</p>}
            </div>
            <div className="grid grid-cols-2 gap-3">
              <div className="space-y-1">
                <Label>Mã phòng</Label>
                <Input {...register("code")} placeholder="101" />
                {errors.code && <p className="text-xs text-red-500">{errors.code.message}</p>}
              </div>
              <div className="space-y-1">
                <Label>Tầng</Label>
                <Input {...register("floor")} placeholder="1" />
              </div>
            </div>
            <div className="space-y-1">
              <Label>Trạng thái</Label>
              <Select defaultValue={editing?.status ?? "available"} onValueChange={(v) => setValue("status", v as Room["status"])}>
                <SelectTrigger><SelectValue /></SelectTrigger>
                <SelectContent>
                  <SelectItem value="available">Trống</SelectItem>
                  <SelectItem value="occupied">Đang ở</SelectItem>
                  <SelectItem value="booked">Đã đặt</SelectItem>
                  <SelectItem value="cleaning">Dọn dẹp</SelectItem>
                  <SelectItem value="maintenance">Bảo trì</SelectItem>
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-1">
              <Label>Ghi chú</Label>
              <Input {...register("notes")} />
            </div>
            <DialogFooter>
              <Button variant="outline" type="button" onClick={closeDialog}>Hủy</Button>
              <Button type="submit" disabled={isSubmitting}>Lưu</Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>
    </div>
  );
}
