<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Context;

use DateTime;
use Exception;
use noxkiwi\cache\Cache;
use noxkiwi\core\Context;
use noxkiwi\spotigame\Auth\SpotigameAuth;
use noxkiwi\spotigame\Model\SongModel;
use noxkiwi\spotigame\RemoteApi\Spotify;
use noxkiwi\spotigame\Song\Song;

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
    /**
     * @inheritDoc
     */
    public function isAllowed(): bool
    {
        if (! parent::isAllowed()) {
            return false;
        }

        return SpotigameAuth::isAdmin();
    }

    /**
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return void
     */
    public function actionPlaylist(): void
    {
        $cache = Cache::getInstance();
        if ($this->request->exists('offset')) {
            $cache->set('offset', 'offset', max((int)$this->request->get('offset', 0), 0));
        }
        if ($this->request->exists('playlistUri')) {
            $cache->set('playlistUri', 'playlistUri', (string)$this->request->get('playlistUri'));
        }
        $playlistUri = (string)$cache->get('playlistUri', 'playlistUri');
        $offset      = (int)$cache->get('offset', 'offset');
        // FETCH DATA
        $spotify = new Spotify('?context=feed&action=playlist');
        //
        $tracks = $spotify->getTracks($playlistUri, $offset);
        foreach ($tracks->items as $track) {
            echo '<pre>';
            $song             = new Song();
            $song->title      = $track->track->name;
            $song->artist     = $track->track->artists[0]->name;
            $song->spotifyId  = $track->track->id;
            $song->album      = $track->track->album->name;
            $song->name       = $track->track->name;
            $song->track      = $track->track->track_number;
            $song->popularity = $track->track->popularity;
            $song->duration   = (int)($track->track->duration_ms ?? 0);
            $song->image      = '';
            try {
                $song->year = (int)(new DateTime($track->track->album->release_date))->format('Y');
                SongModel::store($song);
                echo chr(10) . "$song has been imported.";
            } catch (Exception) {
                //IGNORE NOW FOR FEEDING 🍔
            }
        }
    }
}
