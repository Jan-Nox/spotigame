<?php declare(strict_types = 1);
namespace noxkiwi\spotigame;

use Exception;
use noxkiwi\core\Environment;
use noxkiwi\core\Helper\FrontendHelper;
use noxkiwi\core\Helper\JsonHelper;
use noxkiwi\core\Path;
use noxkiwi\core\Session;
use noxkiwi\translator\Translator;
use function chr;
use function print_r;
use function var_dump;

try {
    $noAnswer     = Translator::getInstance()->translate('NO_ANSWER');
    $stepSetup    = JsonHelper::encode((array)$data->get('stepSetup', []));
    $hostName     = Environment::getInstance()->get('server>hostname', 'https://spotigame.nox.kiwi/');
    $sitting      = $data->get('sitting');
    $player       = $data->get('player');
    $dbg          = chr(10) . chr(10) . "SITTING";
    $dbg          .= print_r($sitting, true);
    $dbg          .= chr(10) . chr(10) . "SESSION";
    $dbg          .= print_r(Session::getInstance()->get(), true);
    $dbg          .= chr(10) . chr(10) . "SONG";
    $dbg          .= print_r($data->get('song'), true);
    $song         = $data->get('song');
    $songDuration = $song->duration - 2000;
    $a            = App::getInstance();
} catch (Exception $e) {
    var_dump($e);
    die('Tja!');
}
echo <<<HTML
    {$a->returnIt(FrontendHelper::parseFile(Path::getInheritedPath('frontend/element/player/mini.php'), $player))}
    <hr />
    {$a->returnIt(FrontendHelper::parseFile(Path::getInheritedPath('frontend/element/sitting/progressbar.php'), $sitting))}
    <hr />
    {$a->returnIt(FrontendHelper::parseFile(Path::getInheritedPath('frontend/element/game/progressbar.php'), $player))}
    <hr />
    {$a->returnIt(FrontendHelper::parseFile(Path::getInheritedPath('frontend/element/game/stepbody.php'), $player))}
    <hr />


<script>
$(document).ready(function() {
    let stepSetup = $stepSetup;

    let curStep = 0,
        vote    = {};

    function buildOptions(stepData) {
        let html        = "<ul>",
            optionCount = stepData.options.length;
        for (let optionIndex = 0; optionIndex <= optionCount - 1; optionIndex++) {
            let myOption = stepData.options[optionIndex],
                selected = "";
            if ((vote[stepData.param] || "") === myOption) {
                selected = "active";
            }
            html = html + "<li class=\"select " + selected + "\">" + myOption + "</li>";
        }
        html = html + "</ul>";

        return html;
    }

    function showSelectStep(stepData) {
        stepBox.find("h4").html("<span class=\"previous-step float-start pointer \">⏪️</span>" + stepData.question + "<span class=\"next-step float-end pointer\">⏩</step>");
        stepBody.html(buildOptions(stepData));
    }

    function showRangeStep(stepData) {
        stepBox.find("h4").html("<span class=\"previous-step float-start pointer \">⏪️</span>" + stepData.question + "<span class=\"next-step float-end pointer\">⏩</step>");
        stepBody.html(`
<div class="input-group mb-3">
  <span class="input-group-text rangeMover" data-offset="-10" id="basic-addon1">-10</span>
  <span class="input-group-text rangeMover" data-offset="-1" id="basic-addon1">-1</span>
  <input type="number" min="1900" max="2023" value="` + (vote.year || 1991) + `" class="year form-control rangeDisplay text-center">
  <span class="input-group-text rangeMover" data-offset="1" id="basic-addon2">+1</span>
  <span class="input-group-text rangeMover" data-offset="10" id="basic-addon2">+10</span>
</div>
<input type="range" name="year" class="year form-range"  value="` + (vote.year || 1991) + `" min="1900" max="2023" id="customRange2">
`);
    }

    function showVerifyStep(stepData) {
        let html      = "",
            stepCount = stepSetup.length - 1;

        stepBox.find("h4").html("<span class=\"pointer previous-step float-start \">⏪️</span>" + stepData.question + "<span class=\"pointer next-step float-end\">💚</step>");
        html = html + "<ul>";

        for (let stepIndex = 0; stepIndex <= stepCount; stepIndex++) {
            let stepData = stepSetup[stepIndex];

            if (stepData.id === -1) {
                continue;
            }

            html = html + "<li class=\"pointer\" onclick=\"showStep(" + stepIndex + ")\">" + stepData["emoji"] + (vote[stepData["param"]] || "$noAnswer") + "</li>";
        }
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
                let html        = "<ul>",
                    answerCount = response.answers.length - 1;
                stepBox.find("h4").html("You've received " + response.points + " points");
                for (let answerIndex = 0; answerIndex <= answerCount; answerIndex++) {
                    let answerData = response.answers[answerIndex],
                        stepData   = stepSetup[answerIndex];

                    html = html + "<li class=\"" + response.answers[answerIndex].colour + "\" class=\"pointer\">" + stepData["emoji"] + answerData.correct + "</li>";
                }
                html = html + "<li  id=\"btnNext\">Continue</li>";
                stepBody.html(html + "</ul>");
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

    function floorCeil(floor, value, ceil) {
        return Math.max(floor, Math.min(value, ceil));
    }

    function incrementStep() {
        let nuStep = floorCeil(0, curStep + 1, stepSetup.length - 1);
        if (nuStep === curStep) {
            return;
        }
        curStep = nuStep;
        showStep(curStep);
    }

    function decrementStep() {
        let nuStep = floorCeil(0, curStep - 1, stepSetup.length - 1);
        if (nuStep === curStep) {
            return;
        }
        curStep = nuStep;
        showStep(curStep);
    }

    $(document).ready(function () {

        // SHOW FIRST STEP!
        showStep(curStep);

        $("body").delegate(".rangeMover", "click", function () {
            let display = $(".rangeDisplay"),
                val     = parseInt(display.val()),
                offset  = parseInt($(this).data("offset")),
                floor   = parseInt(display.attr("min")),
                ceil    = parseInt(display.attr("max")),
                nuVal   = floorCeil(floor, val + offset, ceil),
                slider  = $(".year");

            display.val(nuVal);
            slider.val(nuVal);
            vote.year = nuVal;

        });

        $("body").delegate("ul>li.select", "click", function () {
            let datum    = $(this).html(),
                stepData = stepSetup[curStep],
                name     = stepData.param;

            vote[name] = datum;
            incrementStep();
        });
        $("body").delegate("input.year", "change", function () {
            let year   = $(this).val(),
                slider = $(".year");
            $(".rangeDisplay").val(year);
            vote.year = year;
            slider.val(year);
        });
        $("body").delegate("#btnNext", "click", function () {
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
        maxSongMs   = $songDuration;          // The amount of ms in the song.

    function progress() {
        let div = maxMs;
        if (isFinished) {
            div = maxSongMs;
        }
        let curP = 100 * (curMs / div);
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
            window.location.href = '$hostName?context=game&view=ask&next=1';
        }
    }

    let progressInterval = window.setInterval(progress, ms);
});
</script>
HTML;