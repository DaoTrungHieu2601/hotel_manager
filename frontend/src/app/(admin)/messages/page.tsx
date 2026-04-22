"use client";

import { useState, useRef, useEffect } from "react";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { adminApi } from "@/api/admin";
import { ChatConversation } from "@/types";
import { PageHeader } from "@/components/shared/PageHeader";
import { LoadingSpinner } from "@/components/shared/LoadingSpinner";
import { Button } from "@/components/ui/button";
import { Textarea } from "@/components/ui/textarea";
import { cn } from "@/lib/utils";
import { Send, MessageSquare } from "lucide-react";
import { useAuthStore } from "@/store/auth";

export default function MessagesPage() {
  const qc = useQueryClient();
  const user = useAuthStore((s) => s.user);
  const [selectedConv, setSelectedConv] = useState<ChatConversation | null>(null);
  const [reply, setReply] = useState("");
  const bottomRef = useRef<HTMLDivElement>(null);

  const { data: convs, isLoading } = useQuery({
    queryKey: ["admin-conversations"],
    queryFn: () => adminApi.conversations().then((r) => r.data),
    refetchInterval: 10000,
  });

  const { data: thread, isLoading: loadingThread } = useQuery({
    queryKey: ["admin-messages", selectedConv?.id],
    queryFn: () => selectedConv ? adminApi.conversationMessages(selectedConv.id).then((r) => r.data) : null,
    enabled: !!selectedConv,
    refetchInterval: 5000,
  });

  const send = useMutation({
    mutationFn: () => adminApi.replyMessage(selectedConv!.id, reply),
    onSuccess: () => {
      setReply("");
      qc.invalidateQueries({ queryKey: ["admin-messages", selectedConv?.id] });
      qc.invalidateQueries({ queryKey: ["admin-conversations"] });
    },
  });

  useEffect(() => {
    bottomRef.current?.scrollIntoView({ behavior: "smooth" });
  }, [thread?.messages]);

  return (
    <div>
      <PageHeader title="Tin nhắn" description="Chat với khách hàng" />
      <div className="flex gap-4 h-[calc(100vh-200px)]">
        {/* Conversation list */}
        <div className="w-72 shrink-0 border border-border rounded-lg overflow-y-auto">
          {isLoading ? <LoadingSpinner className="py-6" /> : (
            (convs?.data ?? []).length === 0 ? (
              <div className="flex flex-col items-center justify-center h-full text-muted-foreground gap-2">
                <MessageSquare className="size-8" />
                <p className="text-sm">Chưa có tin nhắn</p>
              </div>
            ) : (
              (convs?.data ?? []).map((conv) => (
                <button
                  key={conv.id}
                  onClick={() => setSelectedConv(conv)}
                  className={cn(
                    "w-full text-left px-4 py-3 border-b border-border transition-colors",
                    selectedConv?.id === conv.id ? "bg-muted" : "hover:bg-muted/50"
                  )}
                >
                  <p className="text-sm font-medium truncate">{conv.user?.name ?? "Khách"}</p>
                  <p className="text-xs text-muted-foreground truncate">{conv.user?.email ?? "-"}</p>
                </button>
              ))
            )
          )}
        </div>

        {/* Message thread */}
        <div className="flex-1 border border-border rounded-lg flex flex-col overflow-hidden">
          {!selectedConv ? (
            <div className="flex-1 flex items-center justify-center text-muted-foreground">
              <p className="text-sm">Chọn cuộc trò chuyện</p>
            </div>
          ) : (
            <>
              <div className="px-4 py-3 border-b border-border bg-muted/30">
                <p className="font-medium text-sm">{selectedConv.user?.name ?? "Khách"}</p>
                <p className="text-xs text-muted-foreground">{selectedConv.user?.email}</p>
              </div>
              <div className="flex-1 overflow-y-auto p-4 space-y-3">
                {loadingThread ? <LoadingSpinner /> : (
                  (thread?.messages ?? []).map((msg) => {
                    const isMe = msg.is_admin;
                    return (
                      <div key={msg.id} className={cn("flex", isMe ? "justify-end" : "justify-start")}>
                        <div className={cn(
                          "max-w-[70%] rounded-2xl px-4 py-2 text-sm",
                          isMe ? "bg-primary text-primary-foreground rounded-br-sm" : "bg-muted rounded-bl-sm"
                        )}>
                          <p>{msg.body}</p>
                          <p className={cn("text-[10px] mt-1", isMe ? "text-primary-foreground/70" : "text-muted-foreground")}>
                            {new Date(msg.created_at).toLocaleTimeString("vi-VN", { hour: "2-digit", minute: "2-digit" })}
                          </p>
                        </div>
                      </div>
                    );
                  })
                )}
                <div ref={bottomRef} />
              </div>
              <div className="p-3 border-t border-border flex gap-2">
                <Textarea
                  value={reply}
                  onChange={(e) => setReply(e.target.value)}
                  placeholder="Nhập tin nhắn..."
                  rows={1}
                  className="resize-none"
                  onKeyDown={(e) => {
                    if (e.key === "Enter" && !e.shiftKey) { e.preventDefault(); reply.trim() && send.mutate(); }
                  }}
                />
                <Button
                  size="icon"
                  disabled={!reply.trim() || send.isPending}
                  onClick={() => send.mutate()}
                >
                  <Send />
                </Button>
              </div>
            </>
          )}
        </div>
      </div>
    </div>
  );
}
