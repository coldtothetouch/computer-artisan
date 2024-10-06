<?php

namespace App\Services;

interface NotificationServiceContract
{
    public static function sendMessage(string $text);
}
