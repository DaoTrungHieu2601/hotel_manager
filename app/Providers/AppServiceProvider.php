<?php

namespace App\Providers;

use App\Models\ChatConversation;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(
            [
                'layouts.guest-hotel',
                'layouts.hotel',
                'layouts.auth-split',
                'guest.home',
                'guest.search',
            ],
            function ($view) {
                $setting = SiteSetting::instance();
                $view->with([
                    'siteSetting' => $setting,
                    'displaySiteName' => $setting->displayName(),
                    'siteTagline' => $setting->site_tagline,
                ]);
            }
        );

        View::composer('layouts.hotel', function ($view) {
            $chatCount = 0;
            $unreadNotifications = collect();
            $unreadNotifCount = 0;

            if (auth()->check()) {
                $user = auth()->user();

                if ($user->isAdmin()) {
                    $chatCount = ChatConversation::query()->whereHas('messages', function ($q) {
                        $q->where('is_admin', false)->whereNull('read_at');
                    })->count();
                }

                $unreadNotifications = $user->notifications()->latest()->take(20)->get();
                $unreadNotifCount = $user->unreadNotifications()->count();
            }

            $view->with([
                'adminUnreadChatCount' => $chatCount,
                'unreadNotifications' => $unreadNotifications,
                'unreadNotifCount' => $unreadNotifCount,
            ]);
        });
    }
}
