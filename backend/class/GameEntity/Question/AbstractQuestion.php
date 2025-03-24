<?php declare(strict_types=1);

namespace noxkiwi\spotigame\GameEntity\Question;

use noxkiwi\core\Request;
use noxkiwi\spotigame\Entity\AbstractEntity;
use noxkiwi\spotigame\GameEntity\Answer;
use noxkiwi\spotigame\GameEntity\GameMode\GameMode;
use noxkiwi\spotigame\GameEntity\Vote\Vote;
use noxkiwi\spotigame\MediaEntity\Song\Song;

/**
 * I am an abstract Question.
 *
 * @package      noxkiwi\spotigame\GameEntity\Player
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
abstract class AbstractQuestion extends AbstractEntity {

    public const   QUESTION_ID = -1;
    public const   QUESTION = '';
    protected Song $song;
    protected GameMode $GameMode;
    // FIELDS USED IN SERIALIZED OBJECTS BY JS!
    public string $emoji;
    public ?array $options;
    public string $decision;
    public array $result;

    public function __construct(Song $song, GameMode $GameMode) {
        parent::__construct();
        $this->id = static::QUESTION_ID;
        $this->name = static::QUESTION;
        $this->song = $song;
        $this->GameMode = $GameMode;
    }

    /**
     * I will create an Answer object for $this Question.
     *
     * The result inside the Answer will be determined by validating the given $request against the $song.
     *
     * @param \noxkiwi\spotigame\GameEntity\Vote\Vote $vote
     * @param \noxkiwi\core\Request $request
     *
     * @return \noxkiwi\spotigame\Answer\Answer
     */
    abstract public function validate(Vote $vote, Request $request): Answer;

    /**
     * I will create a basic Answer object for the given $vote and $response.
     *
     * The Answer tho will NOT be evaluated. Also, it will not contain the correct answer.
     *
     * @param \noxkiwi\spotigame\GameEntity\Vote\Vote $vote
     * @param \noxkiwi\core\Request $request
     *
     * @return \noxkiwi\spotigame\Answer\Answer
     */
    protected function prepareAnswer(Vote $vote, Request $request): Answer {
        $answer = new Answer();
        $answer->colour = Answer::COLOUR_WRONG;
        $answer->voteId = $vote->id;
        $answer->input = $request->get($this->param, ':NOT_ANSWERED:');
        $answer->points = 0;
        $answer->questionId = static::QUESTION_ID;

        return $answer;
    }

    public function __toString(): string {
        return <<<XML
<question
  id="{$this->id}">
</question>
XML;
    }
}
