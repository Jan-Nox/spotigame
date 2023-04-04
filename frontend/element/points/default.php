<?php declare(strict_types = 1);
namespace noxkiwi\spotigame;

use noxkiwi\translator\Translator;
use function number_format;

/** @var \noxkiwi\spotigame\Player\Player $data */
$t      = Translator::getInstance();
$points = number_format($data->points, 0, $t->translate('DECIMAL_SEPARATOR'), $t->translate('THOUSANDS_SEPARATOR'));
echo <<<HTML
<small>🪙$points</small>
HTML;
