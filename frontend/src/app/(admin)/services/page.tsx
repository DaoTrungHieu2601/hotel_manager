"use client";

import { useState } from "react";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { useForm } from "react-hook-form";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import { adminApi } from "@/api/admin";
import { Service } from "@/types";
import { PageHeader } from "@/components/shared/PageHeader";
import { DataTable } from "@/components/shared/DataTable";
import { ConfirmDialog } from "@/components/shared/ConfirmDialog";
import { LoadingSpinner } from "@/components/shared/LoadingSpinner";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Badge } from "@/components/ui/badge";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from "@/components/ui/dialog";
import { Plus, Pencil, Trash2 } from "lucide-react";

const schema = z.object({
  name: z.string().min(1, "Nhập tên dịch vụ"),
  price: z.coerce.number().min(0, "Giá không hợp lệ"),
  is_active: z.boolean().optional(),
});
type FormData = z.infer<typeof schema>;

export default function ServicesPage() {
  const qc = useQueryClient();
  const [open, setOpen] = useState(false);
  const [editing, setEditing] = useState<Service | null>(null);

  const { data, isLoading } = useQuery({
    queryKey: ["admin-services"],
    queryFn: () => adminApi.services().then((r) => r.data),
  });

  const { register, handleSubmit, reset, formState: { errors, isSubmitting } } = useForm<FormData>({
    resolver: zodResolver(schema),
    defaultValues: { is_active: true },
  });

  const upsert = useMutation({
    mutationFn: (d: FormData) => editing ? adminApi.updateService(editing.id, d) : adminApi.createService(d),
    onSuccess: () => { qc.invalidateQueries({ queryKey: ["admin-services"] }); closeDialog(); },
  });

  const del = useMutation({
    mutationFn: (id: number) => adminApi.deleteService(id),
    onSuccess: () => qc.invalidateQueries({ queryKey: ["admin-services"] }),
  });

  const toggleActive = useMutation({
    mutationFn: (s: Service) => adminApi.updateService(s.id, { is_active: !s.is_active }),
    onSuccess: () => qc.invalidateQueries({ queryKey: ["admin-services"] }),
  });

  const openCreate = () => { reset({ is_active: true }); setEditing(null); setOpen(true); };
  const openEdit = (s: Service) => { reset(s); setEditing(s); setOpen(true); };
  const closeDialog = () => { setOpen(false); setEditing(null); reset({}); };

  const columns = [
    { key: "name", header: "Tên dịch vụ" },
    { key: "price", header: "Giá", render: (s: Service) => `${s.price.toLocaleString("vi-VN")}đ` },
    {
      key: "is_active", header: "Trạng thái",
      render: (s: Service) => (
        <button onClick={() => toggleActive.mutate(s)}>
          <Badge variant={s.is_active ? "default" : "outline"}>
            {s.is_active ? "Hoạt động" : "Ngừng"}
          </Badge>
        </button>
      ),
    },
    {
      key: "actions", header: "",
      render: (s: Service) => (
        <div className="flex gap-1">
          <Button size="icon-sm" variant="ghost" onClick={() => openEdit(s)}><Pencil /></Button>
          <ConfirmDialog
            trigger={<Button size="icon-sm" variant="ghost" className="text-destructive"><Trash2 /></Button>}
            title="Xóa dịch vụ"
            description={`Xóa dịch vụ "${s.name}"?`}
            confirmLabel="Xóa"
            onConfirm={() => del.mutate(s.id)}
          />
        </div>
      ),
    },
  ];

  return (
    <div>
      <PageHeader
        title="Dịch vụ"
        action={<Button size="sm" onClick={openCreate}><Plus className="mr-1.5" />Thêm dịch vụ</Button>}
      />
      {isLoading ? <LoadingSpinner /> : (
        <DataTable
          columns={columns as Parameters<typeof DataTable>[0]["columns"]}
          data={(data ?? []) as Record<string, unknown>[]}
          emptyMessage="Chưa có dịch vụ nào"
        />
      )}

      <Dialog open={open} onOpenChange={(v) => !v && closeDialog()}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>{editing ? "Sửa dịch vụ" : "Thêm dịch vụ"}</DialogTitle>
          </DialogHeader>
          <form onSubmit={handleSubmit((d) => upsert.mutate(d))} className="space-y-3">
            <div className="space-y-1">
              <Label>Tên dịch vụ</Label>
              <Input {...register("name")} />
              {errors.name && <p className="text-xs text-red-500">{errors.name.message}</p>}
            </div>
            <div className="space-y-1">
              <Label>Giá (đ)</Label>
              <Input {...register("price")} type="number" />
              {errors.price && <p className="text-xs text-red-500">{errors.price.message}</p>}
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
