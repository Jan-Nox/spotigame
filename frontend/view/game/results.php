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

/** @var \noxkiwi\core\Response $data */
try {
    /** @var \noxkiwi\spotigame\Sitting\Sitting $sitting */
    $sitting = $data->get('sitting');
    $pr      = '';
    foreach ($data->get('playerRows', []) as $playerRow) {
        $pr .= FrontendHelper::parseFile(Path::getInheritedPath('frontend/element/results/playerrow.php'), $playerRow);
    }
    $a = '';
    foreach ($data->get('unfinishedPlayers') as $uf) {
        /** @var \noxkiwi\spotigame\Player\Player $uf */
        $a .= $uf->avatar;
    }
    $js     = '';
    $notice = '';
    if (! $sitting->finished) {
        $notice = 'Die Runde ist noch nicht beendet.';
        $js     = <<<JS
<script>
window.setTimeout(function () {
    window.location.reload()
}, 5000);
</script>
JS;
    }
} catch (Exception $e) {
    var_dump($e);
    die('Tja!');
}
echo <<<HTML
<h2>Game Over</h2>
$a $notice
<table class="table table-sm table-striped">
    <thead>
    <tr>
        <th>Rang</th>
        <th colspan="2">Spieler</th>
        <th>Punkte</th>
    </tr>
    </thead>
    <tbody>
        $pr
    </tbody>
</table>
$js
HTML;
