<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Throwable;

class Telegram implements NotificationServiceContract
{
    public const string HOST = 'https://api.telegram.org/bot';

    public static function sendMessage(string $text)
    {
        try {
            $response = Http::get(self::HOST . config('services.telegram.token') . '/sendMessage', [
                'chat_id' => config('services.telegram.chat_id'),
                'parse_mode' => 'html',
                'text' => $text,
            ])->throw()->json();

            return $response['ok'] ?? false;
        } catch (Throwable $exception) {
            return false;
        }
    }
}
