<?php declare(strict_types = 1);
namespace noxkiwi\spotigame;

use noxkiwi\core\Helper\FrontendHelper;
use noxkiwi\core\Path;

/** @var mixed $data */
// @formatter:off?>
        <hr />
        <?= FrontendHelper::parseFile(Path::getInheritedPath('frontend/element/dev/debugbox.php'), $data) ?>
        <div class="text-center">
            <a href="https://github.com/Jan-Nox/spotigame" target="_blank">spotigame v0.0.00018</a> ©️ 2023 by <a href="https://github.com/Jan-Nox" target="_blank">Jan Nox</a> and run on <a href="https://github.com/noxkiwi" target="_blank">kiwi fruit</a>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>
</html>