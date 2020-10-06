<?php

namespace App\Enums;

use MyCLabs\Enum\Enum;

class VideoType extends Enum
{
    private const VIDEO = 'video';
    private const UPCOMING = 'upcoming';
    private const LIVE = 'live';
    private const ARCHIVE = 'archive';
    private const PREMIERE = 'premiere';
}
