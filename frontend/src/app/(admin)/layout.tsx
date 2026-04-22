"use client";

import { useEffect } from "react";
import { useRouter } from "next/navigation";
import { useAuthStore } from "@/store/auth";
import { AdminSidebar } from "@/components/layout/AdminSidebar";
import { AdminHeader } from "@/components/layout/AdminHeader";

export default function AdminLayout({ children }: { children: React.ReactNode }) {
  const router = useRouter();
  const { user, token } = useAuthStore();

  useEffect(() => {
    if (!token || !user) { router.replace("/login"); return; }
    if (!user.can_access_admin) router.replace("/login");
  }, [token, user, router]);

  if (!token || !user?.can_access_admin) return null;

  return (
    <div className="flex min-h-screen">
      <AdminSidebar />
      <div className="flex flex-col flex-1 min-w-0">
        <AdminHeader />
        <main className="flex-1 p-6 bg-muted/30">{children}</main>
      </div>
    </div>
  );
}
