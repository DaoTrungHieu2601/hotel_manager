import { cn } from "@/lib/utils";

export function LoadingSpinner({ className }: { className?: string }) {
  return (
    <div className={cn("flex items-center justify-center py-12", className)}>
      <div className="size-8 animate-spin rounded-full border-4 border-muted border-t-primary" />
    </div>
  );
}
