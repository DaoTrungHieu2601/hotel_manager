"use client";

import { useState } from "react";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { useForm } from "react-hook-form";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import { bookingsApi } from "@/api/bookings";
import { roomsApi } from "@/api/rooms";
import { adminApi } from "@/api/admin";
import { Booking, Room } from "@/types";
import { PageHeader } from "@/components/shared/PageHeader";
import { DataTable } from "@/components/shared/DataTable";
import { BookingStatusBadge } from "@/components/shared/StatusBadge";
import { LoadingSpinner } from "@/components/shared/LoadingSpinner";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from "@/components/ui/dialog";
import { LogIn, LogOut, CheckCircle, XCircle, Plus } from "lucide-react";

const checkinSchema = z.object({
  room_id: z.coerce.number().min(1, "Chọn phòng"),
  cccd: z.string().optional(),
});

const serviceSchema = z.object({
  service_id: z.coerce.number().min(1, "Chọn dịch vụ"),
  quantity: z.coerce.number().min(1),
});

type CheckinForm = z.infer<typeof checkinSchema>;
type ServiceForm = z.infer<typeof serviceSchema>;

export default function ReservationsPage() {
  const qc = useQueryClient();
  const [page, setPage] = useState(1);
  const [status, setStatus] = useState("pending");
  const [checkinBooking, setCheckinBooking] = useState<Booking | null>(null);
  const [serviceBooking, setServiceBooking] = useState<Booking | null>(null);

  const { data, isLoading } = useQuery({
    queryKey: ["reception-reservations", status, page],
    queryFn: () => bookingsApi.reservations({ status: status === "all" ? undefined : status, page }).then((r) => r.data),
  });

  const { data: availableRooms } = useQuery({
    queryKey: ["rooms-available"],
    queryFn: () => roomsApi.adminRooms({ status: "available" }).then((r) => r.data),
    enabled: !!checkinBooking,
  });

  const { data: services } = useQuery({
    queryKey: ["admin-services"],
    queryFn: () => adminApi.services().then((r) => r.data),
    enabled: !!serviceBooking,
  });

  const confirm = useMutation({
    mutationFn: (id: number) => bookingsApi.confirmReservation(id),
    onSuccess: () => qc.invalidateQueries({ queryKey: ["reception-reservations"] }),
  });

  const cancel = useMutation({
    mutationFn: (id: number) => bookingsApi.cancelReservation(id),
    onSuccess: () => qc.invalidateQueries({ queryKey: ["reception-reservations"] }),
  });

  const checkout = useMutation({
    mutationFn: (id: number) => bookingsApi.checkOut(id),
    onSuccess: () => qc.invalidateQueries({ queryKey: ["reception-reservations"] }),
  });

  const checkinForm = useForm<CheckinForm>({ resolver: zodResolver(checkinSchema) });
  const checkin = useMutation({
    mutationFn: (d: CheckinForm) => bookingsApi.checkIn(checkinBooking!.id, { room_id: d.room_id, cccd: d.cccd }),
    onSuccess: () => { setCheckinBooking(null); checkinForm.reset(); qc.invalidateQueries({ queryKey: ["reception-reservations"] }); },
  });

  const serviceForm = useForm<ServiceForm>({ resolver: zodResolver(serviceSchema), defaultValues: { quantity: 1 } });
  const addSvc = useMutation({
    mutationFn: (d: ServiceForm) => bookingsApi.addService(serviceBooking!.id, d),
    onSuccess: () => { setServiceBooking(null); serviceForm.reset({ quantity: 1 }); qc.invalidateQueries({ queryKey: ["reception-reservations"] }); },
  });

  const columns = [
    { key: "id", header: "#", render: (r: Booking) => `#${r.id}` },
    { key: "user", header: "Khách", render: (r: Booking) => <div><p className="text-sm">{r.user?.name}</p><p className="text-xs text-muted-foreground">{r.user?.email}</p></div> },
    { key: "room_type", header: "Loại phòng", render: (r: Booking) => r.room_type?.name ?? "-" },
    { key: "room", header: "Phòng", render: (r: Booking) => r.room?.code ?? "-" },
    { key: "check_in", header: "Check-in" },
    { key: "check_out", header: "Check-out" },
    { key: "guests", header: "Khách" },
    { key: "status", header: "TT", render: (r: Booking) => <BookingStatusBadge status={r.status} /> },
    {
      key: "actions", header: "Thao tác",
      render: (r: Booking) => (
        <div className="flex gap-1 flex-wrap">
          {r.status === "pending" && (
            <Button size="xs" onClick={() => confirm.mutate(r.id)}>
              <CheckCircle className="mr-1" />Xác nhận
            </Button>
          )}
          {r.status === "confirmed" && (
            <Button size="xs" onClick={() => setCheckinBooking(r)}>
              <LogIn className="mr-1" />Check-in
            </Button>
          )}
          {r.status === "checked_in" && (
            <>
              <Button size="xs" variant="outline" onClick={() => setServiceBooking(r)}>
                <Plus className="mr-1" />DV
              </Button>
              <Button size="xs" onClick={() => checkout.mutate(r.id)}>
                <LogOut className="mr-1" />Check-out
              </Button>
            </>
          )}
          {(r.status === "pending" || r.status === "confirmed") && (
            <Button size="xs" variant="destructive" onClick={() => cancel.mutate(r.id)}>
              <XCircle className="mr-1" />Hủy
            </Button>
          )}
        </div>
      ),
    },
  ];

  return (
    <div>
      <PageHeader title="Đặt phòng" description="Quản lý check-in / check-out" />
      <div className="mb-4">
        <Select value={status} onValueChange={(v) => { setStatus(v); setPage(1); }}>
          <SelectTrigger className="w-44">
            <SelectValue />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="all">Tất cả</SelectItem>
            <SelectItem value="pending">Chờ xác nhận</SelectItem>
            <SelectItem value="confirmed">Đã xác nhận</SelectItem>
            <SelectItem value="checked_in">Đang ở</SelectItem>
            <SelectItem value="checked_out">Đã trả phòng</SelectItem>
            <SelectItem value="cancelled">Đã hủy</SelectItem>
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
          emptyMessage="Không có đặt phòng nào"
        />
      )}

      {/* Check-in dialog */}
      <Dialog open={!!checkinBooking} onOpenChange={(v) => !v && setCheckinBooking(null)}>
        <DialogContent>
          <DialogHeader><DialogTitle>Check-in — #{checkinBooking?.id}</DialogTitle></DialogHeader>
          <form onSubmit={checkinForm.handleSubmit((d) => checkin.mutate(d))} className="space-y-3">
            <div className="space-y-1">
              <Label>Chọn phòng</Label>
              <Select onValueChange={(v) => checkinForm.setValue("room_id", Number(v))}>
                <SelectTrigger><SelectValue placeholder="Phòng trống" /></SelectTrigger>
                <SelectContent>
                  {(availableRooms ?? []).map((r: Room) => (
                    <SelectItem key={r.id} value={r.id.toString()}>
                      {r.code} — Tầng {r.floor} — {r.room_type?.name}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
              {checkinForm.formState.errors.room_id && <p className="text-xs text-red-500">{checkinForm.formState.errors.room_id.message}</p>}
            </div>
            <div className="space-y-1">
              <Label>CCCD / Hộ chiếu (tùy chọn)</Label>
              <Input {...checkinForm.register("cccd")} />
            </div>
            <DialogFooter>
              <Button variant="outline" type="button" onClick={() => setCheckinBooking(null)}>Hủy</Button>
              <Button type="submit" disabled={checkin.isPending}>Check-in</Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>

      {/* Add service dialog */}
      <Dialog open={!!serviceBooking} onOpenChange={(v) => !v && setServiceBooking(null)}>
        <DialogContent>
          <DialogHeader><DialogTitle>Thêm dịch vụ — #{serviceBooking?.id}</DialogTitle></DialogHeader>
          <form onSubmit={serviceForm.handleSubmit((d) => addSvc.mutate(d))} className="space-y-3">
            <div className="space-y-1">
              <Label>Dịch vụ</Label>
              <Select onValueChange={(v) => serviceForm.setValue("service_id", Number(v))}>
                <SelectTrigger><SelectValue placeholder="Chọn dịch vụ" /></SelectTrigger>
                <SelectContent>
                  {(services ?? []).filter((s) => s.is_active).map((s) => (
                    <SelectItem key={s.id} value={s.id.toString()}>
                      {s.name} — {s.price.toLocaleString("vi-VN")}đ
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-1">
              <Label>Số lượng</Label>
              <Input {...serviceForm.register("quantity")} type="number" min={1} />
            </div>
            <DialogFooter>
              <Button variant="outline" type="button" onClick={() => setServiceBooking(null)}>Hủy</Button>
              <Button type="submit" disabled={addSvc.isPending}>Thêm</Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>
    </div>
  );
}
