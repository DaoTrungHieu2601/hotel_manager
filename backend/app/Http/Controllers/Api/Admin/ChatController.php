<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $conversations = ChatConversation::with(['user', 'messages' => fn ($q) => $q->latest()->limit(1)])
            ->orderByDesc('last_message_at')
            ->paginate(20);

        return response()->json($conversations);
    }

    public function messages(ChatConversation $conversation): JsonResponse
    {
        $messages = $conversation->messages()->with('sender')->orderBy('created_at')->get();

        return response()->json([
            'conversation' => $conversation->load('user'),
            'messages'     => $messages,
        ]);
    }

    public function reply(Request $request, ChatConversation $conversation): JsonResponse
    {
        $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => $request->user()->id,
            'is_admin'  => true,
            'body'      => $request->body,
        ]);

        $conversation->update(['last_message_at' => now()]);

        return response()->json($message->load('sender'), 201);
    }
}
