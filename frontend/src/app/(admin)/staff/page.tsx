"use client";

import { useState } from "react";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { useForm } from "react-hook-form";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import { adminApi } from "@/api/admin";
import { User } from "@/types";
import { PageHeader } from "@/components/shared/PageHeader";
import { DataTable } from "@/components/shared/DataTable";
import { ConfirmDialog } from "@/components/shared/ConfirmDialog";
import { LoadingSpinner } from "@/components/shared/LoadingSpinner";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Badge } from "@/components/ui/badge";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from "@/components/ui/dialog";
import { Plus, Pencil, Trash2 } from "lucide-react";

const ROLES = [
  { value: "admin",        label: "Admin" },
  { value: "director",     label: "Giám đốc" },
  { value: "manager",      label: "Quản lý" },
  { value: "receptionist", label: "Lễ tân" },
  { value: "accountant",   label: "Kế toán" },
];

const createSchema = z.object({
  name: z.string().min(2, "Nhập họ tên"),
  email: z.email("Email không hợp lệ"),
  role: z.string().min(1, "Chọn vai trò"),
  phone: z.string().optional(),
  password: z.string().min(8, "Mật khẩu ít nhất 8 ký tự"),
});

const editSchema = z.object({
  name: z.string().min(2, "Nhập họ tên"),
  email: z.email("Email không hợp lệ"),
  role: z.string().min(1, "Chọn vai trò"),
  phone: z.string().optional(),
  password: z.string().optional(),
});

type CreateForm = z.infer<typeof createSchema>;
type EditForm = z.infer<typeof editSchema>;
type FormData = CreateForm | EditForm;

export default function StaffPage() {
  const qc = useQueryClient();
  const [open, setOpen] = useState(false);
  const [editing, setEditing] = useState<User | null>(null);
  const [page, setPage] = useState(1);
  const [selectedRole, setSelectedRole] = useState<string>("");

  const { data, isLoading } = useQuery({
    queryKey: ["admin-staff", page, selectedRole],
    queryFn: () => adminApi.staff({ page, role: selectedRole || undefined }).then((r) => r.data),
  });

  const { register, handleSubmit, reset, setValue, formState: { errors, isSubmitting } } = useForm<FormData>({
    resolver: zodResolver(editing ? editSchema : createSchema),
  });

  const upsert = useMutation({
    mutationFn: (d: FormData) =>
      editing ? adminApi.updateStaff(editing.id, d) : adminApi.createStaff(d as CreateForm),
    onSuccess: () => { qc.invalidateQueries({ queryKey: ["admin-staff"] }); closeDialog(); },
  });

  const del = useMutation({
    mutationFn: (id: number) => adminApi.deleteStaff(id),
    onSuccess: () => qc.invalidateQueries({ queryKey: ["admin-staff"] }),
  });

  const openCreate = () => { reset({}); setEditing(null); setOpen(true); };
  const openEdit = (u: User) => { reset({ name: u.name, email: u.email, role: u.role, phone: u.phone ?? "" }); setEditing(u); setOpen(true); };
  const closeDialog = () => { setOpen(false); setEditing(null); reset({}); };

  const columns = [
    { key: "name", header: "Họ tên" },
    { key: "email", header: "Email" },
    { key: "role", header: "Vai trò", render: (u: User) => <Badge variant="secondary">{ROLES.find(r => r.value === u.role)?.label ?? u.role}</Badge> },
    { key: "phone", header: "SĐT", render: (u: User) => u.phone ?? "-" },
    {
      key: "actions", header: "",
      render: (u: User) => (
        <div className="flex gap-1">
          <Button size="icon-sm" variant="ghost" onClick={() => openEdit(u)}><Pencil /></Button>
          <ConfirmDialog
            trigger={<Button size="icon-sm" variant="ghost" className="text-destructive"><Trash2 /></Button>}
            title="Xóa nhân viên"
            description={`Xóa "${u.name}"?`}
            confirmLabel="Xóa"
            onConfirm={() => del.mutate(u.id)}
          />
        </div>
      ),
    },
  ];

  return (
    <div>
      <PageHeader
        title="Nhân viên"
        action={<Button size="sm" onClick={openCreate}><Plus className="mr-1.5" />Thêm nhân viên</Button>}
      />
      <div className="mb-4">
        <Select value={selectedRole || "all"} onValueChange={(v) => { setSelectedRole(v === "all" ? "" : v); setPage(1); }}>
          <SelectTrigger className="w-44">
            <SelectValue placeholder="Lọc vai trò" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="all">Tất cả</SelectItem>
            {ROLES.map((r) => <SelectItem key={r.value} value={r.value}>{r.label}</SelectItem>)}
          </SelectContent>
        </Select>
      </div>
      {isLoading ? <LoadingSpinner /> : (
        <DataTable
          columns={columns as Parameters<typeof DataTable>[0]["columns"]}
          data={(data?.data ?? []) as Record<string, unknown>[]}
          currentPage={data?.current_page}
          lastPage={data?.last_page}
          onPageChange={setPage}
          emptyMessage="Chưa có nhân viên nào"
        />
      )}

      <Dialog open={open} onOpenChange={(v) => !v && closeDialog()}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>{editing ? "Sửa nhân viên" : "Thêm nhân viên"}</DialogTitle>
          </DialogHeader>
          <form onSubmit={handleSubmit((d) => upsert.mutate(d))} className="space-y-3">
            <div className="grid grid-cols-2 gap-3">
              <div className="space-y-1 col-span-2">
                <Label>Họ tên</Label>
                <Input {...register("name")} />
                {errors.name && <p className="text-xs text-red-500">{errors.name.message}</p>}
              </div>
              <div className="space-y-1">
                <Label>Email</Label>
                <Input {...register("email")} type="email" />
                {errors.email && <p className="text-xs text-red-500">{errors.email.message}</p>}
              </div>
              <div className="space-y-1">
                <Label>SĐT</Label>
                <Input {...register("phone")} />
              </div>
              <div className="space-y-1">
                <Label>Vai trò</Label>
                <Select defaultValue={editing?.role} onValueChange={(v) => setValue("role", v)}>
                  <SelectTrigger><SelectValue placeholder="Chọn vai trò" /></SelectTrigger>
                  <SelectContent>
                    {ROLES.map((r) => <SelectItem key={r.value} value={r.value}>{r.label}</SelectItem>)}
                  </SelectContent>
                </Select>
                {errors.role && <p className="text-xs text-red-500">{errors.role.message}</p>}
              </div>
              <div className="space-y-1">
                <Label>{editing ? "Mật khẩu mới (để trống nếu không đổi)" : "Mật khẩu"}</Label>
                <Input {...register("password")} type="password" />
                {errors.password && <p className="text-xs text-red-500">{errors.password.message}</p>}
              </div>
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
