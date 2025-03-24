<?php declare(strict_types=1);

namespace noxkiwi\spotigame\Interfaces;


use noxkiwi\spotigame\Entity\AbstractEntity;
use noxkiwi\spotigame\MediaEntity\Song\Song;

/**
 * I am the interface for all Sittings.
 *
 * @package      noxkiwi\spotigame\Interfaces
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2025 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
interface PlayerInterface {
    /**
     * I will make the Player earn points.
     * Also, I will log that including the given $reason which is an AbstractEntity.
     *
     * @param int $points
     * @param AbstractEntity $reason
     * @return void
     */
    public function earn(int $points, AbstractEntity $reason): void;

    /**
     * I will play the given Song for the Player.
     * @param Song $song
     * @return void
     */
    public function playSong(Song $song): void;
}
