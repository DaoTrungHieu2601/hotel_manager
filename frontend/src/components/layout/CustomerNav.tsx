"use client";

import Link from "next/link";
import { usePathname, useRouter } from "next/navigation";
import { Hotel, CalendarCheck, LogOut, User } from "lucide-react";
import { cn } from "@/lib/utils";
import { useAuthStore } from "@/store/auth";
import { authApi } from "@/api/auth";
import { Button } from "@/components/ui/button";

const navLinks = [
  { href: "/search",      label: "Tìm phòng",   icon: Hotel },
  { href: "/my/bookings", label: "Đặt phòng của tôi", icon: CalendarCheck },
];

export function CustomerNav() {
  const pathname = usePathname();
  const router = useRouter();
  const { user, clearAuth } = useAuthStore();

  const handleLogout = async () => {
    try { await authApi.logout(); } catch { /* ignore */ }
    clearAuth();
    router.replace("/login");
  };

  return (
    <header className="h-14 border-b border-border bg-background flex items-center px-6 gap-6">
      <span className="font-bold text-foreground mr-2">Hotel Manager</span>
      <nav className="flex items-center gap-1 flex-1">
        {navLinks.map((link) => (
          <Link
            key={link.href}
            href={link.href}
            className={cn(
              "flex items-center gap-2 rounded-md px-3 py-1.5 text-sm transition-colors",
              pathname.startsWith(link.href)
                ? "bg-primary text-primary-foreground"
                : "text-muted-foreground hover:bg-muted hover:text-foreground"
            )}
          >
            <link.icon className="size-4" />
            {link.label}
          </Link>
        ))}
      </nav>
      <div className="flex items-center gap-3">
        <span className="text-sm text-muted-foreground flex items-center gap-1.5">
          <User className="size-4" />
          {user?.name}
        </span>
        <Button variant="ghost" size="sm" onClick={handleLogout}>
          <LogOut className="size-4 mr-1.5" />
          Đăng xuất
        </Button>
      </div>
    </header>
  );
}
