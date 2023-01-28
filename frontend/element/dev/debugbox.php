<?php declare(strict_types = 1);
namespace noxkiwi\spotigame;

use noxkiwi\core\Environment;

/** @var string $data */
if (Environment::getCurrent() === Environment::PRODUCTION) {
    return;
}
echo <<<HTML
<div class="card">
    <h5 class="card-header">Debug Console</h5>
    <div class="card-body">
        <pre>$data</pre>
    </div>
</div>
HTML;
