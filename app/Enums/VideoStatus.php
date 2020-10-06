<?php

namespace App\Enums;

use MyCLabs\Enum\Enum;

class VideoStatus extends Enum
{
    private const PUBLIC = 'public';
    private const UNLISTED = 'unlisted';
    private const PRIVATE = 'private';
    private const DELETE = 'delete'; // reserve
}
