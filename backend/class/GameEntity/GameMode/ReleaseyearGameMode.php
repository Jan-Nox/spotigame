<?php declare(strict_types=1);

namespace noxkiwi\spotigame\GameEntity\GameMode;

/**
 * I am a real GameMode Entity.
 *
 * @package      noxkiwi\spotigame\Entity
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
class ReleaseyearGameMode extends AbstractGameMode {
    private int $yearFrom;
    private int $yearTo;

    public function __construct(int $yearFrom, int $yearTo) {
        if (count($yearFrom) < 1) {
            throw new \InvalidArgumentException('You need to provide at least one artist.');
        }
        if (count($yearTo) < 1) {
            throw new \InvalidArgumentException('You need to provide at least one artist.');
        }
        if ($yearFrom > $yearTo) {
            throw new \InvalidArgumentException('Year from must be smaller than year to.');
        }
        $this->yearFrom = $yearFrom;
        $this->yearTo = $yearTo;
    }

    public function getTrackEverywhereFilter(): string {
        $parent = parent::getTrackEverywhereFilter();
        $imploded = implode(', ', $this->artists);
        return <<<SQL
$parent

AND `song`.`song_year` BETWEEN $this->yearFrom AND $this->yearTo
SQL;
    }
}
