<?php declare(strict_types=1);

namespace noxkiwi\spotigame\GameEntity\Sitting;

use noxkiwi\core\Session;
use noxkiwi\database\Database;
use noxkiwi\spotigame\DataContract\LobbyDataContract;
use noxkiwi\spotigame\Entity\AbstractEntity;
use noxkiwi\spotigame\Exception\GameOverException;
use noxkiwi\spotigame\GameEntity\GameMode\AbstractGameMode;
use noxkiwi\spotigame\GameEntity\GameMode\GameMode;
use noxkiwi\spotigame\GameEntity\Move\Move;
use noxkiwi\spotigame\GameEntity\Player\Player;
use noxkiwi\spotigame\MediaEntity\Song\Song;
use noxkiwi\spotigame\Model\MoveModel;
use noxkiwi\spotigame\Model\SittingModel;
use noxkiwi\spotigame\Model\SittingPlayerModel;
use noxkiwi\spotigame\Model\SongModel;
use const E_WARNING;

/**
 * I am a real Sitting.
 *
 * @package      noxkiwi\spotigame\GameEntity\Player
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class Sitting extends AbstractEntity {
    protected const TYPE = 'sitting';
    /** @var \noxkiwi\spotigame\GameEntity\Player\Player[] */
    public array $players;
    /** @var \noxkiwi\spotigame\MediaEntity\Song\Song[] */
    public array $songs;
    public int $stepCount;
    public int $timeout;
    public string $code;
    public int $sittingId;
    public GameMode $gameMode;
    private Session $session;
    private MoveModel $moveModel;
    private SittingPlayerModel $sittingPlayerModel;
    private SittingModel $model;
    public bool $finished = false;
    /** @var Song[] */
    private array $playedSongs = [];
    public Player $Player;

    public function __construct() {
        parent::__construct();
        $this->session = Session::getInstance();
        $this->model = SittingModel::getInstance();
        $this->moveModel = MoveModel::getInstance();
        $this->sittingPlayerModel = SittingPlayerModel::getInstance();
    }

    /**
     * @return \noxkiwi\spotigame\GameEntity\Sitting\Sitting
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @throws \noxkiwi\core\Exception\InvalidArgumentException
     */
    public function create(LobbyDataContract $Lobby): self {
        $this->timeout = $Lobby->timeout;
        $this->stepCount = $Lobby->songs;
        $this->setName(uniqid("SPOTIGAME_SITTING_"));

        $mode = new GameMode();
        $mode->setName('regular');
        $this->setGameMode($mode);

        $entry = $this->model->getEntry();
        $entry->sitting_code = $this->getName();
        $entry->sitting_flags = 0;
        $entry->sitting_steps = $this->stepCount;

        $this->model->saveEntry($entry);

        // Set ID
        $this->setId((int)$entry->sitting_id);
        $this->sittingId = (int)$entry->sitting_id;


        $this->log->debug('Creating a new Sitting:');
        $this->log->debug('  -- timeout: ' . $this->timeout);
        $this->log->debug('  -- stepCount: ' . $this->stepCount);

        // Add Moves to the Sitting
        for ($move = 1; $move <= $this->stepCount; $move++) {
            $this->generateMove($move);
        }

        return $this;
    }


    public function join(Player $player): void {
        // @todo: Check if player is already on the lobby....

        $this->sittingPlayerModel->save([
            'sitting_id'           => $this->id,
            'player_id'            => $player->id,
            'sitting_player_flags' => 1
        ]);

        // Tell session that we are in a specific sitting at step 0.
        $this->session->set('CURRENT_STEP', 0);
        $this->session->set('SITTING_ID', $this->sittingId);
        $this->session->set('STEP_COUNT', $this->stepCount);
    }

    /**
     * I will solely publish the next Move of the Sitting.
     * @return \noxkiwi\spotigame\GameEntity\Move\Move
     * @throws \noxkiwi\dataabstraction\Exception\EntryMissingException
     * @throws \noxkiwi\database\Exception\DatabaseException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @throws \noxkiwi\core\Exception\InvalidArgumentException
     */
    public function getNextMove(): Move {
        $current = $this->getRelativeStep();
        if ($current === 0) {
            $current = 1;
        }
        $this->setRelativeStep($current + 1);

        return $this->getCurrentMove();
    }

    public static function expect(int $sittingId): self {
        $entry = SittingModel::expect($sittingId);
        $result = new self();
        $result->setId((int)$entry->sitting_id);
        $result->stepCount = (int)$entry->sitting_steps;
        $result->code = $entry->sitting_code;

        return $result;
    }

    public static function expectFromCode(string $sittingCode): self {
        $sittingModel = SittingModel::getInstance();
        $sittingModel->addFilter('sitting_code', $sittingCode);
        $sittingModel->search();
        $result = $sittingModel->getResult();

        return self::expect($result[0]['sitting_id']);
    }

    // Decide what to do with the following methods...

    protected function setRelativeStep(int $step): void {
        $this->session->set('CURRENT_STEP', $step);
    }

    protected function getRelativeStep(): int {
        return (int)$this->session->get('CURRENT_STEP', 0);
    }

    /**
     * I will solely set the GameMode of the current Sitting.
     *
     * @param \noxkiwi\spotigame\GameEntity\GameMode\AbstractGameMode $gameMode
     *
     * @return void
     */
    public function setGameMode(AbstractGameMode $gameMode): void {
        $this->gameMode = $gameMode;
    }

    private function generateMove(int $relativeStep): Move {

        $move = new Move();
        $move->Song = SongModel::getRandom($this->getPlayedSongs(), $this->gameMode);
        $move->Sitting = $this;

        $this->addSong($move->Song);
        // STORE THE CURRENT MOVE!!!
        $moveEntry = $this->moveModel->getEntry();
        $moveEntry->move_flags = 1;
        $moveEntry->sitting_id = $move->Sitting->id;
        $moveEntry->song_id = $move->Song->id;
        $moveEntry->move_step = $relativeStep;
        $moveEntry->save();
        $move->id = (int)$moveEntry->move_id;

        // Log some stuph
        $this->log->debug("Added $move->Song");

        return $move;
    }

    /**
     * @return \noxkiwi\spotigame\GameEntity\Move\Move
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @throws \noxkiwi\spotigame\Exception\GameOverException
     * @throws \noxkiwi\dataabstraction\Exception\EntryMissingException
     */
    public function getCurrentMove(): Move {
        return $this->getStep($this->getRelativeStep());
    }

    /**
     * @return \noxkiwi\spotigame\GameEntity\Player\Player[]
     * @throws \noxkiwi\database\Exception\DatabaseException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @throws \noxkiwi\dataabstraction\Exception\EntryMissingException
     */
    public function getUnfinishedPlayers(): array {
        $sql = <<<SQL
SELECT
	`sitting_player`.`player_id`
FROM
	`sitting_player`
WHERE TRUE
    AND `sitting_player`.`sitting_id` = $this->sittingId
    AND (`sitting_player`.`sitting_player_flags` & (2) = 0) 
SQL;
        $db = Database::getInstance();
        $db->read($sql);
        $ps = $db->getResult();
        $r = [];
        foreach ($ps as $p) {
            $r[] = Player::expect((int)$p['player_id']);
        }

        return $r;
    }

    public function finishRound(Player $player): void {
        $sql = <<<SQL
UPDATE
	`sitting_player`
SET
    `sitting_player`.`sitting_player_flags` = 3
WHERE TRUE
	AND `sitting_player`.`sitting_id` = $this->sittingId
	AND `sitting_player`.`player_id` = $player->id;
SQL;
        $db = Database::getInstance();
        $db->read($sql);
        if (!empty($this->getUnfinishedPlayers())) {
            return;
        }
        $this->finalize();
    }

    public function finalize(): void {
        $sql = <<<SQL
UPDATE
	`sitting`
SET
    `sitting`.`sitting_flags` = 3
WHERE TRUE
	AND `sitting`.`sitting_id` = $this->sittingId;
SQL;
        $db = Database::getInstance();
        $db->read($sql);
    }

    /**
     * @param int $moveStep
     *
     * @return \noxkiwi\spotigame\GameEntity\Move\Move
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @throws \noxkiwi\spotigame\Exception\GameOverException
     * @throws \noxkiwi\dataabstraction\Exception\EntryMissingException
     */
    private function getStep(int $moveStep): Move {
        if ($moveStep === 0) {
            $moveStep = 1;
        }
        $this->gameMode = new GameMode('random');
        $this->moveModel->addFilter('sitting_id', $this->id);
        $this->moveModel->addFilter('move_step', $moveStep);
        $aE = $this->moveModel->search();
        if (empty($aE)) {
            throw new GameOverException('Game is over, no steps left.', E_WARNING);
        }
        $aE = $aE[0];
        $song = Song::expect((int)$aE['song_id']);
        $move = new Move();
        $move->Sitting = $this;
        $move->Song = $song;
        $move->Player = $this->Player;
        $move->id = (int)$aE['move_id'];

        $move->Questions = $move->getQuestions($song, $this->gameMode);

        return $move;
    }

    /**
     * @return array
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @throws \noxkiwi\database\Exception\DatabaseException
     */
    public function getPlayedSongs(): array {
        $songIds = [];
        foreach ($this->playedSongs as $playedSong) {
            $songIds[] = $playedSong->id;
        }

        return $songIds;
    }

    public function addSong(Song $song): void {
        $this->playedSongs[] = $song;
    }
}
