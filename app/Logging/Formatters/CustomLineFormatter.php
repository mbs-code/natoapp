<?php

namespace App\Logging\Formatters;

use Monolog\Formatter\LineFormatter;

class CustomLineFormatter extends LineFormatter
{
    public function __construct()
    {
        // 2020-10-13 [local.INFO] message
        $lineFormat = "%datetime% [%channel%.%level_name%] %message%" . PHP_EOL;
        $dateFormat = "Y-m-d H:i:s.v"; // PHP: DateTime::format

        parent::__construct($lineFormat, $dateFormat, true, true);
    }

    public function format(array $record): string
    {
        $output = parent::format($record);
        return $output;
    }
}
