<?php declare(strict_types = 1);
namespace noxkiwi\spotigame;

use noxkiwi\translator\Translator;
use function number_format;

/** @var int $data */
$t      = Translator::getInstance();
$points = number_format($data, 0, $t->translate('DECIMAL_SEPARATOR'), $t->translate('THOUSANDS_SEPARATOR'));
echo <<<HTML
<span class="points">$data</span>
HTML;
