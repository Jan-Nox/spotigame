<?php declare(strict_types = 1);
namespace noxkiwi\spotigame;

use Exception;use noxkiwi\core\Environment;
use noxkiwi\core\Helper\FrontendHelper;use noxkiwi\core\Helper\JsonHelper;
use noxkiwi\core\Path;use noxkiwi\core\Session;use function chr;use function print_r;

/** @var \noxkiwi\core\Response $data */
try {
    $stepSetup    = JsonHelper::encode((array)$data->get('stepSetup', []));
    $hostName     = Environment::getInstance()->get('server>hostname', 'https://spotigame.nox.kiwi/');
    $player       = $data->get('player');
    $dbg          = chr(10) . chr(10) . "SITTING";
    $dbg          .= print_r($data->get('sitting'), true);
    $dbg          .= chr(10) . chr(10) . "SESSION";
    $dbg          .= print_r(Session::getInstance()->get(), true);
    $dbg          .= chr(10) . chr(10) . "SONG";
    $dbg          .= print_r($data->get('song'), true);
    $song         = $data->get('song');
    $songDuration = $song->duration - 2000;
} catch (Exception) {
    die('Tja!');
}
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SpotiGame</title>

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" type="text/css" href="/asset/lib/bootstrap/css/bootstrap-night.css">
    <style>
        body {background : #000000;color : white;font-weight : lighter;}

        .step > div > ul {margin : 0;padding : 0;}

        .step > div > ul > li {background : rgba(255, 255, 255, 0.2);color : #999;margin : 0 0 5px;padding : 0;list-style-type : none;font-size : 2em;cursor : pointer;width : 100%;text-align : center;}

        .step > div > ul > li:hover {color : #333;background : rgb(242, 242, 242);}

        .step > div > ul > li.active {background : rgba(255, 255, 255, 1);color : black;}

        .step > div > ul > li.correct {background : rgba(123, 227, 123, 1);color : black;}

        .step > div > ul > li.partly {background : rgba(227, 227, 123, 1);color : black;}

        .step > div > ul > li.wrong {background : rgba(227, 123, 123, 1);color : black;}

        #btnNext {color : rgba(123, 227, 123, 1)}
    </style>
</head>
<body>

<div class="container">
    <?= FrontendHelper::parseFile(Path::getInheritedPath('frontend/element/player/mini.php'), $player) ?>
    <hr/>
    <?= FrontendHelper::parseFile(Path::getInheritedPath('frontend/element/game/progressbar.php'), $player) ?>
    <hr/>
    <?= FrontendHelper::parseFile(Path::getInheritedPath('frontend/element/game/stepbody.php'), $player) ?>
    <hr/>
    <?= FrontendHelper::parseFile(Path::getInheritedPath('frontend/element/dev/debugbox.php'), $dbg) ?>
    <div class="text-center">
        <a href="https://github.com/Jan-Nox/spotigame" target="_blank">spotigame v0.0.00013</a> Â©ï¸ 2023 by <a href="https://github.com/Jan-Nox" target="_blank">Jan Nox</a> and run on <a href="https://github.com/noxkiwi" target="_blank">kiwi fruit</a>
    </div>

</div>
<script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>

<script>
    let stepSetup = <?=$stepSetup?>;

    let curStep = 0,
        vote    = {};

    function buildOptions(stepData) {
        let html        = "<ul>",
            optionIndex = 0,
            optionCount = stepData.options.length;
        for (optionIndex = 0; optionIndex <= optionCount - 1; optionIndex++) {
            let myOption = stepData.options[optionIndex],
                selected = "";
            if ((vote[stepData.key] || "") === myOption) {
                selected = "active";
            }
            html = html + "<li class=\"select " + selected + "\">" + myOption + "</li>";
        }
        html = html + "</ul>";

        return html;
    }

    function showSelectStep(stepData) {
        stepBox.find("h4").html("<span class=\"previous-step float-start pointer \">âªï¸</span>" + stepData.question + "<span class=\"next-step float-end pointer\">â©</step>");
        stepBody.html(buildOptions(stepData));
    }

    function showRangeStep(stepData) {
        stepBox.find("h4").html("<span class=\"previous-step float-start pointer \">âªï¸</span>" + stepData.question + "<span class=\"next-step float-end pointer\">â©</step>");
        stepBody.html("<input type=\"range\" name=\"year\" class=\"year form-range\" min=\"1900\" max=\"2023\" id=\"customRange2\"><span class=\"rangeDisplay\">XXXX</span>");
    }

    function showVerifyStep(stepData) {
        let html = "";
        stepBox.find("h4").html("<span class=\"pointer previous-step float-start \">âªï¸</span>" + stepData.question + "<span class=\"pointer next-step float-end\">ðŸ’š</step>");
        html = html + "<ul>";
        html = html + "<li class=\"pointer\" onclick=\"showStep(0)\">ðŸŽ¤" + (vote.artist || "no answer") + "</li>";
        html = html + "<li class=\"pointer\" onclick=\"showStep(1)\">ðŸ’¿" + (vote.album || "no answer") + "</li>";
        html = html + "<li class=\"pointer\" onclick=\"showStep(2)\">ðŸŽ¼" + (vote.title || "no answer") + "</li>";
        html = html + "<li class=\"pointer\" onclick=\"showStep(3)\">ðŸ“†" + (vote.year || "no answer") + "</li>";
        html = html + "</ul>";
        stepBody.html(html);
    }

    function showResultStep() {
        isFinished = true;
        maxMs      = maxSongMs;
        $.ajax({
            url            : "/?context=vote&action=vote",
            method         : "POST",
            data           : {
                artist : vote.artist,
                title  : vote.title,
                year   : vote.year,
                album  : vote.album
            },
            cache          : false,
            "content-type" : "application/json",
            success        : function (response) {
                nextButton.removeClass("d-none");
                progressbar.removeClass("bg-success").removeClass("bg-warning").removeClass("bg-danger").removeClass("progress-bar-striped").removeClass("progress-bar-animated").addClass("bg-primary");
                let html = "";
                stepBox.find("h4").html("You've received " + response.points + " points");
                html = html + "<ul>";
                html = html + "<li  class=\"" + response.answers[1].colour + "\">ðŸŽ¤" + response.move.song.artist + "</li>";
                html = html + "<li  class=\"" + response.answers[3].colour + "\">ðŸ’¿" + response.move.song.album + "</li>";
                html = html + "<li  class=\"" + response.answers[2].colour + "\">ðŸŽ¼" + response.move.song.title + "</li>";
                html = html + "<li  class=\"" + response.answers[4].colour + "\">ðŸ“†" + response.move.song.year + "</li>";
                html = html + "<li  class=\" btnNext\">Continue</li>";
                html = html + "</ul>";
                stepBody.html(html);
            }
        });
    }

    function showStep(step) {
        // HIDE ALL STEPS
        $("div.step").hide();

        // Set new step
        curStep = step;

        // Prepare some local vars.
        let stepData = stepSetup[step];

        switch (stepData.type) {
            case "select":
                showSelectStep(stepData);
                break;
            case "range":
                showRangeStep(stepData);
                break;
            case "verify":
                showVerifyStep(stepData);
                break;
            case "result":
                showResultStep(stepData);
                break;
        }

        // SHOW THE SPECIFIC STEP
        stepBox.fadeIn(200);
    }

    function incrementStep() {
        curStep++;
        showStep(curStep);
    }

    function decrementStep() {
        curStep--;
        showStep(curStep);
    }

    $(document).ready(function () {

        // SHOW FIRST STEP!
        showStep(curStep);

        $("body").delegate("ul>li.select", "click", function () {
            let datum    = $(this).html(),
                stepData = stepSetup[curStep],
                name     = stepData.key;

            vote[name] = datum;
            incrementStep();
        });
        $("body").delegate("input.year", "change", function () {
            let year = $(this).val();
            $("span.rangeDisplay").html(year);
            vote.year = year;
        });
        $("body").delegate(".btnNext", "click", function () {
            window.location.href = "/?context=game&view=ask&next";
        });

        $("body").delegate("div.step>h4>span.previous-step", "click", function () {
            decrementStep();
        });

        $("body").delegate("div.step>h4>span.next-step", "click", function () {
            incrementStep();
        });
    });

    function Sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    const progressbar     = $("#progressBar");
    const secondContainer = $("#seconds");
    const nextButton      = $("#next");
    const stepBox         = $("#stepBox");
    const stepBody        = $("#stepBody");

    let maxMs       = 60000,                  // Maximum time offset in ms
        curMs       = 0,                      // Current time offset in ms
        leftMs      = 0,                      // ms remaining from offset
        warnMs      = maxMs * 0.15,           // Remaining seconds that turn YELLOW
        dangerMs    = maxMs * 0.05,           // Remaining seconds that turn RED
        leftWarning = false,                  // Boolean flag to only once add the warning class.
        leftDanger  = false,                  // Boolean flag to only once add the warning class.
        ms          = 100,                    // Interval for progressbar to refresh. Lower>smoother but more performance required.
        isFinished  = false,                  // Boolean flag to NOT change colour of progress if the player already finished the vote.
        maxSongMs   = <?=$songDuration?>;     // The amount of ms in the song.

    function progress() {
        let div = maxMs;
        if (isFinished) {
            div = maxSongMs;
        }
        let curP = 100 * (curMs / div);
        console.log(curMs + " of " + maxMs + " are over. This is " + curP + "%");
        progressbar.attr("style", "width: " + curP + "%");
        progressbar.attr("aria-valuenow", curP);
        if (! isFinished && ! leftWarning && maxMs - curMs <= warnMs) {
            leftWarning = true;
            progressbar.removeClass("bg-success").addClass("bg-warning");
        }
        if (! isFinished && ! leftDanger && maxMs - curMs <= dangerMs) {
            leftDanger = true;
            progressbar.removeClass("bg-success").addClass("bg-danger");
        }
        curMs += ms;
        if (curP >= 100) {
            window.clearTimeout(progressInterval);
            window.location.href = '<?=$hostName?>?context=game&view=ask&next=1';
        }
    }

    let progressInterval = window.setInterval(progress, ms);
</script>

</body>
</html>