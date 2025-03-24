<?php declare(strict_types=1);

namespace noxkiwi\spotigame\Message;

use noxkiwi\queue\Message;

/**
 * I am a Message object that is used to query the Spotify API for an Album.
 *
 * @package      noxkiwi\spotigame\Message
 * @author       Jan Nox <jan.nox@pm.me>
 * @license      https://nox.kiwi/license
 * @copyright    2025 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 *
 * @see          \noxkiwi\spotigame\Consumer\AlbumDeflateConsumer
 */
final class AlbumDeflateMessage extends Message
{
    /** @var string I am the spotify AlbumID that shall be queried. */
    public string $albumId;
}

