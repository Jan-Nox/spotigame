<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Question;

use noxkiwi\core\Request;
use noxkiwi\spotigame\Answer\Answer;
use noxkiwi\spotigame\Song\Song;
use noxkiwi\spotigame\Vote\Vote;

/**
 * I am the question that lets the Player select the Song's release year.
 * An exact match will grant the player two points,
 * a ranged match will grant one point.
 *
 * A ranged match is:
 * (player_year <= (song_year+x) && player_year >= (song_year-x))  === TRUE
 *
 *
 * @package      noxkiwi\spotigame\Player
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class ReleaseYearRange extends AbstractQuestion
{
    protected const PARAM_NAME  = 'year';
    public const QUESTION_ID = 4;

    /**
     * @inheritDoc
     */
    public function ask(Song $song): string
    {
        return 'NOT YET, JAN!';
    }

    /**
     * @inheritDoc
     */
    public function validate(Song $song, Vote $vote, Request $request): Answer
    {
        $answer          = $this->prepareAnswer($vote, $request);
        $answer->correct = (string)$song->year;
        // Release year was matched exactly?
        if ($song->year === (int)$answer->input) {
            $answer->points += 2;
            $answer->colour = Answer::COLOUR_RIGHT;

            return $answer;
        }
        $minYear = $song->year - 5;
        $maxYear = $song->year + 5;
        // Release year was matched within threshold (negative)?
        if ($answer->input >= $minYear && $answer->input <= $song->year) {
            $answer->colour = Answer::COLOUR_PARTIAL;
            $answer->points++;
        }
        // Release year was matched within threshold (positive)?
        if ($answer->input <= $maxYear && $answer->input >= $song->year) {
            $answer->colour = Answer::COLOUR_PARTIAL;
            $answer->points++;
        }

        return $answer;
    }
}
