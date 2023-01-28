<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Sitting;

use JetBrains\PhpStorm\Pure;
use noxkiwi\core\Session;
use noxkiwi\database\Database;
use noxkiwi\spotigame\GameMode\AbstractGameMode;
use noxkiwi\spotigame\Model\MoveModel;
use noxkiwi\spotigame\Model\SittingModel;
use noxkiwi\spotigame\Model\SittingPlayerModel;
use noxkiwi\spotigame\Model\SongModel;
use noxkiwi\spotigame\Move\AbstractMove;
use noxkiwi\spotigame\Move\Move;
use noxkiwi\spotigame\Interfaces\PlayerInterface;

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
    public int $sittingId;
    /** @var \noxkiwi\spotigame\Player\Player[] */
    private array           $players;
    public AbstractGameMode $gameMode;

    /**
     * @throws \noxkiwi\core\Exception\InvalidArgumentException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return void
     */
    public function init(): void
    {
        $sittingModel                = SittingModel::getInstance();
        $sittingEntry                = $sittingModel->getEntry();
        $sittingEntry->sitting_code  = $this->getName();
        $sittingEntry->sitting_flags = 0;
        $sittingModel->saveEntry($sittingEntry);
        $this->setId((int)$sittingEntry->sitting_id);
        //
        $spModel = SittingPlayerModel::getInstance();
        foreach ($this->players as $player) {
            $spEntry                       = $spModel->getEntry();
            $spEntry->sitting_id           = $sittingEntry->sitting_id;
            $spEntry->player_id            = $player->getId();
            $spEntry->sitting_player_flags = 0;
            $spModel->saveEntry($spEntry);
        }
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
        $move          = new Move();
        $song          = SongModel::getRandom($this->getPlayedSongs());
        $move->sitting = $this;
        $move->song    = $song;
        // STORE THE CURRENT MOVE!!!
        $moMo             = MoveModel::getInstance();
        $moEn             = $moMo->getEntry();
        $moEn->move_flags = 1;
        $moEn->sitting_id = $move->sitting->id;
        $moEn->song_id    = $song->id;
        $moEn->save();
        $move->id = (int)$moEn->move_id;
        $session  = Session::getInstance();
        $session->set('currentMove', $move);

        return $move;
    }

    /**
     * I will identify the user's Request to the code and try to determine what Move he is currently playing.
     * @throws \noxkiwi\core\Exception\InvalidArgumentException
     * @throws \noxkiwi\dataabstraction\Exception\EntryMissingException
     * @throws \noxkiwi\database\Exception\DatabaseException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return \noxkiwi\spotigame\Move\AbstractMove
     */
    public function getCurrentMove(): AbstractMove
    {
        $ses = Session::getInstance();
        if (! $ses->exists('currentMove')) {
            return $this->getNextMove();
        }

        return $ses->get('currentMove');
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
}
