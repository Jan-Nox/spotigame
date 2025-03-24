<?php declare(strict_types=1);

namespace noxkiwi\spotigame\Context;

use noxkiwi\core\Context;
use noxkiwi\database\Database;

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
final class PrometheusContext extends Context {
    private Database $database;

    protected function __construct() {
        parent::__construct();
        $this->database = Database::getInstance();
    }

    /**
     * Additionally to verifying the parent Context, I will also fetch...
     * - the current Player
     * - the current Sitting
     * @inheritDoc
     */
    public function isAllowed(): bool {
        return true;
    }

    protected function actionPrometheus(): void {
        $this->request->set('template', 'txt');

        $this->sittingDetails();
        $this->songDetails();
        $this->playerDetails();

        exit(200);
    }

    private function sittingDetails() {


        // ALL POINTS
        $this->database->read('SELECT SUM(`player`.`player_points`) AS `points` FROM `player`;');
        $rows = $this->database->getResult();

        // SITTINGS IN LAST 24 hours
        $this->database->read('SELECT COUNT(true) AS `sittings` FROM sitting WHERE sitting.sitting_created BETWEEN NOW() - INTERVAL 1 DAY AND NOW(); ');
        $rowsb = $this->database->getResult();

        // ANSWERS IN LAST 24 hours
        $this->database->read('SELECT COUNT(true) AS `answers` FROM answer WHERE answer.answer_created BETWEEN NOW() - INTERVAL 1 DAY AND NOW(); ');
        $rowsc = $this->database->getResult();

        // NEW PLAYERS IN LAST 24 hours
        $this->database->read('SELECT COUNT(true) AS `sittings` FROM sitting WHERE sitting.sitting_flags = 0 AND sitting.sitting_created BETWEEN NOW() - INTERVAL 1 DAY AND NOW(); ');
        $rowse = $this->database->getResult();

        echo <<<TXT
# Gauge, Currently playing Lobbies
{$rowse[0]['sittings']}
# Gauge, Points earned in the past 24 hours.
{$rows[0]['points']}p
# Gauge, Sittings in the past 24 hours.
{$rowsb[0]['sittings']}s
# Gauge, Answers in the past 24 hours.
{$rowsc[0]['answers']}a
TXT;
    }

    private function songDetails() {
        // ALL SONGS
        $this->database->read('SELECT COUNT(true) AS `songs` FROM song;');
        $rowsf = $this->database->getResult();
        echo <<<TXT

# Gauge, Total Songs in the database.
{$rowsf[0]['songs']}
TXT;

    }

    private function playerDetails(): void {

        // NEW PLAYERS IN LAST 24 hours
        $this->database->read('SELECT COUNT(true) AS `players` FROM player WHERE player.player_created BETWEEN NOW() - INTERVAL 1 DAY AND NOW(); ');
        $rowsd = $this->database->getResult();

        echo <<<TXT

# Gauge, New Players in the past 24 hours.
{$rowsd[0]['players']}
TXT;

    }
}
