<?php declare(strict_types=1);

namespace noxkiwi\spotigame\Context;

use DateTime;
use Exception;
use noxkiwi\cache\Cache;
use noxkiwi\core\Context;
use noxkiwi\spotigame\Helper\SpotifyRedirect;
use noxkiwi\spotigame\MediaEntity\Album\Album;
use noxkiwi\spotigame\MediaEntity\Artist\Artist;
use noxkiwi\spotigame\RemoteApi\Spotify;
use noxkiwi\spotigame\MediaEntity\Song\Song;

/**
 * I am the Context object that manages data transfer between Crud Frontend and Crud backend.
 * This overwriting is necessary to add the fitting Manipulator class.
 *
 * @package      noxkiwi\spotigame\Context
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class FeedContext extends Context
{
    private Cache $cache;

    protected function __construct()
    {
        parent::__construct();

        $this->cache = Cache::getInstance();
    }

    /**
     * @inheritDoc
     */
    public function isAllowed(): bool
    {
        return true;
    }

    private function getFromCache(string $key): mixed
    {
        $value = $this->request->get($key, null);
        if ($value !== null) {
            $this->cache->set($key, $key, $value);
            return $value;
        }

        return $this->cache->get($key, $key);
    }

    /**
     * I will fetch all tracks from the given Album.
     * Those will be feeded to the Track Import MessageQueue
     * @return void
     */
    protected function actionAlbum(): void
    {

        $albumUri = $this->getFromCache('albumUri');
        if (empty($albumUri)) {
            throw new \InvalidArgumentException('albumUri not found');
        }
        $albumOffset = (int)$this->getFromCache('albumOffset');

        $cacheKey = "{$albumUri}_{$albumOffset}";

        $spotifyResponse = $this->cache->get('actionAlbum', $cacheKey);
        if (empty($spotifyResponse)) {
            $spotify = new Spotify(SpotifyRedirect::TO_ALBUM_IMPORT);
            $spotifyResponse = $spotify->getAlbumTracks($albumUri, $albumOffset);
            $this->cache->set('actionAlbum', $cacheKey, $spotifyResponse);
        }

        var_dump($spotifyResponse);
    }

    /**
     * I will fetch all tracks from the given Playlist.
     * Those will be feeded to the Track Import MessageQueue
     *
     * @return void
     * @throws \noxkiwi\singleton\Exception\SingletonException
     *
     * https://open.spotify.com/playlist/
     * https://spotigame.nox.kiwi/?context=feed&action=playlist&playlistUri=43E6gAUpfFFOWepKcZ6LfA&playlistOffset=
     */
    public function actionPlaylist(): void
    {
        $playlistUri = $this->getFromCache('playlistUri');
        if (empty($playlistUri)) {
            throw new \InvalidArgumentException('playlistUri not found');
        }

        $playlistOffset = (int)$this->getFromCache('playlistOffset');

        $cacheKey = "{$playlistUri}_{$playlistOffset}";

        $spotifyResponse = $this->cache->get('actionPlaylist', $cacheKey);
        if (empty($spotifyResponse)) {
            $spotify = new Spotify(SpotifyRedirect::TO_PLAYLIST_IMPORTER);
            $spotifyResponse = $spotify->getTracks($playlistUri, $playlistOffset);
            $this->cache->set('actionPlaylist', $cacheKey, $spotifyResponse);
        }

        // Find songs that we already know but havent imported yet.
        $foundSongIds = $this->cache->get('foundSongs', 'foundSongs') ?? [];


        // Attach all new songs to that list.
        foreach ($spotifyResponse->items as $track) {
            $foundSongIds[] = $track->track->id;
        }
        $foundSongIds = array_unique($foundSongIds);

        $nextOffset = $playlistOffset + 100;
        $this->cache->set('foundSongs', 'foundSongs', $foundSongIds);
        // Finally force the client to the import page without the code parameter to prevent 403 from spotify API
        die(<<<HTML
<a href="https://spotigame.nox.kiwi/?context=feed&action=playlist&playlistUri=$playlistUri&playlistOffset=$nextOffset">Nächster Song</a>
https://spotigame.nox.kiwi/?context=feed&action=playlist&playlistUri=$playlistUri&playlistOffset=$nextOffset
<script>
window.location.href='https://spotigame.nox.kiwi/?context=feed&action=playlist&playlistUri=$playlistUri&playlistOffset=$nextOffset';
</script>
HTML
        );
    }


    /**
     * As long as there's no good queue system in place, we will use this to skip a song which breaks the import.
     * @return void
     */
    protected function actionSkipSong()
    {

        $trackId = $this->getFromCache('trackId') ?? '';
        // IF NO SONG WAS STARTED TO IMPORT

        // Fetch all songs
        $foundSongIds = $this->cache->get('foundSongs', 'foundSongs') ?? [];

        if (empty($trackId)) {
            // Use the first song.
            $trackId = $foundSongIds[0];

            $this->cache->set('trackId', 'trackId', $trackId);
        }
        // remove $foundSongIds at position where value equals $trackId
        if (($index = array_search($trackId, $foundSongIds)) !== false) {
            unset($foundSongIds[$index]);
        }
        $foundSongIds = array_values($foundSongIds);

        // Store the new list of $foundSongIds without the current trackId so we know that import was successful
        $this->cache->set('foundSongs', 'foundSongs', $foundSongIds);

        // Also invalidate the cache for the current trackId to make the import proceed.
        $this->cache->set('trackId', 'trackId', null);

    }

    /**
     * I will solely return the list of track IDs on the faked queue.
     * @return void
     */
    protected function actionGetSongs()
    {
        // Fetch all songs
        $foundSongIds = $this->cache->get('foundSongs', 'foundSongs') ?? [];
        $count = count($foundSongIds);

        echo "<pre>{$count} songs queued for import:";
        foreach ($foundSongIds as $index => $songId) {
            echo PHP_EOL . "{$index}   =>  {$songId}";
        }

    }

    /**
     * I am the handler that imports a specific track.
     * @return void
     */
    protected function actionTrack()
    {
        $trackId = $this->getFromCache('trackId') ?? '';
        // IF NO SONG WAS STARTED TO IMPORT

        // Fetch all songs
        $foundSongIds = $this->cache->get('foundSongs', 'foundSongs') ?? [];

        if (empty($trackId)) {
            // Use the first song.
            $trackId = $foundSongIds[0];

            $this->cache->set('trackId', 'trackId', $trackId);
        }

        try {
            // remove $foundSongIds at position where value equals $trackId
            if (($index = array_search($trackId, $foundSongIds)) !== false) {
                unset($foundSongIds[$index]);
            }

            // Fetch the track data from Spotify API
            $trackData = $this->fetchTrack($trackId);

            // Import the trackdata.
            $this->importTrack($trackData);
            $foundSongIds = array_values($foundSongIds);

            // Store the new list of $foundSongIds without the current trackId so we know that import was successful
            $this->cache->set('foundSongs', 'foundSongs', $foundSongIds);

            // Also invalidate the cache for the current trackId to make the import proceed.
            $this->cache->set('trackId', 'trackId', null);

        } catch (Exception $e) {
            echo <<<HTML
<h3>Error during import of $trackId</h3>
HTML;

            var_dump($e->getMessage());
            die();
        }
        // Finally force the client to the import page without the code parameter to prevent 403 from spotify API
        die(<<<HTML
Track #$trackId was imported successfully.
<script>
window.location.href='https://spotigame.nox.kiwi/?context=feed&action=track';
</script>
HTML
        );


    }


    // I just fetch the data.
    private function fetchTrack(string $trackId): \stdClass
    {
        // Cache it.

        $spotifyResponse = $this->cache->get('actionTrack', $trackId);
        if (empty($spotifyResponse)) {
            $spotify = new Spotify(SpotifyRedirect::TO_TRACK_IMPORTER);
            $spotifyResponse = $spotify->getTrack($trackId);
            $this->cache->set('actionTrack', $trackId, $spotifyResponse);
        }

        return $spotifyResponse;
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
