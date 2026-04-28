<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()->notifications()->latest()->limit(30)->get();

        return $this->successResponse([
            'unread_count' => $request->user()->unreadNotifications()->count(),
            'items' => NotificationResource::collection($notifications),
        ]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();
        return $this->successResponse(null, 'All notifications marked as read.');
    }
}
