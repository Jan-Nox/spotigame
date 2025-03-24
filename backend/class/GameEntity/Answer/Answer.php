<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\GameEntity\Answer;

use JetBrains\PhpStorm\Pure;
use noxkiwi\dataabstraction\Entry;
use noxkiwi\spotigame\Entity\AbstractEntity;
use noxkiwi\spotigame\GameEntity\Question\AbstractQuestion;
use noxkiwi\spotigame\GameEntity\Vote\Vote;
use noxkiwi\spotigame\Model\AnswerModel;

/**
 * I am a real Vote.
 *
 * @package      noxkiwi\spotigame\GameEntity\Vote
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class Answer extends AbstractEntity
{
    protected const TYPE = 'answer';
    public int $points;
    /** @var \noxkiwi\spotigame\Answer\Answer[] */
    public array $answers;

    // The used fields.
    public string $answerCorrect = '';
    public string $answerInput = '';
    public array $result;
    public array $correct;
    public Vote $Vote;
    public AbstractQuestion $Question;

    public function save() {
        $Answer = new Entry(AnswerModel::getInstance());
        $this->logError($this->__toString());
        $Answer->set([
            'answer_flags'   => 0,
            'answer_input'   => $this->answerInput,
            'answer_correct' => $this->answerCorrect,
            'answer_points'  => $this->points,
            'vote_id'        => $this->Vote->id,
            'question_id'    => $this->Question->id,
        ]);
        $Answer->save();
    }

    #[Pure] public function __toString(): string {
        return <<<XML
<Answer id="{$this->id}" correct="{$this->answerCorrect}" input="{$this->answerInput}" />
XML;
    }
}
