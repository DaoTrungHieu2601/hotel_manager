"use client";

import Link from "next/link";
import { usePathname, useRouter } from "next/navigation";
import { CalendarCheck, Hotel, LogOut } from "lucide-react";
import { cn } from "@/lib/utils";
import { useAuthStore } from "@/store/auth";
import { authApi } from "@/api/auth";

const navItems = [
  { href: "/reception/reservations", label: "Đặt phòng",  icon: CalendarCheck },
  { href: "/reception/rooms",        label: "Phòng",      icon: Hotel },
];

export function ReceptionSidebar() {
  const pathname = usePathname();
  const router = useRouter();
  const { user, clearAuth } = useAuthStore();

  const handleLogout = async () => {
    try { await authApi.logout(); } catch { /* ignore */ }
    clearAuth();
    router.replace("/login");
  };

  return (
    <aside className="w-56 shrink-0 min-h-screen bg-sidebar border-r border-border flex flex-col">
      <div className="px-4 py-5 border-b border-border">
        <span className="font-bold text-base text-foreground">Lễ tân</span>
        <p className="text-xs text-muted-foreground truncate">{user?.name}</p>
      </div>
      <nav className="flex-1 py-3 space-y-0.5 px-2">
        {navItems.map((item) => {
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
      <div className="p-2 border-t border-border">
        <button
          onClick={handleLogout}
          className="flex w-full items-center gap-3 rounded-md px-3 py-2 text-sm text-muted-foreground hover:bg-muted hover:text-foreground transition-colors"
        >
          <LogOut className="size-4" />
          Đăng xuất
        </button>
      </div>
    </aside>
  );
}
