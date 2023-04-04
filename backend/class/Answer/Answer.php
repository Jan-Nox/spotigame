<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Answer;

use noxkiwi\core\Constants\Bootstrap;
use noxkiwi\spotigame\Model\AnswerModel;

/**
 * I am a real Answer.
 *
 * @package      noxkiwi\spotigame\Album
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
class Answer extends AbstractAnswer
{
    public const  COLOUR_RIGHT   = 'correct';
    public const  COLOUR_WRONG   = 'wrong';
    public const  COLOUR_PARTIAL = 'partly';
    private const FLAG_CORRECT   = 2;
    private const FLAG_WRONG     = 4;
    private const FLAG_PARTIAL   = 8;
    public int    $voteId;
    public int    $questionId;
    public mixed  $input;
    public string $correct;
    public int    $points;
    public string $colour;

    /**
     * I will solely store the current Answer object into the database.
     * @throws \noxkiwi\core\Exception\InvalidArgumentException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return void
     */
    public function store(): void
    {
        $entry                 = AnswerModel::getInstance()->getEntry();
        $entry->answer_flags   = $this->buildFlags();
        $entry->vote_id        = $this->voteId;
        $entry->answer_input   = $this->input;
        $entry->answer_correct = $this->correct;
        $entry->answer_points  = $this->points;
        $entry->question_id    = $this->questionId;
        $entry->save();
        $this->id = (int)$entry->answer_id;
    }

    private function buildFlags(): int
    {
        $flags = 1;
        if ($this->colour === self::COLOUR_RIGHT) {
            $flags += self::FLAG_CORRECT;
        }
        if ($this->colour === self::COLOUR_WRONG) {
            $flags += self::FLAG_WRONG;
        }
        if ($this->colour === self::COLOUR_PARTIAL) {
            $flags += self::FLAG_PARTIAL;
        }

        return $flags;
    }
}
