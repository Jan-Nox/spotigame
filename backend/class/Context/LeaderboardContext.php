<?php declare(strict_types=1);

namespace noxkiwi\spotigame\Context;

use noxkiwi\core\Context;
use noxkiwi\database\Database;

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
final class LeaderboardContext extends Context
{
    /**
     * @inheritDoc
     */
    public function isAllowed(): bool
    {
        return true;
    }

    /**
     * @return void
     * @throws \noxkiwi\singleton\Exception\SingletonException
     */
    public function actionGetPlayers(): void
    {
        $this->request->set('template', 'json');
        $sql = <<<MYSQL
SELECT
    `player`.`player_name` AS `name`,
    `player`.`player_url` AS `url`,
    `player`.`player_avatar` AS `avatar`,
    `player`.`player_points` AS `points`
FROM
    `player`
ORDER BY `player_points` DESC;
MYSQL;
        $db = Database::getInstance();
        $db->read($sql);

        $rows = $db->getResult();
        $this->response->set('Players', $rows);
    }
}
