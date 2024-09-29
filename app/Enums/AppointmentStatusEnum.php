<?php

namespace App\Enums;

enum AppointmentStatusEnum: string
{
    case DENIED = 'Отменена';
    case PENDING = 'В обработке';
    case COMPLETED = 'Завершена';

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
