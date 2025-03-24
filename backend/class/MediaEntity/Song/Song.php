<?php declare(strict_types=1);

namespace noxkiwi\spotigame\MediaEntity\Song;

use noxkiwi\spotigame\Entity\AbstractEntity;
use noxkiwi\spotigame\Model\SongModel;

/**
 * I am a real Song.
 *
 * @package      noxkiwi\spotigame\MediaEntity\Song
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class Song extends AbstractEntity {
    
    public function save(): void {
        $SongModel = SongModel::getInstance();
        // Fetch song if it exists by SpotifyId

        $SongModel->addFilter('song_spotifyid', $this->spotifyId);
        $SongModel->search();
        $foundSong = $SongModel->getResult();

        if ($foundSong) {
            $Entry = SongModel::expect($foundSong[0]['song_id']);
        } else {
            $Entry = $SongModel->getEntry([]);
        }

        $Entry->song_title = $this->title;
        $Entry->song_artist = $this->artist->name;
        $Entry->song_albumcover = $this->album->cover;
        $Entry->song_album = $this->album->name;
        $Entry->song_year = $this->year;
        $Entry->song_spotifyid = $this->spotifyId;
        $Entry->song_duration = $this->duration;
        $Entry->category_id = 1;

        $Entry->save();

    }

}
