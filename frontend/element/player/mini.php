<?php declare(strict_types = 1);
namespace noxkiwi\spotigame;

use noxkiwi\core\Environment;
use noxkiwi\core\Session;
use function in_array;

/** @var \noxkiwi\spotigame\Player\Player $data */
$points    = number_format($data->points, 0);
$adminLink = '';
$e         = Environment::getInstance();
if (in_array(Session::getInstance()->get('player_spotify_id'), (array)$e->get("admins", []), true)) {
    $adminLink = <<<HTML
<a href="/?context=crudfrontend&view=list&modelName=Player">⚙️</a>
HTML;
}
echo <<<HTML
<div class="row">
    <div class="col-3">
        <img src="$data->avatar" class="rounded" width="100%"/>
    </div>
    <div class="col-9">
        <h1>$data->name $adminLink</h1>
        <p>
            <small style="font-family: ' Courier New', 'Monospace'">🪙$points</small>
        </p>
    </div>
</div>
HTML;
