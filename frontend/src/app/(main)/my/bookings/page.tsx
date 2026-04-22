"use client";

import { useState } from "react";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import Link from "next/link";
import { bookingsApi } from "@/api/bookings";
import { Booking } from "@/types";
import { PageHeader } from "@/components/shared/PageHeader";
import { BookingStatusBadge } from "@/components/shared/StatusBadge";
import { LoadingSpinner } from "@/components/shared/LoadingSpinner";
import { ConfirmDialog } from "@/components/shared/ConfirmDialog";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader } from "@/components/ui/card";
import { ChevronLeft, ChevronRight, Eye, XCircle } from "lucide-react";

export default function MyBookingsPage() {
  const qc = useQueryClient();
  const [page, setPage] = useState(1);

  const { data, isLoading } = useQuery({
    queryKey: ["my-bookings", page],
    queryFn: () => bookingsApi.myBookings(page).then((r) => r.data),
  });

  const cancel = useMutation({
    mutationFn: (id: number) => bookingsApi.cancelBooking(id),
    onSuccess: () => qc.invalidateQueries({ queryKey: ["my-bookings"] }),
  });

  if (isLoading) return <LoadingSpinner />;

  const bookings = data?.data ?? [];

  return (
    <div>
      <PageHeader title="Đặt phòng của tôi" />
      {bookings.length === 0 ? (
        <div className="text-center py-16 text-muted-foreground">
          <p className="mb-4">Bạn chưa có đặt phòng nào.</p>
          <Button asChild><Link href="/search">Tìm phòng ngay</Link></Button>
        </div>
      ) : (
        <div className="space-y-3">
          {bookings.map((b: Booking) => (
            <Card key={b.id}>
              <CardHeader className="pb-2">
                <div className="flex items-center justify-between flex-wrap gap-2">
                  <div>
                    <p className="font-semibold">{b.room_type?.name ?? `Đặt phòng #${b.id}`}</p>
                    <p className="text-sm text-muted-foreground">
                      {b.check_in} → {b.check_out} · {b.guests} khách
                    </p>
                  </div>
                  <BookingStatusBadge status={b.status} />
                </div>
              </CardHeader>
              <CardContent className="flex gap-2 flex-wrap">
                <Button asChild size="sm" variant="outline">
                  <Link href={`/my/bookings/${b.id}`}><Eye className="mr-1.5" />Chi tiết</Link>
                </Button>
                {(b.status === "pending" || b.status === "draft") && (
                  <ConfirmDialog
                    trigger={
                      <Button size="sm" variant="destructive">
                        <XCircle className="mr-1.5" />Hủy đặt phòng
                      </Button>
                    }
                    title="Hủy đặt phòng"
                    description="Bạn có chắc muốn hủy đặt phòng này không?"
                    confirmLabel="Hủy đặt phòng"
                    onConfirm={() => cancel.mutate(b.id)}
                  />
                )}
              </CardContent>
            </Card>
          ))}
          {(data?.last_page ?? 1) > 1 && (
            <div className="flex items-center justify-center gap-2 pt-2">
              <Button variant="outline" size="icon-sm" disabled={page <= 1} onClick={() => setPage(page - 1)}>
                <ChevronLeft />
              </Button>
              <span className="text-sm text-muted-foreground">{page} / {data?.last_page}</span>
              <Button variant="outline" size="icon-sm" disabled={page >= (data?.last_page ?? 1)} onClick={() => setPage(page + 1)}>
                <ChevronRight />
              </Button>
            </div>
          )}
        </div>
      )}
    </div>
  );
}
