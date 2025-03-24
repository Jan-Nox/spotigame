<?php declare(strict_types = 1);
namespace noxkiwi\spotigame;

use noxkiwi\core\Helper\LinkHelper;
use noxkiwi\spotigame\Entity\AbstractEntity;
use noxkiwi\spotigame\GameEntity\Player\Player;
use noxkiwi\spotigame\GameEntity\Question\AbstractQuestion;
use noxkiwi\spotigame\GameEntity\Sitting\Sitting;
use noxkiwi\spotigame\MediaEntity\Song\Song;
use noxkiwi\spotigame\GameEntity\Step\Step;

/**
 * I am the spotigame App.
 *
 * @package      noxkiwi\spotigame
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2024 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
class AnsweredSetup extends StepSetup
{
    public $userAnswers;
}
