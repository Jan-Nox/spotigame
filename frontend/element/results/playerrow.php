<?php declare(strict_types = 1);
namespace noxkiwi\spotigame;

use noxkiwi\core\Helper\FrontendHelper;
use noxkiwi\core\Path;

/** @var \noxkiwi\spotigame\Result\PlayerRow $data */
$rowClass = '';
if (!$data->finished) {
    $rowClass = 'bg-warning';
}
$a = App::getInstance();
echo <<<HTML
<tr>
    <td class="$rowClass">
        <h3>🥳$data->rank</h3>
    </td>
    <td  style="height:75px;">
        {$a->returnIt(FrontendHelper::parseFile(Path::getInheritedPath('frontend/element/player/avatar.php'), $data->player))}
    </td>
    <td class="$rowClass">
            {$a->returnIt(FrontendHelper::parseFile(Path::getInheritedPath('frontend/element/player/name.php'), $data->player))}
            <br />
            {$a->returnIt(FrontendHelper::parseFile(Path::getInheritedPath('frontend/element/player/points.php'), $data->player->points))}
    </td>
    <td style="text-align: right">
        <h3>
            {$a->returnIt(FrontendHelper::parseFile(Path::getInheritedPath('frontend/element/player/points.php'), $data->points))}
        </h3>
    </td>
</tr>
HTML;

