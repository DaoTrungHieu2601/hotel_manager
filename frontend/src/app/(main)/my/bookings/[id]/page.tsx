"use client";

import { use } from "react";
import { useQuery } from "@tanstack/react-query";
import Link from "next/link";
import { bookingsApi } from "@/api/bookings";
import { PageHeader } from "@/components/shared/PageHeader";
import { BookingStatusBadge } from "@/components/shared/StatusBadge";
import { LoadingSpinner } from "@/components/shared/LoadingSpinner";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { ArrowLeft } from "lucide-react";

export default function BookingDetailPage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = use(params);

  const { data: booking, isLoading } = useQuery({
    queryKey: ["my-booking", id],
    queryFn: () => bookingsApi.myBooking(Number(id)).then((r) => r.data),
  });

  if (isLoading) return <LoadingSpinner />;
  if (!booking) return <p className="text-center py-12 text-muted-foreground">Không tìm thấy đặt phòng.</p>;

  const nights = Math.max(0, Math.ceil(
    (new Date(booking.check_out).getTime() - new Date(booking.check_in).getTime()) / 86400000
  ));

  return (
    <div>
      <PageHeader
        title={`Đặt phòng #${booking.id}`}
        action={
          <Button asChild variant="outline" size="sm">
            <Link href="/my/bookings"><ArrowLeft className="mr-1.5" />Quay lại</Link>
          </Button>
        }
      />

      <div className="grid md:grid-cols-2 gap-4">
        {/* Booking info */}
        <Card>
          <CardHeader><CardTitle className="text-base">Thông tin đặt phòng</CardTitle></CardHeader>
          <CardContent className="space-y-2 text-sm">
            <div className="flex justify-between">
              <span className="text-muted-foreground">Trạng thái</span>
              <BookingStatusBadge status={booking.status} />
            </div>
            <div className="flex justify-between">
              <span className="text-muted-foreground">Loại phòng</span>
              <span className="font-medium">{booking.room_type?.name ?? "-"}</span>
            </div>
            {booking.room && (
              <div className="flex justify-between">
                <span className="text-muted-foreground">Phòng</span>
                <span className="font-medium">{booking.room.code}</span>
              </div>
            )}
            <div className="flex justify-between">
              <span className="text-muted-foreground">Check-in</span>
              <span>{booking.check_in}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-muted-foreground">Check-out</span>
              <span>{booking.check_out}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-muted-foreground">Số đêm</span>
              <span>{nights}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-muted-foreground">Số khách</span>
              <span>{booking.guests}</span>
            </div>
            {booking.guest_notes && (
              <div className="flex justify-between">
                <span className="text-muted-foreground">Ghi chú</span>
                <span className="text-right max-w-[60%]">{booking.guest_notes}</span>
              </div>
            )}
          </CardContent>
        </Card>

        {/* Financial info */}
        <Card>
          <CardHeader><CardTitle className="text-base">Chi phí</CardTitle></CardHeader>
          <CardContent className="space-y-2 text-sm">
            <div className="flex justify-between">
              <span className="text-muted-foreground">Giá/đêm</span>
              <span>{booking.rate_per_night.toLocaleString("vi-VN")}đ</span>
            </div>
            <div className="flex justify-between">
              <span className="text-muted-foreground">Tiền phòng</span>
              <span>{(booking.rate_per_night * nights).toLocaleString("vi-VN")}đ</span>
            </div>
            {(booking.services ?? []).length > 0 && (
              <>
                <div className="border-t border-border pt-2 mt-2">
                  <p className="text-muted-foreground mb-1">Dịch vụ</p>
                  {(booking.services ?? []).map((s) => (
                    <div key={s.id} className="flex justify-between">
                      <span>{s.name}</span>
                      <span>{s.price.toLocaleString("vi-VN")}đ</span>
                    </div>
                  ))}
                </div>
              </>
            )}
            {booking.deposit_amount > 0 && (
              <div className="flex justify-between text-green-600">
                <span>Đã cọc</span>
                <span>-{booking.deposit_amount.toLocaleString("vi-VN")}đ</span>
              </div>
            )}
            {booking.invoice && (
              <div className="border-t border-border pt-2 mt-2">
                <div className="flex justify-between font-bold">
                  <span>Tổng thanh toán</span>
                  <span className="text-primary">{booking.invoice.total.toLocaleString("vi-VN")}đ</span>
                </div>
                <p className="text-xs text-muted-foreground mt-1">Hóa đơn: {booking.invoice.invoice_number}</p>
              </div>
            )}
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
