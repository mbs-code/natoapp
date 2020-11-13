<?php

namespace App\Lib\TaskBuilder\Events;

use App\Lib\TaskBuilder\Events\DebugTaskEventer;
use Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;

class DebugBlackTaskEventer extends DebugTaskEventer
{
    function __construct()
    {
        parent::__construct();

        $this->colorEvents = [SGR::COLOR_FG_BLACK_BRIGHT];

        $this->colorValue = SGR::COLOR_FG_BLACK_BRIGHT;
        $this->colorNullValue = SGR::COLOR_FG_BLACK_BRIGHT;
        $this->colorExceptionValue = SGR::COLOR_FG_BLACK_BRIGHT;

        $this->colorSynbolAddRecord = SGR::COLOR_FG_BLACK_BRIGHT;
        $this->colorSynbolRemoveRecord = SGR::COLOR_FG_BLACK_BRIGHT;
        $this->colorRecord = SGR::COLOR_FG_BLACK_BRIGHT;

        $this->colorSymbolPushJob = SGR::COLOR_FG_BLACK_BRIGHT;
        $this->colorSymbolPopJob = SGR::COLOR_FG_BLACK_BRIGHT;
        $this->colorJob = SGR::COLOR_FG_BLACK_BRIGHT;
    }
}
