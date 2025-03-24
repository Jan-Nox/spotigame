<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Consumer;

use noxkiwi\core\Exception\InvalidArgumentException;
use noxkiwi\queue\Consumer\RabbitmqConsumer;
use noxkiwi\queue\Message;
use noxkiwi\spotigame\Message\PlaylistDeflateMessage;
use const E_USER_NOTICE;

/**
 * I am the consumer that processes Playlists.
 *
 * As soon as I get my hands on a PlaylistDeflateMessage, I will query the Spotify API for the Playlist details.
 *
 * Then I'll create a TrackImportMessage for each track in the Playlist.
 *
 * @package      noxkiwi\spotigame\Message
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2025 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class PlaylistDeflateConsumer extends RabbitmqConsumer
{
    protected const MESSAGE_TYPES = [
        PlaylistDeflateMessage::class
    ];

    /**
     * @inheritDoc
     *
     * @param \noxkiwi\lightsystem\Message\CommandMessage $message
     *
     * @throws \noxkiwi\core\Exception\InvalidArgumentException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return bool
     */
    public function process(Message $message): bool
    {
        if (! $message instanceof PlaylistDeflateMessage) {
            $messageType = $message::class;
            throw new InvalidArgumentException("The given $messageType is not compatible with this consumer.", E_USER_NOTICE);
        }

        try {
        } catch (Exception) {
            //IGNORE NOW FOR FEEDING 🍔
        }
        return true;
    }

}

