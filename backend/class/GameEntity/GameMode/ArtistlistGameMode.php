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
class GameMode extends AbstractGameMode {
    /** @var string[] */
    private array $artists;

    public function __construct(array $artists) {
        if (empty($artists)) {
            throw new \InvalidArgumentException('You need to provide at least one artist.');
        }
        $this->artists = $artists;
    }

    public function getTrackEverywhereFilter(): string {
        $parent = parent::getTrackEverywhereFilter();
        $imploded = implode(', ', $this->artists);
        return <<<SQL
$parent

-- AND `song`.`song_created` > '2025-03-21 00:00:00'
AND `song`.`song_artist` IN ($imploded)
SQL;
    }
}
