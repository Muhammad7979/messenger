<?php

namespace App\Enums;

enum DevicePlatform: string
{
    case Android = 'android';
    case Ios = 'ios';
    case Web = 'web';
    case Windows = 'windows';
    case Macos = 'macos';
    case Linux = 'linux';

    public function isMobile(): bool
    {
        return in_array($this, [self::Android, self::Ios], true);
    }
}
