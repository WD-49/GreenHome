<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class HeaderComposer
{
    public function compose(View $view)
    {
        $notifications = Auth::check()
            ? Auth::user()->unreadNotifications
            : collect();

        $view->with('notifications', $notifications);
    }
}
