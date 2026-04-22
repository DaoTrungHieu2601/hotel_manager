"use client";

import { useEffect } from "react";
import { useRouter } from "next/navigation";
import { useAuthStore } from "@/store/auth";
import { ReceptionSidebar } from "@/components/layout/ReceptionSidebar";

export default function ReceptionLayout({ children }: { children: React.ReactNode }) {
  const router = useRouter();
  const { user, token } = useAuthStore();

  useEffect(() => {
    if (!token || !user) { router.replace("/login"); return; }
    if (!user.can_access_reception) router.replace("/login");
  }, [token, user, router]);

  if (!token || !user?.can_access_reception) return null;

  return (
    <div className="flex min-h-screen">
      <ReceptionSidebar />
      <main className="flex-1 p-6 bg-muted/30">{children}</main>
    </div>
  );
}
