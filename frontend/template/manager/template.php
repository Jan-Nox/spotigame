<?php declare(strict_types = 1);
namespace noxkiwi\spotigame;

use noxkiwi\core\Response;

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Spotigame Backoffice</title>

    <!-- JQ -->
    <script type="text/javascript" src="/asset/lib/jquery/jquery.min.js"></script>

    <!-- BOOTSTRAP -->
    <link rel="stylesheet" type="text/css" href="/asset/lib/bootstrap/css/bootstrap-night.css">
    <script type="text/javascript" src="/asset/lib/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- DATATABLES -->
    <link rel="stylesheet" type="text/css" media="screen" href="/asset/lib/datatables/css/datatables.min.css"/>
    <script type="text/javascript" src="/asset/lib/datatables/js/datatables.min.js"></script>
    <script type="text/javascript" src="/asset/lib/datatables.buttons/js/buttons.colVis.min.js"></script>
    <script type="text/javascript" src="/asset/lib/datatables.buttons/js/buttons.bootstrap5.min.js"></script>

    <!-- FONTAWESOME -->
    <link rel="stylesheet" type="text/css" media="screen" href="/asset/lib/fontawesome/css/all.min.css"/>
    <script type="text/javascript" src="/asset/lib/fontawesome/js/all.min.js"></script>

    <!-- SELECTIZE
    <script type="text/javascript" src="/asset/lib/selectize/examples/js/jqueryui.js"></script>
    <script type="text/javascript" src="/asset/lib/selectize/dist/js/standalone/selectize.js"></script>
    <script type="text/javascript" src="/asset/lib/selectize/examples/js/index.js"></script>
     -->
</head>
<body>
<nav class="navbar navbar-expand navbar-dark bg-dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-link"><a href="">XXXX</a></li>
                <li class="nav-link dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-solid fa-table fa-2x"></i>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="/?context=crudfrontend&view=list&modelName=Answer">Answer</a></li>
                        <li><a class="dropdown-item" href="/?context=crudfrontend&view=list&modelName=Category">Category</a></li>
                        <li><a class="dropdown-item" href="/?context=crudfrontend&view=list&modelName=Move">Move</a></li>
                        <li><a class="dropdown-item" href="/?context=crudfrontend&view=list&modelName=Player">Player</a></li>
                        <li><a class="dropdown-item" href="/?context=crudfrontend&view=list&modelName=Question">Question</a></li>
                        <li><a class="dropdown-item" href="/?context=crudfrontend&view=list&modelName=Sitting">Sitting</a></li>
                        <li><a class="dropdown-item" href="/?context=crudfrontend&view=list&modelName=SittingPlayer">SittingPlayer</a></li>
                        <li><a class="dropdown-item" href="/?context=crudfrontend&view=list&modelName=Song">Song</a></li>
                        <li><a class="dropdown-item" href="/?context=crudfrontend&view=list&modelName=Translation">Translation</a></li>
                        <li><a class="dropdown-item" href="/?context=crudfrontend&view=list&modelName=Vote">Vote</a></li>
                    </ul>
                </li>
            </ul>
        </div>
</nav>
<hr/>
<div class="container-fluid">

    <?= Response::getInstance()->get('content') ?>
</div>
</body>
</html>
