<?php declare(strict_types=1);

namespace noxkiwi\spotigame\Interfaces;

use noxkiwi\spotigame\DataContract\LobbyDataContract;
use noxkiwi\spotigame\GameEntity\Move\Move;
use noxkiwi\spotigame\GameEntity\Player\Player;

/**
 * I define the methods on every Sitting.
 *
 * @package      noxkiwi\spotigame\Entity
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
interface SittingInterface {
    /**
     * @return self
     */
    public function create(LobbyDataContract $Lobby): self;

    /**
     * I will generate and return the next move.
     * @return \noxkiwi\spotigame\GameEntity\Move\Move
     */
    public function getNextMove(): Move;

    /**
     * I will add the given $player to the sitting.
     *
     * After storing that, I will set the session up to begin playing the sitting.
     *
     * @param \noxkiwi\spotigame\GameEntity\Player\Player $player
     * @return void
     */
    public function join(Player $player): void;
}
