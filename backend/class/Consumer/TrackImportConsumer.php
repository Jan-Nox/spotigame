<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Consumer;

use noxkiwi\core\Exception\InvalidArgumentException;
use noxkiwi\queue\Consumer\RabbitmqConsumer;
use noxkiwi\queue\Message;
use noxkiwi\spotigame\MediaEntity\Album\Album;
use noxkiwi\spotigame\MediaEntity\Artist\Artist;
use noxkiwi\spotigame\MediaEntity\Song\Song;
use noxkiwi\spotigame\Message\TrackImportMessage;
use \Exception;
use const E_USER_NOTICE;

/**
 * I am an example Message object.
 *
 * @package      noxkiwi\spotigame\Message
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2025 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class TrackImportConsumer extends RabbitmqConsumer
{
    protected const MESSAGE_TYPES = [
        TrackImportMessage::class
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
        if (! $message instanceof TrackImportMessage) {
            $messageType = $message::class;
            throw new InvalidArgumentException("The given $messageType is not compatible with this consumer.", E_USER_NOTICE);
        }

        try {
            // Fetch the track data from Spotify API
            $trackData = $this->fetchTrack($trackId);

            // Feed the data to our own database.
            $this->importTrack($trackData);
        } catch (Exception) {
            //IGNORE NOW FOR FEEDING 🍔
        }
        return true;
    }

    // I solely run the import of the data.
    private function importTrack(\stdClass $track): void
    {
        try {
            // Update the Song.
            $song = new Song();
            $song->title = $track->name;
            $song->artist = new Artist();
            $song->artist->name = $track->artists[0]->name;
            $song->album = new Album();
            $song->album->name = $track->album->name;
            $song->album->cover = $track->album->images[0]->url;
            $song->spotifyId = $track->id;
            $song->name = $track->name;
            $song->track = $track->track_number;
            $song->popularity = $track->popularity;
            $song->duration = (int)($track->duration_ms ?? 0);
            $song->year = (int)(new DateTime($track->album->release_date))->format('Y');

            $song->save();
        } catch (Exception) {
            //IGNORE NOW FOR FEEDING 🍔
        }
    }
}

