<?php

namespace App\Enums;

enum AttachmentStorage: string
{
    case Local = 'local';
    case S3 = 's3';
    case Minio = 'minio';
}
