<?php declare(strict_types = 1);
namespace noxkiwi\spotigame;

use Exception;
use noxkiwi\core\Constants\Mvc;
use noxkiwi\core\Environment;
use noxkiwi\core\Helper\FrontendHelper;
use noxkiwi\core\Helper\JsonHelper;
use noxkiwi\core\Helper\LinkHelper;
use noxkiwi\core\Path;
use noxkiwi\core\Session;
use noxkiwi\translator\Translator;
use function chr;
use function print_r;
use function var_dump;

try {
    $leaderBoardEntries = (array)$data->get('leaderboard', []);
    $b                  = '';
    $a                  = App::getInstance();
    /** @var \noxkiwi\spotigame\Player\Player $leaderBoardEntry */
    foreach ($leaderBoardEntries as $leaderBoardEntry) {
        $b .= <<<HTML
<tr>
    <td style="width: 8%">
        {$a->returnIt(FrontendHelper::parseFile(Path::getInheritedPath('frontend/element/player/avatar.php'), $leaderBoardEntry))}
    </td>
    <td>
        {$a->returnIt(FrontendHelper::parseFile(Path::getInheritedPath('frontend/element/player/name.php'), $leaderBoardEntry))}
    </td>
    <td style="text-align: right">
        <h1>{$a->returnIt(FrontendHelper::parseFile(Path::getInheritedPath('frontend/element/player/points.php'), $leaderBoardEntry->points))}</h1>
    </td>
</tr>
HTML;
    }
} catch (Exception $e) {
}
echo <<<HTML
<h1>Spotigame Leaderboard</h1>
<table class="table table-sm table-striped table-responsive-sm">
    <thead>
        <tr>
            <th colspan="2">Spieler</th>
            <th>Punkte</th>
        </tr>    
    </thead>
    <tbody>
        $b
    </tbody>
</table>
HTML;