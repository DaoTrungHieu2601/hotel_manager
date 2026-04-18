<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(Request $request): View
    {
        $conversations = ChatConversation::query()
            ->with(['user:id,name,email'])
            ->withCount([
                'messages as unread_from_customer_count' => function ($q) {
                    $q->where('is_admin', false)->whereNull('read_at');
                },
            ])
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->get();

        $selected = $conversations->firstWhere('id', (int) $request->query('conversation'))
            ?? $conversations->first();

        $messages = collect();
        if ($selected) {
            $selected->messages()
                ->where('is_admin', false)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            $messages = $selected->messages()
                ->with('sender:id,name')
                ->get();
        }

        return view('admin.messages.index', [
            'conversations' => $conversations,
            'selected' => $selected,
            'messages' => $messages,
        ]);
    }

    public function messages(Request $request, ChatConversation $conversation): \Illuminate\Http\JsonResponse
    {
        $afterId = (int) $request->query('after', 0);

        $conversation->messages()
            ->where('is_admin', false)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $query = $conversation->messages()->orderBy('id');
        if ($afterId > 0) {
            $query->where('id', '>', $afterId);
        }

        $messages = $query->get()->map(fn (ChatMessage $m) => [
            'id'         => $m->id,
            'body'       => $m->body,
            'is_admin'   => $m->is_admin,
            'sender_id'  => $m->sender_id,
            'created_at' => $m->created_at->format('d/m H:i'),
        ]);

        return response()->json(['messages' => $messages]);
    }

    public function reply(Request $request, ChatConversation $conversation): RedirectResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $admin = $request->user();
        $message = $conversation->messages()->create([
            'sender_id' => $admin->id,
            'is_admin' => true,
            'body' => $data['body'],
        ]);

        $conversation->update(['last_message_at' => $message->created_at]);

        return redirect()
            ->route('admin.messages.index', ['conversation' => $conversation->id])
            ->with('status', __('Đã gửi tin nhắn.'));
    }
}
