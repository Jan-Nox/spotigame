<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Question;

use noxkiwi\core\Request;
use noxkiwi\spotigame\Answer\Answer;
use noxkiwi\spotigame\Song\Song;
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
final class AlbumMultipleChoice extends AbstractQuestion
{
    protected const PARAM_NAME  = 'album';
    public const    QUESTION_ID = 3;

    /**
     * @inheritDoc
     */
    public function validate(Song $song, Vote $vote, Request $request): Answer
    {
        $answer          = $this->prepareAnswer($vote, $request);
        $answer->correct = $song->album;
        // @todo: Use the spotify URI instead of the artist name!
        if ($answer->correct === $answer->input) {
            $answer->points++;
            $answer->colour = Answer::COLOUR_RIGHT;
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
