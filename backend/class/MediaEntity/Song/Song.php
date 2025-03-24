<?php declare(strict_types=1);

namespace noxkiwi\spotigame\MediaEntity\Song;

use noxkiwi\spotigame\Entity\AbstractEntity;
use noxkiwi\spotigame\MediaEntity\Album\Album;
use noxkiwi\spotigame\MediaEntity\Artist\Artist;
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
final class Song extends AbstractEntity
{

    protected const TYPE = 'song';
    public int $year;
    public Artist $artist;
    public Album $album;
    public string $title;
    public string $image;
    public int $track;
    public int $popularity;
    public int $duration;
    public string $spotifyId;
    public int $songId;

    public function save(): void
    {
        $songModel = SongModel::getInstance();
        // Fetch song if it exists by SpotifyId

        $songModel->addFilter('song_spotifyid', $this->spotifyId);
        $songModel->search();
        $foundSong = $songModel->getResult();

        if ($foundSong) {
            $Entry = SongModel::expect($foundSong[0]['song_id']);
        } else {
            $Entry = $songModel->getEntry([]);
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

    /**
     * @param int $songId
     *
     * @return static
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @throws \noxkiwi\dataabstraction\Exception\EntryMissingException
     */
    final public static function expect(int $songId): self
    {
        $entry = SongModel::expect($songId);
        $song = new self();
        $song->setName($entry->song_title);
        $song->uuid =  $entry->song_spotifyid;
        $song->spotifyId = $entry->song_spotifyid;
        $song->artist = new Artist();
        $song->artist->name = $entry->song_artist;
        $song->artist->uuid = $entry->song_spotifyid;
        $song->album = new Album();
        $song->album->name = $entry->song_album;
        $song->album->cover = $entry->song_albumcover;
        $song->album->uuid = $entry->song_spotifyid;
        $song->id = (int)$entry->song_id;
        $song->title = $entry->song_title;
        $song->year = (int)$entry->song_year;
        $song->track = (int)$entry->song_track;
        $song->popularity = (int)$entry->song_popularity;
        $song->image = (string)$entry->song_image;
        $song->duration = (int)$entry->song_duration;
        if ($song->duration === 0) {
            $song->duration = 999999;
        }

        return $song;
    }

    public function __toString(): string
    {
        return <<<XML
<song
    spotifyId="$this->spotifyId"
    artist="$this->artist"
    id="$this->id"
    album="$this->album"
    title="$this->title"
    year="$this->year"
    track="$this->track"
    popularity="$this->popularity"
    image="$this->image"
    duration="$this->duration">
</song>
XML;
    }

}
