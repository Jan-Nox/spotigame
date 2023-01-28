<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Context;

use InvalidArgumentException;
use noxkiwi\core\Context;
use noxkiwi\core\Environment;
use noxkiwi\dataabstraction\Exception\EntryMissingException;
use noxkiwi\database\Database;
use noxkiwi\spotigame\GameMode\GameMode;
use noxkiwi\spotigame\Model\SittingModel;
use noxkiwi\spotigame\Player\Player;
use noxkiwi\spotigame\Sitting\Sitting;
use function header;
use function uniqid;

/**
 * I am the Context object that manages data transfer between Crud Frontend and Crud backend.
 * This overwriting is necessary to add the fitting Manipulator class.
 *
 * @package      noxkiwi\spotigame\Context
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class SittingContext extends Context
{
    /**
     * @inheritDoc
     */
    public function isAllowed(): bool
    {
        parent::isAllowed();

        return true;
    }

    /**
     * @throws \noxkiwi\database\Exception\DatabaseException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return void
     */
    protected function actionGetMoves(): void
    {
        $sId = (int)$this->request->get('sittingId', -1);
        if ($sId <= 0) {
            throw new InvalidArgumentException("SittingId $sId is invalid!");
        }
        $this->queryResponse(
            <<<SQL
SELECT
	`vote`.`vote_points`,
	`vote`.`vote_flags`,
	`player`.`player_name`,
	`song`.`song_title`,
	`song`.`song_artist`,
	`song`.`song_album`,
	`song`.`song_year`,
	`sitting`.`sitting_code`
FROM
	`vote`
JOIN	`player`  USING (`player_id`)
JOIN	`move`    USING (`move_id`)
JOIN	`song`    USING (`song_id`)
JOIN    `sitting` USING (`sitting_id`)
WHERE TRUE
    AND `sitting`.`sitting_id` = $sId
;
SQL,
            'players'
        );
    }

    /**
     * @throws \noxkiwi\database\Exception\DatabaseException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return void
     */
    protected function actionGetPlayers(): void
    {
        $sId = (int)$this->request->get('sittingId', -1);
        if ($sId <= 0) {
            throw new InvalidArgumentException("SittingId $sId is invalid!");
        }
        $this->queryResponse(
            <<<SQL
SELECT
	`player`.`player_id`,
	`player`.`player_name`
FROM	`sitting`
JOIN 	`sitting_player` USING (`sitting_id`)
JOIN	`player`  USING (`player_id`)
WHERE TRUE
    AND `sitting`.`sitting_id` = $sId
;
SQL,
            'players'
        );
    }

    /**
     * @throws \noxkiwi\core\Exception\InvalidArgumentException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return void
     */
    protected function viewDashboard(): void
    {
        $e        = Environment::getInstance();
        $hostName = $e->get('server>hostname');
        try {
            $player = Player::identify();
            SittingModel::getInstance()->fetchSitting($player);
        } catch (EntryMissingException) {
            header("Location: $hostName?context=sitting&action=create");

            return;
        }
        // Player is logged in and has a (new) sitting.
        header("Location: $hostName?context=game&view=ask");
    }

    /**
     * @param string $query
     * @param string $key
     *
     * @throws \noxkiwi\database\Exception\DatabaseException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return void
     */
    private function queryResponse(string $query, string $key): void
    {
        $db = Database::getInstance();
        $db->read($query);
        $this->response->set($key, $db->getResult());
    }

    /**
     * @throws \noxkiwi\core\Exception\InvalidArgumentException
     * @throws \noxkiwi\dataabstraction\Exception\EntryMissingException
     * @throws \noxkiwi\database\Exception\DatabaseException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return void
     */
    protected function actionCreate(): void
    {
        $player  = Player::identify();
        $mode    = new GameMode();
        $sitting = new Sitting();
        $mode->setName('regular');
        $sitting->setName(uniqid("SPOTIGAME_SITTING_"));
        $sitting->setGameMode($mode);
        $sitting->addPlayer($player);
        $sitting->init();
        $sitting->getNextMove();
        $this->session->set("sittingId$player->id", $sitting->id);
        $this->session->remove('sittingId15');
        $this->session->remove('sitting_id15');
        $e        = Environment::getInstance();
        $hostName = $e->get('server>hostname');
        header("Location: $hostName?context=game&view=ask");
    }
}
