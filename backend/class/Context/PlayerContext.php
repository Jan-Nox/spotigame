<?php declare(strict_types = 1);
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
final class PlayerContext extends Context
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
    public function viewInfo(): void
    {
        $this->response->set('points', $this->getPoints());
        $this->response->set('sittings', $this->getSittings());
    }

    /**
     * @throws \noxkiwi\database\Exception\DatabaseException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return int
     */
    public function getPoints(): int
    {
        $db  = Database::getInstance();
        $sql = <<<SQL
SELECT
	SUM(`vote`.`vote_points`) AS `count`
FROM 	`vote`
WHERE TRUE
	AND `vote`.`player_id` = 2;
;
SQL;
        $db->read($sql);

        return (int)($db->getResult()[0]['count'] ?? 0);
    }

    /**
     * @throws \noxkiwi\database\Exception\DatabaseException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return array
     */
    public function getSittings(): array
    {
        $db  = Database::getInstance();
        $sql = <<<SQL
SELECT
	SUM(`vote`.`vote_points`) AS `sitting_points`,
	`sitting`.`sitting_id`,
	`vote`.`vote_flags`,
	`player`.`player_name`,
	`song`.`song_title`,
	`sitting`.`sitting_code`
FROM
	`vote`
JOIN	`player`  USING (`player_id`)
JOIN	`move`    USING (`move_id`)
JOIN	`song`    USING (`song_id`)
JOIN    `sitting` USING (`sitting_id`)
WHERE TRUE
    AND `player`.`player_id` = 2
GROUP BY 
	`sitting`.`sitting_id`
;
SQL;
        $db->read($sql);

        return $db->getResult();
    }
}
