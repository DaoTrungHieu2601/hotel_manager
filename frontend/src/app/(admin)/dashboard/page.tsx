"use client";

import { useQuery } from "@tanstack/react-query";
import { adminApi } from "@/api/admin";
import { bookingsApi } from "@/api/bookings";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { PageHeader } from "@/components/shared/PageHeader";
import { LoadingSpinner } from "@/components/shared/LoadingSpinner";
import { CalendarCheck, Users, Hotel, TrendingUp, LogIn, LogOut } from "lucide-react";

export default function AdminDashboard() {
  const { data: dashboard, isLoading: loadingDash } = useQuery({
    queryKey: ["admin-dashboard"],
    queryFn: () => adminApi.dashboard().then((r) => r.data),
  });

  const { data: stats, isLoading: loadingStats } = useQuery({
    queryKey: ["admin-stats"],
    queryFn: () => bookingsApi.adminStats().then((r) => r.data),
  });

  if (loadingDash || loadingStats) return <LoadingSpinner />;

  const cards = [
    { label: "Chờ xác nhận",    value: stats?.pending ?? 0,          icon: CalendarCheck, color: "text-yellow-600" },
    { label: "Đã xác nhận",     value: stats?.confirmed ?? 0,         icon: CalendarCheck, color: "text-blue-600" },
    { label: "Đang ở",          value: stats?.checked_in ?? 0,        icon: Users,         color: "text-green-600" },
    { label: "Check-in hôm nay", value: stats?.today_checkins ?? 0,   icon: LogIn,         color: "text-indigo-600" },
    { label: "Check-out hôm nay",value: stats?.today_checkouts ?? 0,  icon: LogOut,        color: "text-orange-600" },
    { label: "Tổng số phòng",   value: dashboard?.total_rooms ?? 0,   icon: Hotel,         color: "text-slate-600" },
    { label: "Phòng trống",     value: dashboard?.available_rooms ?? 0, icon: Hotel,       color: "text-green-600" },
    { label: "Doanh thu tháng", value: `${(dashboard?.revenue_this_month ?? 0).toLocaleString("vi-VN")}đ`, icon: TrendingUp, color: "text-emerald-600" },
  ];

  return (
    <div>
      <PageHeader title="Tổng quan" description="Số liệu hoạt động của khách sạn" />
      <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
        {cards.map((card) => (
          <Card key={card.label}>
            <CardHeader className="pb-2 flex-row items-center justify-between space-y-0">
              <CardTitle className="text-sm font-medium text-muted-foreground">{card.label}</CardTitle>
              <card.icon className={`size-4 ${card.color}`} />
            </CardHeader>
            <CardContent>
              <p className="text-2xl font-bold">{card.value}</p>
            </CardContent>
          </Card>
        ))}
      </div>
    </div>
  );
}
