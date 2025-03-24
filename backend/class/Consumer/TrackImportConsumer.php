<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Consumer;

use noxkiwi\core\Exception\InvalidArgumentException;
use noxkiwi\queue\Consumer\RabbitmqConsumer;
use noxkiwi\queue\Message;
use noxkiwi\spotigame\MediaEntity\Album\Album;
use noxkiwi\spotigame\MediaEntity\Artist\Artist;
use noxkiwi\spotigame\MediaEntity\Song\Song;
use noxkiwi\spotigame\Message\TrackImportMessage;
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
            $Song = new Song();
            $Song->title = $track->name;
            $Song->artist = new Artist();
            $Song->artist->name = $track->artists[0]->name;
            $Song->album = new Album();
            $Song->album->name = $track->album->name;
            $Song->album->cover = $track->album->images[0]->url;
            $Song->spotifyId = $track->id;
            $Song->name = $track->name;
            $Song->track = $track->track_number;
            $Song->popularity = $track->popularity;
            $Song->duration = (int)($track->duration_ms ?? 0);
            $Song->year = (int)(new DateTime($track->album->release_date))->format('Y');

            $Song->save();
        } catch (Exception) {
            //IGNORE NOW FOR FEEDING 🍔
        }
    }
}

