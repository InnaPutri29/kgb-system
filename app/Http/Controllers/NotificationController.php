<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Tandai satu notifikasi sebagai dibaca.
     */
    public function read($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return back()->with('success', 'Notifikasi berhasil ditandai sebagai dibaca.');
    }

    /**
     * Tandai semua notifikasi sebagai dibaca.
     */
    public function readAll()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi berhasil ditandai sebagai dibaca.');
    }
}
