<?php

namespace App\Logging;

use App\Logging\Formatters\ColorLineFormatter;

class ColorFormatterApply
{
    public function __invoke($logging)
    {
        $coloredLineFormattetr = new ColorLineFormatter();

        foreach($logging->getHandlers() as $handler) {
            $handler->setFormatter($coloredLineFormattetr);
        }
    }
}
