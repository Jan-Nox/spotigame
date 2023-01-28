<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Song;

use noxkiwi\spotigame\Entity\AbstractEntity;
use noxkiwi\spotigame\Model\SongModel;

/**
 * I am an abstract Song.
 *
 * @package      noxkiwi\spotigame\Song
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
abstract class AbstractSong extends AbstractEntity
{
    protected const TYPE = 'song';
    public int    $year;
    public string $artist;
    public string $album;
    public string $title;
    public string $image;
    public int    $track;
    public int    $popularity;
    public int    $duration;
    public string $spotifyId;
    public int    $songId;

    /**
     * @param int $songId
     *
     * @throws \noxkiwi\dataabstraction\Exception\EntryMissingException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return static
     */
    final public static function expect(int $songId): static
    {
        $entry = SongModel::expect($songId);
        $song  = new static();
        $song->setName($entry->song_title);
        $song->spotifyId  = $entry->song_spotifyid;
        $song->artist     = $entry->song_artist;
        $song->id         = (int)$entry->song_id;
        $song->album      = $entry->song_album;
        $song->title      = $entry->song_title;
        $song->year       = (int)$entry->song_year;
        $song->track      = (int)$entry->song_track;
        $song->popularity = (int)$entry->song_popularity;
        $song->image      = (string)$entry->song_image;
        $song->duration   = (int)$entry->song_duration;
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
