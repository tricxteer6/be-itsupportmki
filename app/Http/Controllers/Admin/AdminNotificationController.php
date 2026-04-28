<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function feed(Request $request): JsonResponse
    {
        $user = $request->user();
        $items = $user->notifications()
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn ($n) => [
                'id' => $n->id,
                'title' => $n->data['title'] ?? 'Pembaruan tiket',
                'body' => $n->data['body'] ?? (isset($n->data['requester_name']) ? 'Permintaan baru dari '.$n->data['requester_name'] : null),
                'requester_name' => $n->data['requester_name'] ?? null,
                'status' => $n->data['status'] ?? null,
                'created_at_human' => $n->created_at?->diffForHumans(),
            ]);

        return response()->json([
            'unread_count' => $user->unreadNotifications()->count(),
            'items' => $items,
        ]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['ok' => true]);
    }
}
