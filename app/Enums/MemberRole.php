<?php

namespace App\Enums;

enum MemberRole: string
{
    case Owner = 'owner';
    case Admin = 'admin';
    case Moderator = 'moderator';
    case Member = 'member';

    public function isPrivileged(): bool
    {
        return in_array($this, [
            self::Owner,
            self::Admin,
        ], true);
    }
}
