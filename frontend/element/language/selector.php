<?php declare(strict_types = 1);
namespace noxkiwi\spotigame;

use noxkiwi\translator\Translator;

/** @var \noxkiwi\spotigame\Player\Player $data */
$current = Translator::getEmoji();
$de = Translator::getEmoji(Translator::LANGUAGE_DE_DE);
$nz = Translator::getEmoji(Translator::LANGUAGE_EN_NZ);
echo <<<HTML
<div class="dropdown">
  <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
    $current
  </button>
  <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
    <li><a class="dropdown-item" href="/?lang=de-DE">$de</a></li>
    <li><a class="dropdown-item" href="/?lang=en-NZ">$nz</a></li>
  </ul>
</div>
HTML;
