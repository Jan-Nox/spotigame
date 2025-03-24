<?php declare(strict_types = 1);
namespace noxkiwi\spotigame;

use noxkiwi\spotigame\Entity\AbstractEntity;
use noxkiwi\spotigame\GameEntity\Player\Player;
use noxkiwi\spotigame\GameEntity\Question\AbstractQuestion;
use noxkiwi\spotigame\GameEntity\Sitting\Sitting;
use noxkiwi\spotigame\MediaEntity\Song\Song;

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
class StepSetup
{
    // The current step.
    public AbstractEntity $currentStep;
    public Sitting $Sitting;
    public Player $Player;
    public Song $Song;
    /** @var AbstractQuestion[] */
    public array $Questions;
}
