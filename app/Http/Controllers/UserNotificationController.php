<?php

namespace App\Http\Controllers;

class UserNotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return auth()->user()->unreadNotifications;
    }

    public function destroy($user, $notificationId)
    {
        dd($user);
        auth()->user()->notifications()->findOrFail($notificationId)->markAsRead();
    }
}