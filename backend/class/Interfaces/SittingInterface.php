<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Interfaces;

use noxkiwi\spotigame\Move\AbstractMove;

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
    public function getNextMove(): AbstractMove;
}
