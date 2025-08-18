<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markAsRead(Request $request, $id)
    {
        Log::info('markAsRead called', ['notification_id' => $id]);
        $user = Auth::user();
        if (!$user) {
            Log::warning('Unauthorized access', ['id' => $id]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $notification = $user->notifications()->find($id);
        if (!$notification) {
            Log::warning('Notification not found', ['user_id' => $user->id, 'notification_id' => $id]);
            return response()->json(['error' => 'Notification not found'], 404);
        }

        Log::info('Notification before marking as read', ['notification_id' => $notification->id, 'read_at' => $notification->read_at]);
        $notification->markAsRead();
        $notification->refresh();
        Log::info('Notification after marking as read', ['notification_id' => $notification->id, 'read_at' => $notification->read_at]);

        return response()->json(['status' => 'read']);
    }

    public function readAndRedirect(Request $request, $id)
    {
        Log::info('readAndRedirect called', ['notification_id' => $id]);
        $user = Auth::user();
        if (!$user) {
            Log::warning('Unauthorized access', ['id' => $id]);
            return redirect()->route('login');
        }

        $notification = $user->notifications()->find($id);
        if (!$notification) {
            Log::warning('Notification not found', ['user_id' => $user->id, 'notification_id' => $id]);
            return redirect()->route('home'); // Redirect về trang chủ nếu không tìm thấy thông báo
        }

        Log::info('Notification before marking as read', ['notification_id' => $notification->id, 'read_at' => $notification->read_at]);
        $notification->markAsRead();
        $notification->refresh();
        Log::info('Notification after marking as read', ['notification_id' => $notification->id, 'read_at' => $notification->read_at]);

        // Lấy URL từ query parameter
        $url = $request->query('url', route('home')); // Mặc định về trang chủ nếu không có URL
        return redirect()->to(urldecode($url));
    }
}
