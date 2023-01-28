<?php declare(strict_types = 1);

namespace noxkiwi\spotigame;

/** @var \noxkiwi\spotigame\Player\Player $data */

echo <<<HTML
<div class="row">
    <div class="col-3">
        <img src="$data->avatar" class="rounded" width="100%"/>
    </div>
    <div class="col-9">
        <h1>$data->name</h1>
        <p>
            <small style="font-family: ' Courier New', 'Monospace'">🪙$data->points</small>
        </p>
    </div>
</div>
HTML;
