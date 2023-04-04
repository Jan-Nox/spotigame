<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Context;

use Exception;
use InvalidArgumentException;
use noxkiwi\core\Constants\Mvc;
use noxkiwi\core\Context;
use noxkiwi\core\Environment;
use noxkiwi\dataabstraction\Exception\EntryMissingException;
use noxkiwi\database\Database;
use noxkiwi\spotigame\GameMode\GameMode;
use noxkiwi\spotigame\Model\SittingModel;
use noxkiwi\spotigame\Model\SittingPlayerModel;
use noxkiwi\spotigame\Player\Player;
use noxkiwi\spotigame\Sitting\Sitting;
use function header;
use function uniqid;
use function var_dump;

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
    private Player $player;

    /**
     * @inheritDoc
     */
    public function isAllowed(): bool
    {
        parent::isAllowed();
        try {
            $this->player = Player::identify();
        } catch (Exception) {
            return false;
        }

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
        $hostName = $e->get('server>hostname', 'https://spotigame.nox.kiwi/');
        try {
            SittingModel::getInstance()->fetchSitting($this->player);
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

    protected function viewCreate(): void
    {
        $this->request->set(Mvc::TEMPLATE, 'game');
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
        $mode    = new GameMode();
        $sitting = new Sitting();
        $mode->setName('regular');
        $sitting->stepCount = (int)$this->request->get('steps', 10);
        $sitting->setName(uniqid("SPOTIGAME_SITTING_"));
        $sitting->setGameMode($mode);
        $sitting->addPlayer($this->player);
        $sitting->create();
        // Put data into session
        $this->session->set('CURRENT_STEP', 0);
        $this->session->set('SITTING_ID', $sitting->sittingId);
        $this->session->set('STEP_COUNT', $sitting->stepCount);
        // Forward into game
        $e        = Environment::getInstance();
        $hostName = $e->get('server>hostname', 'https://spotigame.nox.kiwi/');
        header("Location: $hostName?context=game&view=ask");
    }

    protected function actionJoin(): void
    {
        $sitting = Sitting::expect((int)$this->request->get('sittingId'));
        $sitting->addPlayer($this->player);
        $newSittingPlayer = SittingPlayerModel::getInstance();
        $newSittingPlayer->save([
                                    'sitting_id'           => $sitting->id,
                                    'player_id'            => $this->player->id,
                                    'sitting_player_flags' => 1
                                ]);
        $e        = Environment::getInstance();
        $hostName = $e->get('server>hostname', 'https://spotigame.nox.kiwi/');
        // Put data to session
        $this->session->set('CURRENT_STEP', 1);
        $this->session->set('SITTING_ID', $sitting->id);
        $this->session->set('STEP_COUNT', $sitting->stepCount);
        header("Location: $hostName?context=game&view=ask");
        die('A');
    }
}
