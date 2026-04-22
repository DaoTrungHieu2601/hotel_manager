"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import Link from "next/link";
import { useForm } from "react-hook-form";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { Label } from "@/components/ui/label";
import { authApi } from "@/api/auth";
import { useAuthStore } from "@/store/auth";

const schema = z.object({
  name: z.string().min(2, "Tên phải có ít nhất 2 ký tự"),
  email: z.email("Email không hợp lệ"),
  password: z.string().min(8, "Mật khẩu phải có ít nhất 8 ký tự"),
  password_confirmation: z.string(),
}).refine((d) => d.password === d.password_confirmation, {
  message: "Mật khẩu không khớp",
  path: ["password_confirmation"],
});

type FormData = z.infer<typeof schema>;

export default function RegisterPage() {
  const router = useRouter();
  const setAuth = useAuthStore((s) => s.setAuth);
  const [error, setError] = useState("");

  const { register, handleSubmit, formState: { errors, isSubmitting } } = useForm<FormData>({
    resolver: zodResolver(schema),
  });

  const onSubmit = async (data: FormData) => {
    try {
      setError("");
      const res = await authApi.register(data);
      setAuth(res.data.user, res.data.token);
      router.push("/my/bookings");
    } catch {
      setError("Đăng ký thất bại. Email có thể đã được sử dụng.");
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-50">
      <Card className="w-full max-w-md">
        <CardHeader>
          <CardTitle className="text-2xl text-center">Đăng ký tài khoản</CardTitle>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
            {error && <p className="text-sm text-red-600 bg-red-50 p-3 rounded">{error}</p>}
            <div className="space-y-1">
              <Label>Họ tên</Label>
              <Input {...register("name")} placeholder="Nguyễn Văn A" />
              {errors.name && <p className="text-xs text-red-500">{errors.name.message}</p>}
            </div>
            <div className="space-y-1">
              <Label>Email</Label>
              <Input {...register("email")} type="email" placeholder="email@example.com" />
              {errors.email && <p className="text-xs text-red-500">{errors.email.message}</p>}
            </div>
            <div className="space-y-1">
              <Label>Mật khẩu</Label>
              <Input {...register("password")} type="password" placeholder="••••••••" />
              {errors.password && <p className="text-xs text-red-500">{errors.password.message}</p>}
            </div>
            <div className="space-y-1">
              <Label>Xác nhận mật khẩu</Label>
              <Input {...register("password_confirmation")} type="password" placeholder="••••••••" />
              {errors.password_confirmation && <p className="text-xs text-red-500">{errors.password_confirmation.message}</p>}
            </div>
            <Button type="submit" className="w-full" disabled={isSubmitting}>
              {isSubmitting ? "Đang đăng ký..." : "Đăng ký"}
            </Button>
            <p className="text-center text-sm text-muted-foreground">
              Đã có tài khoản?{" "}
              <Link href="/login" className="text-primary hover:underline">Đăng nhập</Link>
            </p>
          </form>
        </CardContent>
      </Card>
    </div>
  );
}
