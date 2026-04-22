"use client";

import { useEffect } from "react";
import { useRouter } from "next/navigation";
import { useAuthStore } from "@/store/auth";
import { CustomerNav } from "@/components/layout/CustomerNav";

export default function MainLayout({ children }: { children: React.ReactNode }) {
  const router = useRouter();
  const { user, token } = useAuthStore();

  useEffect(() => {
    if (!token || !user) router.replace("/login");
  }, [token, user, router]);

  if (!token || !user) return null;

  return (
    <div className="min-h-screen flex flex-col">
      <CustomerNav />
      <main className="flex-1 p-6 max-w-5xl mx-auto w-full">{children}</main>
    </div>
  );
}
