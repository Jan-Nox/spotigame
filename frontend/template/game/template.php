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
use noxkiwi\spotigame\Auth\SpotigameAuth;
use noxkiwi\translator\Translator;
use function chr;
use function print_r;
use function var_dump;

/** @var \noxkiwi\core\Response $data */
try {
    $a           = App::getInstance();
    $playerLink  = LinkHelper::get([
                                       Mvc::CONTEXT => 'player',
                                       Mvc::VIEW    => 'info'
                                   ]);
    $newGameLink = LinkHelper::get([
                                       Mvc::CONTEXT => 'sitting',
                                       Mvc::ACTION  => 'create',
                                       'steps'      => 5
                                   ]);
    $leaderBoardLink = LinkHelper::get([
                                       Mvc::CONTEXT => 'info',
                                       Mvc::VIEW  => 'leaderboard'
                                   ]);
} catch (Exception) {
    die('A');
}
$settingsLink = LinkHelper::get(
    [
        Mvc::CONTEXT => 'settings',
        Mvc::VIEW    => 'show'
    ]
);
$adminLink    = '';
if (SpotigameAuth::isAdmin()) {
    $url       = LinkHelper::get(
        [
            Mvc::CONTEXT => 'crudfrontend',
            Mvc::VIEW    => 'list',
            'modelName'  => 'Player'
        ]
    );
    $adminLink = <<<HTML
<li class="nav-item">
<a class="nav-link active" aria-current="page" href="$url">⚙️ Admin</a>
</li>
HTML;
}
echo <<<HTML
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SpotiGame</title>

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" type="text/css" href="/asset/lib/bootstrap/css/bootstrap-night.css">
    <style>
     body {background : #081017;color : white;font-weight : lighter;}
    .step > div > ul {margin : 0;padding : 0;}

        .step > div > ul > li {background : rgba(255, 255, 255, 0.2);color : #999;margin : 0 0 5px;padding : 0;list-style-type : none;font-size : 2em;cursor : pointer;width : 100%;text-align : center;}

        .step > div > ul > li:hover {color : #333;background : rgb(242, 242, 242);}

            .step > div > ul > li.active {background : rgba(255, 255, 255, 1);color : black;}

        .correct {background : rgba(123, 227, 123, 1)!important;color : black!important;}

        .partly {background : rgba(227, 227, 123, 1)!important;color : black!important;}

        .wrong {background : rgba(227, 123, 123, 1)!important;color : black!important;}

        #btnNext {color : white;background : rgb(56, 155, 255);}
        
        .points::after {content: '🪙';}
        .songs::after {content: '🎤';}
        .players::after {content: '👥';}

    </style>
    <script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</head>
<body>
    <!-- HEADER -->
    <header>
      <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
        <div class="container-fluid">
          <a class="navbar-brand" onclick="alert('v0.0.00018')">SpotiGame</a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav me-auto mb-2 mb-md-0">
              <li class="nav-item">
                <a class="nav-link" aria-current="page" href="$newGameLink">🎮 Neues Spiel</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" aria-current="page" href="$playerLink">👤 Mein Profil</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" aria-current="page" href="$settingsLink">⚙️ Einstellungen</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" aria-current="page" href="$leaderBoardLink">🏆 Leaderboard</a>
              </li>
              $adminLink
            </ul>
          </div>
        </div>
      </nav>
    </header>
    
    <!-- MAIN -->
    <main class="flex-shrink-0" style="padding-top:60px;">
      <div class="container">
        {$data->get('content')}
        {$a->returnIt(FrontendHelper::parseFile(Path::getInheritedPath('frontend/element/dev/debugbox.php'), print_r($data->get(), true)))}
        <div class="text-center">
            <a href="https://github.com/Jan-Nox/spotigame" target="_blank">spotigame v0.0.00018</a> ©️ 2023 by <a href="https://github.com/Jan-Nox" target="_blank">Jan Nox</a> and run on <a href="https://github.com/noxkiwi" target="_blank">kiwi fruit</a>
        </div>
      </div>
    </main>
    
    <!-- FOOT
    <footer class="footer mt-auto py-3 bg-dark">
        <div class="container">
            <span class="text-center text-muted">
                <a href="https://github.com/Jan-Nox/spotigame" target="_blank">spotigame v0.0.00018</a> ©️ 2023 by <a href="https://github.com/Jan-Nox" target="_blank">Jan Nox</a> and run on <a href="https://github.com/noxkiwi" target="_blank">kiwi fruit</a>
            </span>
        </div>
    </footer> -->
</body>
</html>
HTML;