<?php declare(strict_types = 1);
namespace noxkiwi\spotigame;

use noxkiwi\core\Constants\Mvc;
use noxkiwi\core\Environment;
use noxkiwi\core\Helper\LinkHelper;
use noxkiwi\core\Session;

/** @var \noxkiwi\spotigame\Sitting\Sitting $data */
$session     = Session::getInstance();
$environment = Environment::getInstance();
$currentStep = (int)$session->get('CURRENT_STEP', 1);
$progress    = $currentStep / $data->stepCount * 100;
$hostName    = $environment->get('server>hostname', 'https://spotigame.nox.kiwi/');
$ila = [
    Mvc::CONTEXT => 'sitting',
    Mvc::ACTION => 'join',
    'sittingId' => $data->id
];
$a = LinkHelper::get($ila);
$inviteLink  = "$hostName$a";
echo <<<HTML
<div class="input-group mb-3">
    <span class="input-group-text btn btn-success" id="basic-addon1">
        <a class="" href="whatsapp://send?text=$inviteLink">➕</a>
    </span>
    <input type="text" class="form-control" type="text" value="$inviteLink" disabled>
</div>
<div class="progress" style="height:3px">
    <div class="progress-bar bg-danger" role="progressbar" id="gameProgress" style="width: $progress%" aria-valuenow="$progress" aria-valuemin="0" aria-valuemax="100">
    </div>
</div>
HTML;
