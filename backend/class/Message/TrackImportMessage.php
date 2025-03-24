<?php declare(strict_types=1);

namespace noxkiwi\spotigame\Message;

use noxkiwi\queue\Message;

/**
 * I am a Message object that is used to query the Spotify API for track details.
 * 
 * @package      noxkiwi\spotigame\Message
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2025 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 *
 * @see          \noxkiwi\spotigame\Consumer\TrackImportConsumer
 */
final class TrackImportMessage extends Message
{
    /** @var string I am the spotify TrackId that shall be queried. */
    public string $trackId;
}

