"use client";

import { useState } from "react";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { adminApi } from "@/api/admin";
import { User } from "@/types";
import { PageHeader } from "@/components/shared/PageHeader";
import { LoadingSpinner } from "@/components/shared/LoadingSpinner";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { RotateCcw } from "lucide-react";

const ROLES = [
  { value: "admin",        label: "Admin" },
  { value: "director",     label: "Giám đốc" },
  { value: "manager",      label: "Quản lý" },
  { value: "receptionist", label: "Lễ tân" },
  { value: "accountant",   label: "Kế toán" },
  { value: "customer",     label: "Khách hàng" },
];

const PERMISSIONS = [
  { key: "view_dashboard",      label: "Xem dashboard" },
  { key: "view_reservations",   label: "Xem đặt phòng" },
  { key: "manage_reservations", label: "Quản lý đặt phòng" },
  { key: "confirm_reservations",label: "Xác nhận đặt phòng" },
  { key: "checkin",             label: "Check-in" },
  { key: "checkout",            label: "Check-out" },
  { key: "view_messages",       label: "Xem tin nhắn" },
  { key: "reply_messages",      label: "Trả lời tin nhắn" },
  { key: "view_invoices",       label: "Xem hóa đơn" },
  { key: "manage_rooms",        label: "Quản lý phòng" },
  { key: "manage_services",     label: "Quản lý dịch vụ" },
  { key: "manage_staff",        label: "Quản lý nhân viên" },
  { key: "manage_settings",     label: "Cài đặt hệ thống" },
  { key: "view_reports",        label: "Xem báo cáo" },
];

interface PermissionData {
  users: User[];
  permission_labels: Record<string, string>;
}

export default function PermissionsPage() {
  const qc = useQueryClient();
  const [expandedUser, setExpandedUser] = useState<number | null>(null);

  const { data, isLoading } = useQuery<PermissionData>({
    queryKey: ["admin-permissions"],
    queryFn: () => adminApi.permissions().then((r) => r.data),
  });

  const updateRole = useMutation({
    mutationFn: ({ userId, role }: { userId: number; role: string }) =>
      adminApi.updateRole(userId, role),
    onSuccess: () => qc.invalidateQueries({ queryKey: ["admin-permissions"] }),
  });

  const updatePerms = useMutation({
    mutationFn: ({ userId, permissions }: { userId: number; permissions: string[] }) =>
      adminApi.updatePermissions(userId, permissions),
    onSuccess: () => qc.invalidateQueries({ queryKey: ["admin-permissions"] }),
  });

  const resetPerms = useMutation({
    mutationFn: (userId: number) => adminApi.resetPermissions(userId),
    onSuccess: () => qc.invalidateQueries({ queryKey: ["admin-permissions"] }),
  });

  const togglePerm = (user: User, perm: string) => {
    const current = user.permissions ?? [];
    const updated = current.includes(perm)
      ? current.filter((p) => p !== perm)
      : [...current, perm];
    updatePerms.mutate({ userId: user.id, permissions: updated });
  };

  if (isLoading) return <LoadingSpinner />;

  return (
    <div>
      <PageHeader title="Phân quyền" description="Quản lý vai trò và quyền từng nhân viên" />
      <div className="space-y-3">
        {(data?.users ?? []).map((user) => (
          <Card key={user.id}>
            <CardHeader className="py-3">
              <div className="flex items-center justify-between">
                <div className="flex items-center gap-3">
                  <button
                    className="text-left"
                    onClick={() => setExpandedUser(expandedUser === user.id ? null : user.id)}
                  >
                    <CardTitle className="text-sm font-medium">{user.name}</CardTitle>
                    <p className="text-xs text-muted-foreground">{user.email}</p>
                  </button>
                  <Badge variant="outline">{ROLES.find((r) => r.value === user.role)?.label ?? user.role}</Badge>
                </div>
                <div className="flex items-center gap-2">
                  <Select
                    defaultValue={user.role}
                    onValueChange={(v) => updateRole.mutate({ userId: user.id, role: v })}
                  >
                    <SelectTrigger className="h-7 text-xs w-36"><SelectValue /></SelectTrigger>
                    <SelectContent>
                      {ROLES.map((r) => <SelectItem key={r.value} value={r.value}>{r.label}</SelectItem>)}
                    </SelectContent>
                  </Select>
                  <Button
                    size="icon-sm"
                    variant="ghost"
                    title="Reset về quyền mặc định"
                    onClick={() => resetPerms.mutate(user.id)}
                  >
                    <RotateCcw />
                  </Button>
                </div>
              </div>
            </CardHeader>
            {expandedUser === user.id && (
              <CardContent className="pt-0">
                <div className="grid grid-cols-2 md:grid-cols-4 gap-2">
                  {PERMISSIONS.map((p) => {
                    const checked = user.permissions?.includes(p.key);
                    return (
                      <label key={p.key} className="flex items-center gap-2 text-sm cursor-pointer">
                        <input
                          type="checkbox"
                          checked={!!checked}
                          onChange={() => togglePerm(user, p.key)}
                          className="rounded border-border"
                        />
                        {p.label}
                      </label>
                    );
                  })}
                </div>
              </CardContent>
            )}
          </Card>
        ))}
      </div>
    </div>
  );
}
