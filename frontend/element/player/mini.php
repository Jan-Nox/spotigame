<?php declare(strict_types = 1);
namespace noxkiwi\spotigame;

use noxkiwi\core\Helper\FrontendHelper;
use noxkiwi\core\Path;

/** @var \noxkiwi\spotigame\Player\Player $data */
$a = App::getInstance();
echo <<<HTML
<div class="row">
    <div class="col-2">
        {$a->returnIt(FrontendHelper::parseFile(Path::getInheritedPath('frontend/element/player/avatar.php'), $data))}
    </div>
    <div class="col-10">
        <h1>
            {$a->returnIt(FrontendHelper::parseFile(Path::getInheritedPath('frontend/element/player/name.php'), $data))}
        </h1>
        {$a->returnIt(FrontendHelper::parseFile(Path::getInheritedPath('frontend/element/player/points.php'), $data->points))}
    </div>
</div>
HTML;
