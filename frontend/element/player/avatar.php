<?php declare(strict_types = 1);
namespace noxkiwi\spotigame;

use noxkiwi\core\Constants\Mvc;
use noxkiwi\core\Helper\FrontendHelper;
use noxkiwi\core\Helper\LinkHelper;
use noxkiwi\core\Path;
use noxkiwi\core\Response;
use noxkiwi\spotigame\Auth\SpotigameAuth;
use noxkiwi\spotigame\Player\Player;

/** @var \noxkiwi\spotigame\Player\Player $data */
$playerLink = '';
$me         = Player::identify();
$color      = 'rgba(255,255,255,.6)';
if (SpotigameAuth::isAdmin()) {
    $playerLink = LinkHelper::get([
                                      Mvc::CONTEXT => 'player',
                                      Mvc::VIEW    => 'info',
                                      'playerId'   => $data->id
                                  ]);
}
if ($data->getId() === $me->getId()) {
    $playerLink = LinkHelper::get([
                                      Mvc::CONTEXT => 'player',
                                      Mvc::VIEW    => 'info',
                                      'playerId'   => $data->id
                                  ]);
    $color      = 'rgba(0,255,255,.6)';
}
echo <<<HTML
<a href="$playerLink">
    <img style="max-width: 100%;max-height:100%;border-radius: 5px;border: 1px solid $color;" src="$data->avatar" />
</a>
HTML;
