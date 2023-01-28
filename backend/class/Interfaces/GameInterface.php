<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Interfaces;

/**
 * I am the interface for all Game Instances.
 *
 * @package      noxkiwi\spotigame\Entity
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
interface GameInterface
{
    public function addPlayer(PlayerInterface $player): void;

    public function start();

    public function end();
}
