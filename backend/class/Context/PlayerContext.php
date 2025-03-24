<?php declare(strict_types=1);

namespace noxkiwi\spotigame\Context;

use Exception;
use noxkiwi\core\Constants\Mvc;
use noxkiwi\database\Database;
use noxkiwi\spotigame\Auth\SpotigameAuth;
use noxkiwi\spotigame\GameEntity\Player\Player;

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
final class PlayerContext extends AuthenticatedSpotigameContext
{

    /**
     * I will solely search for the desired Player's details.
     * @return void
     */
    public function viewInfo(): void
    {
        try {

            $this->request->set(Mvc::TEMPLATE, 'game');
            $player = Player::identify();
            // If you're an admin, you may want to see a different Player's info.
            if (SpotigameAuth::isAdmin() && (int)$this->request->get('playerId', $player->playerId) !== $player->id) {
                $player = Player::expect((int)$this->request->get('playerId', $player->playerId));
            }
            $this->response->set('player', $player);
            $this->response->set('sittings', $this->getSittings($player));
        } catch (Exception) {
            // Do nothing.
        }
    }

    /**
     * I will return the list of existing sittings with the given Player inside.
     *
     * @param \noxkiwi\spotigame\Player\Player $player
     *
     * @return array
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @throws \noxkiwi\database\Exception\DatabaseException
     */
    private function getSittings(Player $player): array
    {
        $db = Database::getInstance();
        $sql = <<<SQL
SELECT
    SUM(`vote`.`vote_points`) AS `sitting_points`,
    `sitting`.`sitting_steps`,
    `sitting`.`sitting_code`,
    `sitting`.`sitting_id`,
    `sitting`.`sitting_code`,
    2 AS `sitting_players`
FROM
    `vote`
JOIN    `player`  USING (`player_id`)
JOIN    `move`    USING (`move_id`)
JOIN    `sitting` USING (`sitting_id`)
WHERE TRUE
    AND `player`.`player_id` = $player->playerId
GROUP BY
    `sitting`.`sitting_id`
ORDER BY
    `sitting`.`sitting_id` DESC
;
SQL;
        $db->read($sql);

        return $db->getResult();
    }
}
