<?php declare(strict_types=1);

namespace noxkiwi\spotigame\GameEntity\GameMode;

use noxkiwi\spotigame\Entity\AbstractEntity;

/**
 * I am a Modifier for the entire game.
 *
 *
 * @todo         Rename me to GameModifier
 *
 * @package      noxkiwi\spotigame\Entity
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
abstract class AbstractGameMode extends AbstractEntity {
    protected const TYPE = 'gameMode';
    public int $pointMultiplier = 1;
    public int $pointTitle = 1;
    public int $pointAlbum = 1;
    public int $pointArtist = 1;
    public int $pointYearExact = 2;
    public int $pointYearThreshold = 1;

    /**
     * I will return SQL that is used to filter the ANY songs that appear ANYWHERE in the game we create.
     * @return string
     */
    public function getTrackEverywhereFilter(): string {
        $sql = <<<SQL
-- Common filters for all game modes added in AbstractGameMode
AND `song`.`song_albumcover` IS NOT NULL
SQL;
        return $sql;
    }

    /**
     * @return string
     */
    public function getTrackRandomSelectionFilter(): string {
        $sql = $this->getTrackEverywhereFilter();
        return <<<SQL
$sql
SQL;
    }
}
