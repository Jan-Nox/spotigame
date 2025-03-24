<?php declare(strict_types=1);

namespace noxkiwi\spotigame\Consumer;

use noxkiwi\core\Exception\InvalidArgumentException;
use noxkiwi\queue\Consumer\RabbitmqConsumer;
use noxkiwi\queue\Message;
use noxkiwi\spotigame\Message\AlbumDeflateMessage;
use \Exception;
use const E_USER_NOTICE;

/**
 * I am the consumer that processes Albums.
 *
 * As soon as I get my hands on a AlbumDeflateMessage, I will query the Spotify API for the Album details.
 *
 * Then I'll create a TrackImportMessage for each track in the Album
 *
 * @package      noxkiwi\spotigame\Message
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2025 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class AlbumDeflateConsumer extends RabbitmqConsumer {
    protected const MESSAGE_TYPES = [
        AlbumDeflateMessage::class
    ];

    /**
     * @inheritDoc
     *
     * @param \noxkiwi\lightsystem\Message\CommandMessage $message
     *
     * @return bool
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @throws \noxkiwi\core\Exception\InvalidArgumentException
     */
    public function process(Message $message): bool {
        if (!$message instanceof AlbumDeflateMessage) {
            $messageType = $message::class;
            throw new InvalidArgumentException("The given $messageType is not compatible with this consumer.", E_USER_NOTICE);
        }

        try {
            // Do nothing for now.
        } catch (Exception) {
            //IGNORE NOW FOR FEEDING 🍔
        }

        return true;
    }

}

