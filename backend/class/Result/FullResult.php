<?php declare(strict_types=1);

namespace noxkiwi\spotigame\Result;

use noxkiwi\database\Database;
use noxkiwi\spotigame\GameEntity\GameMode\GameMode;
use noxkiwi\spotigame\GameEntity\Player\Player;
use noxkiwi\spotigame\GameEntity\Move\Move;
use noxkiwi\spotigame\Model\MoveModel;
use noxkiwi\spotigame\GameEntity\Sitting\Sitting;

/**
 * I am the spotigame App.
 *
 * @package      noxkiwi\spotigame
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class FullResult
{
    private Database $Database;


    public GameMode $GameMode;
    public Sitting $Sitting;

    /** @var Rank[] */
    public array $Ranks = [];

    /** @var Player[] */
    public array $Players = [];

    /** @var Move[] */
    public array $Moves = [];

    public array $Pointsmap = [];

    public function __construct(Sitting $sitting)
    {
        $this->Database = Database::getInstance();
        $this->Sitting = $sitting;
        $this->GameMode = new GameMode();

        // First load the participating users.
        $this->fillPlayers();

        // Then load the moves.
        $this->fillMoves();

        // Then load results
        $this->loadResults();
    }

    private function fillPlayers()
    {
        $this->Database->read(<<<SQL
SELECT player_id AS `playerId` FROM sitting_player WHERE sitting_id = {$this->Sitting->id};
SQL
        );
        $rows = $this->Database->getResult();
        foreach ($rows as $row) {
            $this->addPlayer(Player::expect($row['playerId']));
        }
    }

    private function fillMoves()
    {
        $this->Database->read(<<<SQL
SELECT move_id AS `moveId` FROM move WHERE sitting_id = {$this->Sitting->id} ORDER BY move_step ASC;
SQL
        );
        $rows = $this->Database->getResult();
        foreach ($rows as $row) {
            $this->Moves[] = $this->buildMove($row['moveId']);
        }
    }

    private function buildMove(int $moveId): Move
    {
        // BUILD EVERY MOVE
        return Move::expect($moveId, $this->GameMode);
    }

    protected function addPlayer(Player $player): void
    {
        $rank = $this->buildRank($player);
        $this->Ranks[$player->id] = $rank;
        $this->Players[] = $player;
    }

    private function buildRank(Player $player): Rank
    {
        $rank = new Rank();
        $rank->Player = $player;
        $rank->Points = 0;
        $rank->Rank = 1;
        $rank->Time = "04:32:000";
        return $rank;
    }

    private function loadResults(): void
    {
        $sql = <<<SQL
SELECT
    *
FROM
    `answer`
JOIN vote USING (vote_id)
JOIN move USING (move_id)
WHERE TRUE
    AND move.sitting_id = {$this->Sitting->id}

ORDER BY
    move_id DESC;
SQL;

        $this->Database->read($sql);
        $rows = $this->Database->getResult();
        $this->Pointsmap = [];
        foreach ($rows as $row) {
            $this->Ranks[$row['player_id']]->Points += $row['answer_points'];
            $this->Pointsmap[$row['move_id']][$row['question_id']][$row['player_id']] = [
                'earnedPoints' => $row['answer_points'],
                'colour' => $row['answer_points'] === 0 ? 'warning' : 'success',
            ];
        }
    }
}

