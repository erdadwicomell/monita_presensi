<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotifikasiController extends Controller
{
    /**
     * Ambil notifikasi unread & latest list (JSON API)
     */
    public function getLatest(Request $request): JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['unread_count' => 0, 'notifications' => []]);
        }

        $unreadCount = Notifikasi::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        $notifications = Notifikasi::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($item) {
                return [
                    'id'               => $item->id,
                    'judul'            => $item->judul,
                    'pesan'            => $item->pesan,
                    'link'             => $item->link,
                    'url_terkait'      => $item->link,
                    'tipe'             => $item->tipe,
                    'is_read'          => $item->is_read,
                    'time_ago'         => $item->created_at->diffForHumans(),
                    'created_at_human' => $item->created_at->diffForHumans(),
                    'created_at'       => $item->created_at->format('d M Y H:i'),
                ];
            });

        return response()->json([
            'unread_count'  => $unreadCount,
            'notifications' => $notifications,
            'notifikasi'    => $notifications,
        ]);
    }

    /**
     * Tandai satu notifikasi sudah dibaca
     */
    public function markAsRead(int $id): JsonResponse
    {
        $user = auth()->user();

        $notif = Notifikasi::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if ($notif) {
            $notif->update(['is_read' => true]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Tandai semua notifikasi sudah dibaca
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = auth()->user();

        Notifikasi::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
