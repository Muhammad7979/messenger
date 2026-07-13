<?php

namespace App\Enums;

enum PresenceStatus: string
{
    case Online = 'online';
    case Offline = 'offline';
    case Away = 'away';
    case Busy = 'busy';
}
