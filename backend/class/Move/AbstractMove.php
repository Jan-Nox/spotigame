<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Move;

use noxkiwi\cache\Cache;
use noxkiwi\core\Request;
use noxkiwi\core\Traits\LanguageImprovementTrait;
use noxkiwi\dataabstraction\Entry;
use noxkiwi\spotigame\Entity\AbstractEntity;
use noxkiwi\spotigame\Model\PlayerModel;
use noxkiwi\spotigame\Model\VoteModel;
use noxkiwi\spotigame\Question\AlbumMultipleChoice;
use noxkiwi\spotigame\Question\ArtistMultipleChoice;
use noxkiwi\spotigame\Question\ReleaseYearRange;
use noxkiwi\spotigame\Question\ResultQuestion;
use noxkiwi\spotigame\Question\TitleMultipleChoice;
use noxkiwi\spotigame\Question\VerifyQuestion;
use noxkiwi\spotigame\Sitting\Sitting;
use noxkiwi\spotigame\Song\Song;
use noxkiwi\spotigame\Vote\Vote;

/**
 * I am an abstract Move in the game.
 *
 * @package      noxkiwi\spotigame\Move
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
abstract class AbstractMove extends AbstractEntity
{
    use LanguageImprovementTrait;

    public Song    $song;
    public Sitting $sitting;

    /**
     * @param \noxkiwi\spotigame\Vote\Vote $vote
     * @param \noxkiwi\spotigame\Move\Move $move
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
        $voteEntry->player_id   = $vote->player->getId();
        $voteEntry->move_id     = $move->id;
        $voteEntry->save();
        $questions = $move->getQuestions();
        $vote->id  = (int)$voteEntry->vote_id;
        /** @var \noxkiwi\spotigame\Question\AbstractQuestion[] $questions */
        // @todo: This array needs to come from the sitting where the questions and points are set up!
        foreach ($questions as $question) {
            $answer = $question->validate($vote, Request::getInstance());
            $answer->store();
            $voteEntry->vote_points += $answer->points;
            $vote->answers[]        = $answer;
        }
        $voteEntry->save();
        // STORE POINTS TO PLAYER
        $player                = PlayerModel::expect($vote->player->getId());
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
     * @return array
     */
    public function getQuestions(): array
    {
        return [
            new ArtistMultipleChoice($this->song),
            new AlbumMultipleChoice($this->song),
            new TitleMultipleChoice($this->song),
            new ReleaseYearRange($this->song)
        ];
    }

    public function buildSetup(): array
    {
        $questions   = $this->getQuestions();
        $questions[] = new VerifyQuestion($this->song);
        $questions[] = new ResultQuestion($this->song);

        return $questions;
    }
}
