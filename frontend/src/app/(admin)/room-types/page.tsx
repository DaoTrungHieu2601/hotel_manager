"use client";

import { useState } from "react";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { useForm } from "react-hook-form";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import { roomsApi } from "@/api/rooms";
import { RoomType } from "@/types";
import { PageHeader } from "@/components/shared/PageHeader";
import { DataTable } from "@/components/shared/DataTable";
import { ConfirmDialog } from "@/components/shared/ConfirmDialog";
import { LoadingSpinner } from "@/components/shared/LoadingSpinner";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from "@/components/ui/dialog";
import { Plus, Pencil, Trash2 } from "lucide-react";

const schema = z.object({
  name: z.string().min(1, "Vui lòng nhập tên"),
  slug: z.string().min(1, "Vui lòng nhập slug"),
  default_price: z.coerce.number().min(0),
  beds: z.coerce.number().min(1),
  max_occupancy: z.coerce.number().min(1),
  description: z.string().optional(),
  facilities: z.string().optional(),
  amenities: z.string().optional(),
});
type FormData = z.infer<typeof schema>;

export default function RoomTypesPage() {
  const qc = useQueryClient();
  const [open, setOpen] = useState(false);
  const [editing, setEditing] = useState<RoomType | null>(null);

  const { data, isLoading } = useQuery({
    queryKey: ["admin-room-types"],
    queryFn: () => roomsApi.adminRoomTypes().then((r) => r.data),
  });

  const { register, handleSubmit, reset, formState: { errors, isSubmitting } } = useForm<FormData>({
    resolver: zodResolver(schema),
  });

  const upsert = useMutation({
    mutationFn: (d: FormData) =>
      editing ? roomsApi.updateRoomType(editing.id, d) : roomsApi.createRoomType(d),
    onSuccess: () => { qc.invalidateQueries({ queryKey: ["admin-room-types"] }); closeDialog(); },
  });

  const del = useMutation({
    mutationFn: (id: number) => roomsApi.deleteRoomType(id),
    onSuccess: () => qc.invalidateQueries({ queryKey: ["admin-room-types"] }),
  });

  const openCreate = () => { reset({}); setEditing(null); setOpen(true); };
  const openEdit = (rt: RoomType) => { reset(rt); setEditing(rt); setOpen(true); };
  const closeDialog = () => { setOpen(false); setEditing(null); reset({}); };

  const columns = [
    { key: "name", header: "Tên loại phòng" },
    { key: "slug", header: "Slug" },
    { key: "default_price", header: "Giá/đêm", render: (r: RoomType) => `${r.default_price.toLocaleString("vi-VN")}đ` },
    { key: "beds", header: "Giường" },
    { key: "max_occupancy", header: "Sức chứa" },
    { key: "rooms_count", header: "Số phòng", render: (r: RoomType) => r.rooms_count ?? 0 },
    {
      key: "actions", header: "",
      render: (r: RoomType) => (
        <div className="flex gap-1">
          <Button size="icon-sm" variant="ghost" onClick={() => openEdit(r)}><Pencil /></Button>
          <ConfirmDialog
            trigger={<Button size="icon-sm" variant="ghost" className="text-destructive"><Trash2 /></Button>}
            title="Xóa loại phòng"
            description={`Xóa "${r.name}"? Không thể xóa nếu có phòng thuộc loại này.`}
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
        title="Loại phòng"
        action={<Button size="sm" onClick={openCreate}><Plus className="mr-1.5" />Thêm loại phòng</Button>}
      />
      {isLoading ? <LoadingSpinner /> : (
        <DataTable
          columns={columns as Parameters<typeof DataTable>[0]["columns"]}
          data={(data ?? []) as Record<string, unknown>[]}
          emptyMessage="Chưa có loại phòng nào"
        />
      )}

      <Dialog open={open} onOpenChange={(v) => !v && closeDialog()}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>{editing ? "Sửa loại phòng" : "Thêm loại phòng"}</DialogTitle>
          </DialogHeader>
          <form onSubmit={handleSubmit((d) => upsert.mutate(d))} className="space-y-3">
            <div className="grid grid-cols-2 gap-3">
              <div className="space-y-1">
                <Label>Tên</Label>
                <Input {...register("name")} />
                {errors.name && <p className="text-xs text-red-500">{errors.name.message}</p>}
              </div>
              <div className="space-y-1">
                <Label>Slug</Label>
                <Input {...register("slug")} />
                {errors.slug && <p className="text-xs text-red-500">{errors.slug.message}</p>}
              </div>
              <div className="space-y-1">
                <Label>Giá/đêm (đ)</Label>
                <Input {...register("default_price")} type="number" />
              </div>
              <div className="space-y-1">
                <Label>Số giường</Label>
                <Input {...register("beds")} type="number" />
              </div>
              <div className="space-y-1 col-span-2">
                <Label>Sức chứa tối đa</Label>
                <Input {...register("max_occupancy")} type="number" />
              </div>
            </div>
            <div className="space-y-1">
              <Label>Mô tả</Label>
              <Textarea {...register("description")} rows={2} />
            </div>
            <div className="space-y-1">
              <Label>Tiện nghi (mỗi dòng 1 mục)</Label>
              <Textarea {...register("amenities")} rows={3} />
            </div>
            <div className="space-y-1">
              <Label>Cơ sở vật chất (mỗi dòng 1 mục)</Label>
              <Textarea {...register("facilities")} rows={3} />
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
