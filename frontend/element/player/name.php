<?php declare(strict_types = 1);
namespace noxkiwi\spotigame;

use noxkiwi\spotigame\Player\Player;

/** @var \noxkiwi\spotigame\Player\Player $data */
$me    = Player::identify();
$style = '';
if ($data->getId() === $me->getId()) {
    $style = 'font-weight:bold;';
}
echo <<<HTML
<span style="$style">$data->name</span>
HTML;
