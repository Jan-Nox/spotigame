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
    $player   = $data->get('player');
    $sittings = $data->get('sittings', []);
    $a        = App::getInstance();
    $b        = '';
    foreach ($sittings as $sitting) {
        $link = LinkHelper::get([
                                    Mvc::CONTEXT => 'game',
                                    Mvc::VIEW    => 'results',
                                    'sittingId'  => $sitting['sitting_id']
                                ]);
        $b    .= <<<HTML
<tr>
    <td>
        <a href="$link">{$sitting['sitting_code']}</a>
    </td>
    <td style="text-align: right">
        {$a->returnIt(FrontendHelper::parseFile(Path::getInheritedPath('frontend/element/player/points.php'), (int)$sitting['sitting_points']))}
    </td>
    <td style="text-align: right">
        {$a->returnIt(FrontendHelper::parseFile(Path::getInheritedPath('frontend/element/sitting/songcount.php'), (int)$sitting['sitting_steps']))}
    </td>
    <td style="text-align: right">
        {$a->returnIt(FrontendHelper::parseFile(Path::getInheritedPath('frontend/element/sitting/playercount.php'), (int)$sitting['sitting_players']))}
    </td>
</tr>
HTML;
    }
} catch (Exception $e) {
    var_dump($e);
    die('Tja!');
}
echo <<<HTML
{$a->returnIt(FrontendHelper::parseFile(Path::getInheritedPath('frontend/element/player/mini.php'), $player))}
<hr />
<table class="table table-sm table-striped table-responsive-sm">
    <thead>
        <tr>
            <th>Sitting</th>
            <th>Punkte</th>
            <th>Songs</th>
            <th>Spieler</th>
        </tr>    
    </thead>
    <tbody>
        $b
    </tbody>
</table>
HTML;