<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\GameMode;

use noxkiwi\spotigame\Entity\AbstractEntity;

/**
 * I am an abstract GameMode Entity.
 *
 * @package      noxkiwi\spotigame\Entity
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
abstract class AbstractGameMode extends AbstractEntity
{
    protected const TYPE = 'gameMode';
    public int $pointMultiplier    = 1;
    public int $pointTitle         = 1;
    public int $pointAlbum         = 1;
    public int $pointArtist        = 1;
    public int $pointYearExact     = 2;
    public int $pointYearThreshold = 1;
}
