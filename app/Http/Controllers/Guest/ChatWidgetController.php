<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatWidgetController extends Controller
{
    private function isCustomer(Request $request): bool
    {
        $user = $request->user();

        return $user && $user->isCustomer();
    }

    public function fetch(Request $request): JsonResponse
    {
        abort_unless($this->isCustomer($request), 403);
        $conversation = ChatConversation::query()->where('user_id', $request->user()->id)->first();

        if (! $conversation) {
            return response()->json(['messages' => []]);
        }

        $conversation->messages()
            ->where('is_admin', true)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $afterId = (int) $request->query('after', 0);
        $query   = $conversation->messages()->with('sender:id,name');
        if ($afterId > 0) {
            $query->where('id', '>', $afterId);
        }

        $messages = $query->get()->map(fn (ChatMessage $m) => [
            'id'         => $m->id,
            'body'       => $m->body,
            'is_admin'   => $m->is_admin,
            // source: 'user' | 'ai' | 'admin'
            'source'     => $m->is_admin
                                ? ($m->sender_id === null ? 'ai' : 'admin')
                                : 'user',
            'created_at' => $m->created_at->toIso8601String(),
        ]);

        return response()->json(['messages' => $messages]);
    }

    public function store(Request $request): JsonResponse
    {
        abort_unless($this->isCustomer($request), 403);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $conversation = ChatConversation::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['last_message_at' => now()]
        );

        $message = $conversation->messages()->create([
            'sender_id' => $request->user()->id,
            'is_admin' => false,
            'body' => $data['body'],
        ]);

        $conversation->update(['last_message_at' => $message->created_at]);

        return response()->json([
            'message' => [
                'id' => $message->id,
                'body' => $message->body,
                'is_admin' => false,
                'created_at' => $message->created_at->toIso8601String(),
            ],
        ], 201);
    }
}
