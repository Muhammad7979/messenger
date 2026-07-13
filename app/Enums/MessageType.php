<?php

namespace App\Enums;

enum MessageType: int
{
    case Text = 0;
    case Image = 1;
    case Video = 2;
    case Audio = 3;
    case File = 4;
    case VoiceNote = 5;
    case System = 6;
    case Gif = 7;

    public function label(): string
    {
        return match ($this) {
            self::Text => 'text',
            self::Image => 'image',
            self::Video => 'video',
            self::Audio => 'audio',
            self::File => 'file',
            self::VoiceNote => 'voice_note',
            self::System => 'system',
            self::Gif => 'gif',
        };
    }

    public function hasAttachment(): bool
    {
        return in_array($this, [
            self::Image,
            self::Video,
            self::Audio,
            self::File,
            self::VoiceNote,
            self::Gif,
        ], true);
    }
}
