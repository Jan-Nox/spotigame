<?php declare(strict_types=1);

namespace noxkiwi\spotigame\GameEntity\Vote;

use JetBrains\PhpStorm\Pure;
use noxkiwi\dataabstraction\Entry;
use noxkiwi\spotigame\Entity\AbstractEntity;
use noxkiwi\spotigame\GameEntity\Move\Move;
use noxkiwi\spotigame\GameEntity\Question\AbstractQuestion;
use noxkiwi\spotigame\Model\VoteModel;

/**
 * I am an abstract Vote.
 *
 * @package      noxkiwi\spotigame\GameEntity\Vote
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
abstract class AbstractVote extends AbstractEntity {
    protected const TYPE = 'vote';
    public int $points;
    /** @var \noxkiwi\spotigame\Answer\Answer[] */
    public array $answers;

    // The used fields.
    public string $answerCorrect = '';
    public string $answerInput = '';
    public Move $Move;
    public AbstractQuestion $Question;

    public function save() {
        $entry = new Entry(VoteModel::getInstance(), [
            'vote_flags'  => 1,
            'vote_points' => 0,
            'move_id'     => $this->Move->id,
            'player_id'   => $this->Move->Player->id,
        ]);


        $entry->save();

        $this->setId((int)$entry->vote_id);
    }

    #[Pure] public function __toString(): string {
        return <<<XML
<Vote
    id="{$this->id}"
>
    $this->Move
</Vote>
XML;
    }
}
