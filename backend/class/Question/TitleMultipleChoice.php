<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Question;

use noxkiwi\core\Request;
use noxkiwi\spotigame\Answer\Answer;
use noxkiwi\spotigame\Song\Song;
use noxkiwi\spotigame\Vote\Vote;

/**
 * I am the question that lets the Player select one of multiple title.
 * Only the correct selection will grant one single point to the Player.
 *
 * @package      noxkiwi\spotigame\Player
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class TitleMultipleChoice extends AbstractQuestion
{
    protected const PARAM_NAME  = 'title';
    public const    QUESTION_ID = 2;

    /**
     * @inheritDoc
     */
    public function validate(Song $song, Vote $vote, Request $request): Answer
    {
        $answer          = $this->prepareAnswer($vote, $request);
        $answer->correct = $song->title;
        // @todo: Use the spotify URI instead of the artist name!
        if ($answer->correct === $answer->input) {
            $answer->colour = Answer::COLOUR_RIGHT;
            $answer->points++;
        }

        return $answer;
    }

    /**
     * @inheritDoc
     */
    public function ask(Song $song): string
    {
        return 'NOT YET';
    }
}
