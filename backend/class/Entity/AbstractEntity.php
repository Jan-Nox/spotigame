<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Entity;

use JetBrains\PhpStorm\Pure;
use noxkiwi\dataabstraction\Entry;
use Stringable;

/**
 * I am an arbitrary Entity
 *
 * @package      noxkiwi\spotigame\Entity
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
abstract class AbstractEntity implements Stringable
{
    protected const TYPE = 'INACCURATE';
    public string $name = 'unknown';
    public int    $id   = -1;
    public Entry  $entry;

    /**
     * I will solely set the name of the Entity.
     *
     * @param string $name
     *
     * @return void
     */
    final public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * I will solely return the name of the Entity.
     * @return string
     */
    final public function getName(): string
    {
        return $this->name;
    }

    /**
     * I will solely set the ID of the Entity.
     *
     * @param int $id
     *
     * @return void
     */
    final public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * I will solely return the ID of the Entity.
     * @return int
     */
    final public function getId(): int
    {
        return $this->id;
    }

    /**
     * For easier logging, I will create a somewhat readable format of the Entity.
     * @return string
     */
    #[Pure] public function __toString(): string
    {
        return "<" . static::TYPE . ' name="' . $this->getName() . '">';
    }
}
