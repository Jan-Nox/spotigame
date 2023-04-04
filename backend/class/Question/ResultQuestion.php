<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Question;

use noxkiwi\core\Request;
use noxkiwi\spotigame\Answer\Answer;
use noxkiwi\spotigame\Vote\Vote;

/**
 * I am the question that lets the Player select one of multiple album.
 * Only the correct selection will grant one single point to the Player.
 *
 * @package      noxkiwi\spotigame\Player
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class ResultQuestion extends AbstractQuestion
{
    public const QUESTION = 'result';
    public string $emoji    = '?';
    public string $type     = 'result';

    /**
     * @inheritDoc
     */
    public function validate(Vote $vote, Request $request): Answer
    {
        $answer          = $this->prepareAnswer($vote, $request);
        $answer->correct = $this->song->album;
        // @todo: Use the spotify URI instead of the artist name!
        if ($answer->correct === $answer->input) {
            $answer->points++;
            $answer->colour = Answer::COLOUR_RIGHT;
        }

        return $answer;
    }
}
