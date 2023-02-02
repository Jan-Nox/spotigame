<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Question;

use noxkiwi\core\Request;
use noxkiwi\spotigame\Answer\Answer;
use noxkiwi\spotigame\Entity\AbstractEntity;
use noxkiwi\spotigame\Song\Song;
use noxkiwi\spotigame\Vote\Vote;
use noxkiwi\translator\Traits\TranslatorTrait;

/**
 * I am an abstract Question.
 *
 * @package      noxkiwi\spotigame\Player
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
abstract class AbstractQuestion extends AbstractEntity
{
    use TranslatorTrait;

    protected const PARAM_NAME  = '';
    public const    QUESTION_ID = 42;
    public string  $question;
    protected Song $song;
    public string  $emoji;
    public string  $type;
    public ?array  $options;
    public string  $param;

    public function __construct(Song $song)
    {
        $this->id       = static::QUESTION_ID;
        $this->question = $this->translate($this->question);
        $this->song     = $song;
    }

    /**
     * I will create the front end for the Player to use according to the given $song.
     *
     * I will NOT publish any data on the correct answers to the Response object or the output.
     *
     * @param \noxkiwi\spotigame\Song\Song $song
     *
     * @return string
     */
    abstract public function ask(Song $song): string;

    /**
     * I will create an Answer object for $this Question.
     *
     * The result inside the Answer will be determined by validating the given $request against the $song.
     *
     * @param \noxkiwi\spotigame\Vote\Vote $vote
     * @param \noxkiwi\core\Request        $request
     *
     * @return \noxkiwi\spotigame\Answer\Answer
     */
    abstract public function validate(Vote $vote, Request $request): Answer;

    /**
     * I will create a basic Answer object for the given $vote and $response.
     *
     * The Answer tho will NOT be evaluated. Also, it will not contain the correct answer.
     *
     * @param \noxkiwi\spotigame\Vote\Vote $vote
     * @param \noxkiwi\core\Request        $request
     *
     * @return \noxkiwi\spotigame\Answer\Answer
     */
    protected function prepareAnswer(Vote $vote, Request $request): Answer
    {
        $answer             = new Answer();
        $answer->colour     = Answer::COLOUR_WRONG;
        $answer->voteId     = $vote->id;
        $answer->input      = $request->get(static::PARAM_NAME, ':NOT_ANSWERED:');
        $answer->points     = 0;
        $answer->questionId = static::QUESTION_ID;

        return $answer;
    }
}
