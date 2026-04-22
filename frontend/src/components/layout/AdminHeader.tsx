"use client";

import { useRouter } from "next/navigation";
import { LogOut, User } from "lucide-react";
import { Button } from "@/components/ui/button";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { useAuthStore } from "@/store/auth";
import { authApi } from "@/api/auth";

export function AdminHeader() {
  const router = useRouter();
  const { user, clearAuth } = useAuthStore();

  const handleLogout = async () => {
    try { await authApi.logout(); } catch { /* ignore */ }
    clearAuth();
    router.replace("/login");
  };

  return (
    <header className="h-14 border-b border-border bg-background flex items-center justify-end px-6 shrink-0">
      <DropdownMenu>
        <DropdownMenuTrigger asChild>
          <Button variant="ghost" size="sm" className="gap-2">
            <User className="size-4" />
            <span className="text-sm">{user?.name}</span>
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
          <DropdownMenuLabel className="text-xs text-muted-foreground">{user?.email}</DropdownMenuLabel>
          <DropdownMenuSeparator />
          <DropdownMenuItem onClick={handleLogout} className="text-destructive">
            <LogOut className="size-4 mr-2" />
            Đăng xuất
          </DropdownMenuItem>
        </DropdownMenuContent>
      </DropdownMenu>
    </header>
  );
}
