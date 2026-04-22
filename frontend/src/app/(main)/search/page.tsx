"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import { useQuery, useMutation } from "@tanstack/react-query";
import { useForm } from "react-hook-form";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import { roomsApi } from "@/api/rooms";
import { bookingsApi } from "@/api/bookings";
import { RoomType } from "@/types";
import { PageHeader } from "@/components/shared/PageHeader";
import { LoadingSpinner } from "@/components/shared/LoadingSpinner";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from "@/components/ui/dialog";
import { Search, BedDouble, Users } from "lucide-react";

const searchSchema = z.object({
  check_in: z.string().min(1, "Chọn ngày check-in"),
  check_out: z.string().min(1, "Chọn ngày check-out"),
  guests: z.coerce.number().min(1),
});

const bookSchema = z.object({
  guest_notes: z.string().optional(),
});

type SearchForm = z.infer<typeof searchSchema>;
type BookForm = z.infer<typeof bookSchema>;

export default function SearchPage() {
  const router = useRouter();
  const [searchParams, setSearchParams] = useState<SearchForm | null>(null);
  const [bookingRoomType, setBookingRoomType] = useState<RoomType | null>(null);
  const [bookError, setBookError] = useState("");

  const searchForm = useForm<SearchForm>({
    resolver: zodResolver(searchSchema),
    defaultValues: { guests: 1 },
  });

  const bookForm = useForm<BookForm>({ resolver: zodResolver(bookSchema) });

  const { data: results, isLoading, isFetching } = useQuery({
    queryKey: ["search-rooms", searchParams],
    queryFn: () => searchParams ? roomsApi.searchRooms(searchParams).then((r) => r.data) : null,
    enabled: !!searchParams,
  });

  const createBooking = useMutation({
    mutationFn: (d: BookForm) =>
      bookingsApi.createBooking({
        room_type_id: bookingRoomType!.id,
        check_in: searchParams!.check_in,
        check_out: searchParams!.check_out,
        guests: searchParams!.guests,
        guest_notes: d.guest_notes,
      }),
    onSuccess: () => {
      router.push("/my/bookings");
    },
    onError: () => setBookError("Đặt phòng thất bại. Vui lòng thử lại."),
  });

  const onSearch = (data: SearchForm) => setSearchParams(data);

  const nights = searchParams
    ? Math.max(0, Math.ceil((new Date(searchParams.check_out).getTime() - new Date(searchParams.check_in).getTime()) / 86400000))
    : 0;

  return (
    <div>
      <PageHeader title="Tìm phòng" description="Chọn ngày và số khách để xem phòng trống" />

      {/* Search form */}
      <Card className="mb-6">
        <CardContent className="pt-5">
          <form onSubmit={searchForm.handleSubmit(onSearch)} className="flex flex-wrap gap-4 items-end">
            <div className="space-y-1">
              <Label>Ngày check-in</Label>
              <Input {...searchForm.register("check_in")} type="date" className="w-40" />
              {searchForm.formState.errors.check_in && <p className="text-xs text-red-500">{searchForm.formState.errors.check_in.message}</p>}
            </div>
            <div className="space-y-1">
              <Label>Ngày check-out</Label>
              <Input {...searchForm.register("check_out")} type="date" className="w-40" />
              {searchForm.formState.errors.check_out && <p className="text-xs text-red-500">{searchForm.formState.errors.check_out.message}</p>}
            </div>
            <div className="space-y-1">
              <Label>Số khách</Label>
              <Input {...searchForm.register("guests")} type="number" min={1} className="w-24" />
            </div>
            <Button type="submit" disabled={isFetching}>
              <Search className="mr-1.5" />
              {isFetching ? "Đang tìm..." : "Tìm kiếm"}
            </Button>
          </form>
        </CardContent>
      </Card>

      {/* Results */}
      {isLoading && <LoadingSpinner />}
      {results && results.length === 0 && (
        <p className="text-center text-muted-foreground py-12">Không có phòng trống cho lịch này.</p>
      )}
      {results && results.length > 0 && (
        <div className="grid md:grid-cols-2 gap-4">
          {results.map((rt: RoomType) => (
            <Card key={rt.id} className="overflow-hidden">
              <CardHeader>
                <div className="flex items-center justify-between">
                  <CardTitle className="text-lg">{rt.name}</CardTitle>
                  <p className="text-lg font-bold text-primary">{rt.default_price.toLocaleString("vi-VN")}đ/đêm</p>
                </div>
                <div className="flex gap-4 text-sm text-muted-foreground">
                  <span className="flex items-center gap-1"><BedDouble className="size-4" />{rt.beds} giường</span>
                  <span className="flex items-center gap-1"><Users className="size-4" />Tối đa {rt.max_occupancy} khách</span>
                </div>
              </CardHeader>
              <CardContent>
                {rt.description && <p className="text-sm text-muted-foreground mb-3">{rt.description}</p>}
                {nights > 0 && (
                  <p className="text-sm mb-3">
                    <span className="font-medium">{nights} đêm</span>
                    {" — "}
                    Tổng: <span className="font-bold text-primary">{(rt.default_price * nights).toLocaleString("vi-VN")}đ</span>
                  </p>
                )}
                <Button className="w-full" onClick={() => { setBookingRoomType(rt); setBookError(""); bookForm.reset(); }}>
                  Đặt phòng
                </Button>
              </CardContent>
            </Card>
          ))}
        </div>
      )}

      {/* Booking dialog */}
      <Dialog open={!!bookingRoomType} onOpenChange={(v) => !v && setBookingRoomType(null)}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Xác nhận đặt phòng</DialogTitle>
          </DialogHeader>
          {bookingRoomType && searchParams && (
            <div className="space-y-4">
              <div className="rounded-lg bg-muted p-3 text-sm space-y-1">
                <p><span className="font-medium">Loại phòng:</span> {bookingRoomType.name}</p>
                <p><span className="font-medium">Check-in:</span> {searchParams.check_in}</p>
                <p><span className="font-medium">Check-out:</span> {searchParams.check_out}</p>
                <p><span className="font-medium">Số khách:</span> {searchParams.guests}</p>
                <p><span className="font-medium">Tổng dự kiến:</span> {(bookingRoomType.default_price * nights).toLocaleString("vi-VN")}đ</p>
              </div>
              <form onSubmit={bookForm.handleSubmit((d) => createBooking.mutate(d))} className="space-y-3">
                {bookError && <p className="text-sm text-red-600 bg-red-50 p-2 rounded">{bookError}</p>}
                <div className="space-y-1">
                  <Label>Ghi chú (tùy chọn)</Label>
                  <Input {...bookForm.register("guest_notes")} placeholder="Yêu cầu đặc biệt..." />
                </div>
                <DialogFooter>
                  <Button variant="outline" type="button" onClick={() => setBookingRoomType(null)}>Hủy</Button>
                  <Button type="submit" disabled={createBooking.isPending}>
                    {createBooking.isPending ? "Đang đặt..." : "Đặt phòng"}
                  </Button>
                </DialogFooter>
              </form>
            </div>
          )}
        </DialogContent>
      </Dialog>
    </div>
  );
}
