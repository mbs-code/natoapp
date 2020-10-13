<?php

namespace App\Logging\Formatters;

use Bramus\Monolog\Formatter\ColorSchemes\DefaultScheme;

// override by: https://github.com/bramus/monolog-colored-line-formatter/blob/master/src/Formatter/ColoredLineFormatter.php
class ColorLineFormatter extends CustomLineFormatter
{
    /**
     * The Color Scheme to use
     * @var ColorSchemeInterface
     */
    private $colorScheme = null;

    /**
     * Gets The Color Scheme
     * @return ColorSchemeInterface
     */
    public function getColorScheme()
    {
        if (!$this->colorScheme) {
            $this->colorScheme = new DefaultScheme();
        }

        return $this->colorScheme;
    }

    /**
     * {@inheritdoc}
     */
    public function format(array $record) : string
    {
        $colorScheme = $this->getColorScheme();
        $output = parent::format($record);

        return $colorScheme->getColorizeString($record['level'])
            .trim($output)
            .$colorScheme->getResetString()
            ."\n";
    }
}
