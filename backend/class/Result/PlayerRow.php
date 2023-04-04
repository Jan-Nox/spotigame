<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Result;

use noxkiwi\spotigame\Player\Player;

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
final class PlayerRow
{
    public Player $player;
    public int    $rank;
    public int    $points;
    public bool   $finished = false;
}
