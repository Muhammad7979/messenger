<?php

namespace App\Enums;

enum ConversationType: int
{
    case Private = 1;
    case Group = 2;
    case Channel = 3;

    public function label(): string
    {
        return match ($this) {
            self::Private => 'private',
            self::Group => 'group',
            self::Channel => 'channel',
        };
    }
}
