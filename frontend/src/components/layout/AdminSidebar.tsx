"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import {
  LayoutDashboard,
  CalendarCheck,
  BedDouble,
  Hotel,
  Wrench,
  Users,
  Shield,
  MessageSquare,
} from "lucide-react";
import { cn } from "@/lib/utils";
import { useAuthStore } from "@/store/auth";

const navItems = [
  { href: "/admin/dashboard",   label: "Tổng quan",       icon: LayoutDashboard, perm: null },
  { href: "/admin/bookings",    label: "Đặt phòng",       icon: CalendarCheck,   perm: "view_reservations" },
  { href: "/admin/room-types",  label: "Loại phòng",      icon: BedDouble,       perm: "manage_rooms" },
  { href: "/admin/rooms",       label: "Phòng",           icon: Hotel,           perm: "manage_rooms" },
  { href: "/admin/services",    label: "Dịch vụ",         icon: Wrench,          perm: "manage_rooms" },
  { href: "/admin/staff",       label: "Nhân viên",       icon: Users,           perm: "manage_staff" },
  { href: "/admin/permissions", label: "Phân quyền",      icon: Shield,          perm: "manage_staff" },
  { href: "/admin/messages",    label: "Tin nhắn",        icon: MessageSquare,   perm: "view_messages" },
];

export function AdminSidebar() {
  const pathname = usePathname();
  const user = useAuthStore((s) => s.user);

  const visible = navItems.filter(
    (item) => !item.perm || user?.permissions?.includes(item.perm)
  );

  return (
    <aside className="w-56 shrink-0 min-h-screen bg-sidebar border-r border-border flex flex-col">
      <div className="px-4 py-5 border-b border-border">
        <span className="font-bold text-lg text-foreground">Hotel Manager</span>
      </div>
      <nav className="flex-1 py-3 space-y-0.5 px-2">
        {visible.map((item) => {
          const active = pathname.startsWith(item.href);
          return (
            <Link
              key={item.href}
              href={item.href}
              className={cn(
                "flex items-center gap-3 rounded-md px-3 py-2 text-sm transition-colors",
                active
                  ? "bg-primary text-primary-foreground"
                  : "text-muted-foreground hover:bg-muted hover:text-foreground"
              )}
            >
              <item.icon className="size-4 shrink-0" />
              {item.label}
            </Link>
          );
        })}
      </nav>
    </aside>
  );
}
