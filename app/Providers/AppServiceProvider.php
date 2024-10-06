<?php

namespace App\Providers;

use App\Services\NotificationServiceContract;
use App\Services\Telegram;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Monolog\Handler\TelegramBotHandler;

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
        Vite::prefetch(concurrency: 3);
        Carbon::setLocale('ru');
        JsonResource::withoutWrapping();

        app()->bind(NotificationServiceContract::class, fn() => new Telegram);
    }
}
