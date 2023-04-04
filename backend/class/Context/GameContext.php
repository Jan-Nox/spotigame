<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Context;

use Exception;
use noxkiwi\core\Constants\Mvc;
use noxkiwi\core\Context;
use noxkiwi\core\Environment;
use noxkiwi\core\ErrorHandler;
use noxkiwi\core\Helper\LinkHelper;
use noxkiwi\core\Helper\WebHelper;
use noxkiwi\database\Database;
use noxkiwi\spotigame\Exception\GameOverException;
use noxkiwi\spotigame\Model\SittingModel;
use noxkiwi\spotigame\Player\Player;
use noxkiwi\spotigame\Result\PlayerRow;
use noxkiwi\spotigame\Sitting\Sitting;
use function header;

/**
 * I am the main view context.
 *
 * @package      noxkiwi\spotigame\Context
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
class GameContext extends Context
{
    private Sitting     $sitting;
    private Player      $player;
    private Environment $environment;

    /**
     * I will initialize the Context.
     * @throws \noxkiwi\singleton\Exception\SingletonException
     */
    protected function initialize(): void
    {
        parent::initialize();
        $this->environment = Environment::getInstance();
    }

    /**
     * @inheritDoc
     */
    public function isAllowed(): bool
    {
        parent::isAllowed();
        try {
            $this->request->set(Mvc::TEMPLATE, 'game');
            $this->response->set(Mvc::TEMPLATE, 'game');
            // Fetch player
            $this->player = Player::identify();
            $this->response->set('player', $this->player);
            // Fetch sitting!
            $this->sitting = $this->getSitting();
            $this->response->set('sitting', $this->sitting);
        } catch (Exception) {
            return false;
        }

        return true;
    }

    /**
     * I will return the current Player's current Sitting.
     * @return \noxkiwi\spotigame\Sitting\Sitting
     */
    private function getSitting(): Sitting
    {
        try {
            return SittingModel::getInstance()->fetchSitting($this->player);
        } catch (Exception) {
            $hostName = $this->environment->get('server>hostname', 'https://spotigame.nox.kiwi/');
            header("Location: $hostName?context=sitting&action=create");
            exit(WebHelper::HTTP_TEMPORARY_REDIRECT);
        }
    }

    /**
     * @return void
     */
    protected function viewAsk(): void
    {
        try {
            if ($this->request->exists('next')) {
                $move = $this->sitting->getNextMove();
            } else {
                $move = $this->sitting->getCurrentMove();
            }
        } catch (GameOverException) {
            $this->sitting->finishRound($this->player);
            // set sitting_player_flags => &2
            // Check if all players finished?
            LinkHelper::forward([
                                    Mvc::CONTEXT => 'game',
                                    Mvc::VIEW    => 'results',
                                    'sittingId'  => $this->sitting->id
                                ]);
        } catch (Exception $exception) {
            ErrorHandler::handleException($exception, E_USER_NOTICE);

            return;
        }
        $this->player->playSong($move->song);
        // Put stuph to Response
        $this->response->set('stepSetup', $move->buildSetup());
        $this->response->set('song', $move->song);
    }

    protected function viewResults(): void
    {
        $sql = <<<SQL
SELECT
	`player`.player_id,
	`player`.`player_avatar`,
	`player`.`player_points`,
	`player`.`player_name`,
	`sitting_player`.`sitting_player_flags`,
	SUM(`answer`.`answer_points`) AS `newPoints`
FROM
  `answer`
  JOIN `vote` USING (`vote_id`)
  JOIN `move` USING (`move_id`)
  JOIN `sitting` USING (`sitting_id`)
  JOIN `player` USING (`player_id`)
  JOIN `sitting_player` USING (`sitting_id`)
WHERE TRUE
  AND `sitting_id` = {$this->request->get('sittingId', $this->sitting->id)}
GROUP BY
	`player`.`player_id`
ORDER BY
	`newPoints` DESC
SQL;
        $db  = Database::getInstance();
        $db->read($sql);
        $rows              = $db->getResult();
        $unfinishedPlayers = $this->sitting->getUnfinishedPlayers();
        $playerRows        = [];
        $rank              = 1;
        foreach ($rows as $row) {
            $player              = new Player();
            $player->id          = (int)$row['player_id'];
            $player->playerId    = $row['player_id'];
            $player->avatar      = $row['player_avatar'];
            $player->points      = (int)$row['player_points'];
            $player->name        = $row['player_name'];
            $playerRow           = new PlayerRow();
            $playerRow->player   = $player;
            $playerRow->points   = (int)$row['newPoints'];
            $playerRow->rank     = $rank;
            $playerRow->finished = ((int)$row['sitting_player_flags'] & 2) === 2;
            $playerRows[]        = $playerRow;
            $rank++;
        }
        $this->response->set('playerRows', $playerRows);
        $this->response->set('results', $rows);
        $this->response->set('unfinishedPlayers', $unfinishedPlayers);
        $this->request->set('template', 'game');
    }
}
