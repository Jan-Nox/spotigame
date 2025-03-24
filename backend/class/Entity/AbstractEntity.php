<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Entity;

use JetBrains\PhpStorm\Pure;
use noxkiwi\dataabstraction\Entry;
use noxkiwi\log\Log;
use noxkiwi\log\Traits\LogTrait;
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
class AbstractEntity implements Stringable
{
    use LogTrait;
    protected const TYPE = 'INACCURATE';
    public string $name = 'unknown';
    public int    $id   = -1;
    public string $uuid;
    public Entry  $entry;
    protected Log $log;

    public function __construct() {
        $this->log = Log::getInstance();
        $this->uuid = self::uuidv4();
    }

    protected static function uuidv4(): string
    {
        $data = random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

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
