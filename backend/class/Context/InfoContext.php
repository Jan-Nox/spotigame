<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Context;

use noxkiwi\core\Context;
use noxkiwi\core\Context\ResourceContext as BaseResourceContext;
use noxkiwi\database\Database;
use noxkiwi\spotigame\Player\Player;

/**
 * I am the Resource Context
 *
 * @package      noxkiwi\spotigame\Context
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class InfoContext extends Context
{
    /**
     * @inheritDoc
     */
    public function isAllowed(): bool
    {
        return true;
    }

    public function viewLeaderboard(): void
    {
        $sql = <<<SQL
SELECT
	`player`.`player_id`
FROM `player`
WHERE TRUE
	AND	`player`.`player_settings` ->"$.showOnLeaderboard" = TRUE
ORDER BY
    `player`.`player_points` DESC
SQL;
        $r   = Database::getInstance();
        $r->read($sql);
        $d = $r->getResult();
        $l = [];
        foreach ($d as $i) {
            $l[] = Player::expect((int)$i['player_id']);
        }
        $this->response->set('leaderboard', $l);
        $this->request->set('template', 'game');
    }
}
