<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Player;

use noxkiwi\spotigame\Entity\AbstractEntity;
use noxkiwi\spotigame\Interfaces\PlayerInterface;

/**
 * I am an abstract Player.
 *
 * @package      noxkiwi\spotigame\Player
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
abstract class AbstractPlayer extends AbstractEntity implements PlayerInterface
{
    protected const TYPE = 'player';
    public string $playerId;
    public string $avatar;
    public int    $points;
    public array  $settings;
}
