<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Question;

use noxkiwi\core\Request;
use noxkiwi\spotigame\Answer\Answer;
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
    public const    QUESTION_ID = 4;
    public const QUESTION = 'release_year_range';
    public string $emoji    = '🗓️';
    public string $type     = 'range';
    public string $param    = 'year';

    /**
     * @inheritDoc
     */
    public function validate(Vote $vote, Request $request): Answer
    {
        $answer          = $this->prepareAnswer($vote, $request);
        $answer->correct = (string)$this->song->year;
        // Release year was matched exactly?
        if ($this->song->year === (int)$answer->input) {
            $answer->points += 2;
            $answer->colour = Answer::COLOUR_RIGHT;

            return $answer;
        }
        $minYear = $this->song->year - 5;
        $maxYear = $this->song->year + 5;
        // Release year was matched within threshold (negative)?
        if ($answer->input >= $minYear && $answer->input <= $this->song->year) {
            $answer->colour = Answer::COLOUR_PARTIAL;
            $answer->points++;
        }
        // Release year was matched within threshold (positive)?
        if ($answer->input <= $maxYear && $answer->input >= $this->song->year) {
            $answer->colour = Answer::COLOUR_PARTIAL;
            $answer->points++;
        }

        return $answer;
    }
}
