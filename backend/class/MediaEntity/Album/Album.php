<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\MediaEntity\Album;

use noxkiwi\spotigame\Entity\AbstractEntity;

/**
 * I am a real Album Entity.
 *
 * @package      noxkiwi\spotigame\MediaEntity\Album
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class Album extends AbstractEntity
{
    public string $cover = '';
}
