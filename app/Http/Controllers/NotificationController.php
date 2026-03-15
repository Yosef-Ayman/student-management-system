<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()
            ->notifications()
            ->latest()
            ->limit(20)
            ->get()
            ->map(fn($n) => [
                'id'      => $n->id,
                'message' => $n->data['message'] ?? 'New notification',
                'url'     => $n->data['url'] ?? '#',
                'read'    => !is_null($n->read_at),
                'time'    => $n->created_at->diffForHumans(),
            ]);

        $unread = Auth::user()->unreadNotifications()->count();

        return response()->json(['notifications' => $notifications, 'unread' => $unread]);
    }

    public function markRead(string $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return response()->json(['ok' => true]);
    }

    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['ok' => true]);
    }

    public function destroy(string $id)
    {
        Auth::user()->notifications()->where('id', $id)->delete();
        return response()->json(['ok' => true]);
    }

    public function destroyAll()
    {
        Auth::user()->notifications()->delete();
        return response()->json(['ok' => true]);
    }
}
