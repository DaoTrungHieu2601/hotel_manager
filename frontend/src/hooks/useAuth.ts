"use client";

import { useEffect } from "react";
import { useRouter } from "next/navigation";
import { useAuthStore } from "@/store/auth";

type Portal = "admin" | "reception" | "customer";

export function useAuth(required?: Portal) {
  const router = useRouter();
  const { user, token } = useAuthStore();

  useEffect(() => {
    if (!token || !user) {
      router.replace("/login");
      return;
    }
    if (required === "admin" && !user.can_access_admin) {
      router.replace("/login");
    }
    if (required === "reception" && !user.can_access_reception) {
      router.replace("/login");
    }
    if (required === "customer" && !user.is_customer) {
      router.replace("/login");
    }
  }, [token, user, required, router]);

  return { user, token };
}
