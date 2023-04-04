<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Interfaces;

use noxkiwi\spotigame\Move\AbstractMove;
use noxkiwi\spotigame\Player\Player;
use noxkiwi\spotigame\Sitting\Sitting;

/**
 * I am the interface for all Sittings.
 *
 * @package      noxkiwi\spotigame\Entity
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
interface SittingInterface
{
    /**
     * I will generate and return the next move.
     * @return \noxkiwi\spotigame\Move\AbstractMove
     */
    public function getNextMove(): AbstractMove;

    /**
     * I will solely add the given $player to the Sitting.
     *
     * @param \noxkiwi\spotigame\Player\Player $player
     *
     * @return void
     */
    public function addPlayer(Player $player): void;

    /**
     * I will solely add the given $song to the Sitting.
     *
     * @param \noxkiwi\spotigame\Sitting\Sitting $song
     *
     * @return void
     */
    public function addSong(Sitting $song): void;
}
