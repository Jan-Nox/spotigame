<?php declare(strict_types=1);

namespace noxkiwi\spotigame\GameEntity\Move;

use JetBrains\PhpStorm\Pure;
use noxkiwi\cache\Cache;
use noxkiwi\core\Request;
use noxkiwi\core\Traits\LanguageImprovementTrait;
use noxkiwi\dataabstraction\Entry;
use noxkiwi\spotigame\Entity\AbstractEntity;
use noxkiwi\spotigame\GameEntity\GameMode\GameMode;
use noxkiwi\spotigame\GameEntity\Player\Player;
use noxkiwi\spotigame\GameEntity\Question\AbstractQuestion;
use noxkiwi\spotigame\GameEntity\Question\AlbumMultipleChoice;
use noxkiwi\spotigame\GameEntity\Question\ArtistMultipleChoice;
use noxkiwi\spotigame\GameEntity\Question\TitleMultipleChoice;
use noxkiwi\spotigame\GameEntity\Sitting\Sitting;
use noxkiwi\spotigame\GameEntity\Vote\Vote;
use noxkiwi\spotigame\MediaEntity\Song\Song;
use noxkiwi\spotigame\Model\MoveModel;
use noxkiwi\spotigame\Model\PlayerModel;
use noxkiwi\spotigame\Model\VoteModel;
use noxkiwi\spotigame\StepSetup;

/**
 * I am a real Move in the game.
 *
 * @package      noxkiwi\spotigame\GameEntity\Move
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
class Move extends AbstractEntity {
    use LanguageImprovementTrait;
    public Song    $Song;
    public Sitting $Sitting;
    public Player  $Player;
    public string  $sitting_code = 'ASDA';
    /** @var AbstractQuestion[] */
    public $Questions = [];


    /**
     * @param \noxkiwi\spotigame\GameEntity\Vote\Vote $vote
     * @param \noxkiwi\spotigame\GameEntity\Move\Move $move
     *
     * @throws \noxkiwi\core\Exception\InvalidArgumentException
     * @throws \noxkiwi\dataabstraction\Exception\EntryMissingException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return \noxkiwi\dataabstraction\Entry
     */
    public function evaluate(Vote $vote, Move $move): Entry
    {
        $flags                  = 0;
        $points                 = 0;
        $voteModel              = VoteModel::getInstance();
        $voteEntry              = $voteModel->getEntry();
        $voteEntry->vote_flags  = $flags;
        $voteEntry->vote_points = $points;
        $voteEntry->player_id   = $vote->Player->getId();
        $voteEntry->move_id     = $move->id;
        $voteEntry->save();
        $questions = $move->getQuestions();
        $vote->id  = (int)$voteEntry->vote_id;
        /** @var \noxkiwi\spotigame\GameEntity\Question\AbstractQuestion[] $questions */
        // @todo: This array needs to come from the sitting where the questions and points are set up!
        foreach ($questions as $question) {
            $answer = $question->validate($vote, Request::getInstance());
            $answer->store();
            $voteEntry->vote_points += $answer->points;
            $vote->answers[]        = $answer;
        }
        $voteEntry->save();
        // STORE POINTS TO PLAYER
        $player                = PlayerModel::expect($vote->Player->getId());
        $player->player_points += $voteEntry->vote_points;
        $player->save();

        return $voteEntry;
    }

    /**
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return void
     */
    public function end(): void
    {
        $cache = Cache::getInstance();
        $cache->clearKey('MOVE', 'MOVE');
    }

    /**
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return AbstractQuestion[]
     */
    public function getQuestions(Song $Song, GameMode $GameMode): array
    {
        $Questions = [];
        // BUILD QUESTIONS
        $Question1 = new ArtistMultipleChoice($Song, $GameMode);
        $Questions[$Question1->uuid] = $Question1;

        $Question2 = new AlbumMultipleChoice($Song, $GameMode);
        $Questions[$Question2->uuid] = $Question2;

        $Question3 = new TitleMultipleChoice($Song, $GameMode);
        $Questions[$Question3->uuid] = $Question3;

        return $Questions;
    }

    public function buildSetup(Player $Player, GameMode $GameMode): StepSetup
    {
        $setup                    = new StepSetup;
        $setup->Questions         = $this->getQuestions($this->Song, $GameMode);
        $setup->Song              = $this->Song;
        $setup->Player            = $Player;
        $setup->Sitting           = $this->Sitting;

        return $setup;
    }

    /** @var AnswerWrapper[] */
    public array $ResultAnswers = [];

    public static function expect(int $moveId, GameMode $GameMode): self {

        $MoveEntity = MoveModel::expect($moveId);
        $Move = new self();
        $Move->id = $moveId;
        $Move->Song = Song::expect($MoveEntity->song_id);
        $Move->Questions = $Move->getQuestions($Move->Song, $GameMode);

        return $Move;
    }

    #[Pure] public function __toString(): string {
        return <<<XML
<Move>
    {$this->Song}
    <Questions>
        {$this->gQuestions()}
    </Questions>
</Move>
XML;
    }

    private function gQuestions(): string {
        $a = '';
        foreach ($this->Questions as $Question) {
            $a .= $Question;
        }
        return $a;
    }

}
