<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Sitting;

use noxkiwi\spotigame\Entity\AbstractEntity;
use noxkiwi\spotigame\Interfaces\SittingInterface;

/**
 * I am an abstract Sitting.
 *
 * @package      noxkiwi\spotigame\Sitting
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
abstract class AbstractSitting extends AbstractEntity implements SittingInterface
{
    protected const TYPE = 'sitting';
}
