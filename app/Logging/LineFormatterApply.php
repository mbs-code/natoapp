<?php

namespace App\Logging;

use App\Logging\Formatters\CustomLineFormatter;

class LineFormatterApply
{
    public function __invoke($logging)
    {
        $customLineFormatter = new CustomLineFormatter();

        foreach($logging->getHandlers() as $handler) {
            $handler->setFormatter($customLineFormatter);
        }
    }
}
