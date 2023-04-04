<?php declare(strict_types = 1);
namespace noxkiwi\spotigame;

use noxkiwi\core\Environment;
use noxkiwi\core\Response;
use noxkiwi\spotigame\Auth\SpotigameAuth;
use function memory_get_peak_usage;
use function print_r;

return;
/** @var string $data */
if (Environment::getCurrent() === Environment::PRODUCTION) {
    return;
}
if (! SpotigameAuth::isAdmin()) {
    return;
}
$r   = print_r(Response::getInstance()->get(), true);
$mem = memory_get_peak_usage();
echo <<<HTML
<div class="card">
$mem bytes
    <h5 class="card-header">Debug Console</h5>
    <div class="card-body">
        <pre><?= $data ?></pre>
        <pre><?= $r ?></pre>
    </div>
</div>
HTML;
