<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Sitting;

use noxkiwi\core\Session;
use noxkiwi\database\Database;
use noxkiwi\spotigame\Exception\GameOverException;
use noxkiwi\spotigame\GameMode\AbstractGameMode;
use noxkiwi\spotigame\Model\MoveModel;
use noxkiwi\spotigame\Model\SittingModel;
use noxkiwi\spotigame\Model\SittingPlayerModel;
use noxkiwi\spotigame\Model\SongModel;
use noxkiwi\spotigame\Move\AbstractMove;
use noxkiwi\spotigame\Move\Move;
use noxkiwi\spotigame\Interfaces\PlayerInterface;
use noxkiwi\spotigame\Player\Player;
use noxkiwi\spotigame\Song\Song;
use const E_WARNING;

/**
 * I am a real Sitting.
 *
 * @package      noxkiwi\spotigame\Player
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class Sitting extends AbstractSitting
{
    public int              $sittingId;
    public AbstractGameMode $gameMode;
    private Session         $storage;
    public bool             $finished = false;

    public function __construct()
    {
        $this->storage = Session::getInstance();
    }

    /**
     * @throws \noxkiwi\core\Exception\InvalidArgumentException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return \noxkiwi\spotigame\Sitting\Sitting
     */
    public function create(): self
    {
        // Create the Sitting
        $sittingModel                = SittingModel::getInstance();
        $sittingEntry                = $sittingModel->getEntry();
        $sittingEntry->sitting_code  = $this->getName();
        $sittingEntry->sitting_flags = 0;
        $sittingEntry->sitting_steps = $this->stepCount;
        $sittingModel->saveEntry($sittingEntry);
        // Store Players for the Sitting
        $spModel = SittingPlayerModel::getInstance();
        foreach ($this->players as $player) {
            $spEntry                       = $spModel->getEntry();
            $spEntry->sitting_id           = $sittingEntry->sitting_id;
            $spEntry->player_id            = $player->getId();
            $spEntry->sitting_player_flags = 0;
            $spModel->saveEntry($spEntry);
        }
        // Set ID
        $this->setId((int)$sittingEntry->sitting_id);
        $this->sittingId = (int)$sittingEntry->sitting_id;
        // Add Songs. Lets just exaggerate it and add 100 songs.
        for ($relativeStep = 1; $relativeStep <= $this->stepCount; $relativeStep++) {
            $this->generateMove($relativeStep);
        }

        return $this;
    }

    public function setRelativeStep(int $step): void
    {
        $this->storage->set('CURRENT_STEP', $step);
    }

    public function getRelativeStep(): int
    {
        return (int)$this->storage->get('CURRENT_STEP', 0);
    }

    public static function expect(int $sittingId): self
    {
        $entry  = SittingModel::expect($sittingId);
        $result = new self();
        $result->setId((int)$entry->sitting_id);
        $result->stepCount = (int)$entry->sitting_steps;

        return $result;
    }

    /**
     * I will solely add the given $player to the Sitting.
     *
     * @param \noxkiwi\spotigame\Interfaces\PlayerInterface $player
     *
     * @return void
     */
    public function addPlayer(PlayerInterface $player): void
    {
        $this->players[] = $player;
    }

    /**
     * I will solely set the GameMode of the current Sitting.
     *
     * @param \noxkiwi\spotigame\GameMode\AbstractGameMode $gameMode
     *
     * @return void
     */
    public function setGameMode(AbstractGameMode $gameMode): void
    {
        $this->gameMode = $gameMode;
    }

    /**
     * I will solely publish the next Move of the Sitting.
     * @throws \noxkiwi\core\Exception\InvalidArgumentException
     * @throws \noxkiwi\dataabstraction\Exception\EntryMissingException
     * @throws \noxkiwi\database\Exception\DatabaseException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return \noxkiwi\spotigame\Move\AbstractMove
     */
    public function getNextMove(): AbstractMove
    {
        $current = $this->getRelativeStep();
        if ($current === 0) {
            $current = 1;
        }
        $this->setRelativeStep($current + 1);

        return $this->getCurrentMove();
    }

    private function generateMove(int $relativeStep): AbstractMove
    {
        $move          = new Move();
        $song          = SongModel::getRandom($this->getPlayedSongs());
        $move->sitting = $this;
        $move->song    = $song;
        // STORE THE CURRENT MOVE!!!
        $moveModel             = MoveModel::getInstance();
        $moveEntry             = $moveModel->getEntry();
        $moveEntry->move_flags = 1;
        $moveEntry->sitting_id = $move->sitting->id;
        $moveEntry->song_id    = $song->id;
        $moveEntry->move_step  = $relativeStep;
        $moveEntry->save();
        $move->id = (int)$moveEntry->move_id;

        return $move;
    }

    /**
     * @throws \noxkiwi\dataabstraction\Exception\EntryMissingException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @throws \noxkiwi\spotigame\Exception\GameOverException
     * @return \noxkiwi\spotigame\Move\AbstractMove
     */
    public function getCurrentMove(): AbstractMove
    {
        return $this->getStep($this->getRelativeStep());
    }

    /**
     * @throws \noxkiwi\dataabstraction\Exception\EntryMissingException
     * @throws \noxkiwi\database\Exception\DatabaseException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return \noxkiwi\spotigame\Player\Player[]
     */
    public function getUnfinishedPlayers(): array
    {
        $sql = <<<SQL
SELECT
	`sitting_player`.`player_id`
FROM
	`sitting_player`
WHERE TRUE
    AND `sitting_player`.`sitting_id` = $this->sittingId
    AND (`sitting_player`.`sitting_player_flags` & (2) = 0) 
SQL;
        $db  = Database::getInstance();
        $db->read($sql);
        $ps = $db->getResult();
        $r  = [];
        foreach ($ps as $p) {
            $r[] = Player::expect((int)$p['player_id']);
        }

        return $r;
    }

    public function finishRound(Player $player): void
    {
        $sql = <<<SQL
UPDATE
	`sitting_player`
SET
    `sitting_player`.`sitting_player_flags` = 3
WHERE TRUE
	AND `sitting_player`.`sitting_id` = $this->sittingId
	AND `sitting_player`.`player_id` = $player->playerId;
SQL;
        $db  = Database::getInstance();
        $db->read($sql);
        if (! empty($this->getUnfinishedPlayers())) {
            return;
        }
        $this->finalize();
    }

    public function finalize(): void
    {
        $sql = <<<SQL
UPDATE
	`sitting`
SET
    `sitting`.`sitting_flags` = 3
WHERE TRUE
	AND `sitting`.`sitting_id` = $this->sittingId;
SQL;
        $db  = Database::getInstance();
        $db->read($sql);
    }

    /**
     * @param int $moveStep
     *
     * @throws \noxkiwi\dataabstraction\Exception\EntryMissingException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @throws \noxkiwi\spotigame\Exception\GameOverException
     * @return \noxkiwi\spotigame\Move\AbstractMove
     */
    private function getStep(int $moveStep): AbstractMove
    {
        if ($moveStep === 0) {
            $moveStep = 1;
        }
        $aM = MoveModel::getInstance();
        $aM->addFilter('sitting_id', $this->id);
        $aM->addFilter('move_step', $moveStep);
        $aE = $aM->search();
        if (empty($aE)) {
            throw new GameOverException('Game is over, no steps left.', E_WARNING);
        }
        $aE            = $aE[0];
        $song          = Song::expect((int)$aE['song_id']);
        $move          = new Move();
        $move->sitting = $this;
        $move->song    = $song;
        $move->id      = (int)$aE['move_id'];

        return $move;
    }

    /**
     * @throws \noxkiwi\database\Exception\DatabaseException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return array
     */
    public function getPlayedSongs(): array
    {
        $db  = Database::getInstance();
        $sql = <<<SQL
SELECT
	`move`.`song_id`
FROM
	`move`
WHERE
	`move`.`sitting_id` = $this->id
ORDER BY
	`move`.`move_created` DESC;
SQL;
        $db->read($sql);
        $rows    = $db->getResult();
        $songIds = [0, 1];
        foreach ($rows as $row) {
            $songIds[] = (int)$row['song_id'];
        }

        return $songIds;
    }

    public function addSong(Sitting $song): void
    {
        // TODO: Implement addSong() method.
    }
}
