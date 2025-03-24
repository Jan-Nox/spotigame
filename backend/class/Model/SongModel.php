<?php declare(strict_types=1);

namespace noxkiwi\spotigame\Model;

use Exception;
use noxkiwi\cache\Cache;
use noxkiwi\dataabstraction\Entry;
use noxkiwi\dataabstraction\Model;
use noxkiwi\database\Database;
use noxkiwi\spotigame\GameEntity\GameMode\GameMode;
use noxkiwi\spotigame\MediaEntity\Song\AbstractSong;
use noxkiwi\spotigame\MediaEntity\Song\Song;
use function implode;
use function in_array;

/**
 * I am the storage for all Songs.
 *
 * @package      noxkiwi\spotigame\Model
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class SongModel extends Model {
    public const TABLE = 'song';

    /**
     * If the given $song is not yet known to our meta database, we will store it.
     *
     * Otherwise we will just skip everything.
     *
     * @param \noxkiwi\spotigame\MediaEntity\Song\Song $song
     *
     * @return void
     * @throws \noxkiwi\dataabstraction\Exception\EntryMissingException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @throws \noxkiwi\core\Exception\InvalidArgumentException
     */
    public static function store(Song $song): void {
        $instance = new self();
        $entry = self::fetchEntry($song);
        $instance->saveEntry($entry);
    }

    /**
     * From all the Songs we know in our own meta database, I will return ONE Random song instance.
     *
     * @param AbstractSong[] $excludedSongs
     *
     * @return \noxkiwi\spotigame\MediaEntity\Song\Song
     * @throws \noxkiwi\database\Exception\DatabaseException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @throws \noxkiwi\dataabstraction\Exception\EntryMissingException
     */
    public static function getRandomSongs(array $excludedSongs, GameMode $GameMode, int $count = 1, ?AbstractSong $correct = null): Song {
        $limit = $count;

        if ($correct) {
            $limit = $count -1;
        }

        $exclude = '';
        if ($excludedSongs) {
            $exclude = 'WHERE  `song`.`song_id` NOT IN (' . implode(',', $excludedSongs ?? [0]) . ')';
        }


        $sql = <<<SQL
SELECT `song_id`
FROM   `song`
    $exclude
   AND `song`.`song_id` != $songId
    
{$GameMode->getTrackEverywhereFilter()}

ORDER BY RAND() LIMIT {$limit}

UNION ALL

SELECT `song_id`
FROM   `song`
WHERE  `song`.`song_id` = $specificSongId
    
{$GameMode->getTrackEverywhereFilter()}
SQL;
        $db = Database::getInstance();
        $db->read($sql);
        $rows = $db->getResult();

        $result = [];
        foreach ($rows as $row) {
            $result [] = Song::expect((int)$row[0]['song_id']);
        }

        return $result;
    }

    /**
     * From all the Songs we know in our own meta database, I will return ONE Random song instance.
     *
     * @param array|null $excludedSongs
     *
     * @return \noxkiwi\spotigame\MediaEntity\Song\Song
     * @throws \noxkiwi\database\Exception\DatabaseException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @throws \noxkiwi\dataabstraction\Exception\EntryMissingException
     */
    public static function getRandom(array $excludedSongs, GameMode $GameMode, int $count = 1): Song {
        $exclude = '';
        if ($excludedSongs) {
            $exclude = ' AND `song`.`song_id` NOT IN (' . implode(',', $excludedSongs ?? [0]) . ')';
        }
        $sql = <<<SQL
SELECT `song_id`
FROM   `song`
WHERE TRUE  $exclude
    
{$GameMode->getTrackEverywhereFilter()}

-- Yes use a random song
ORDER BY RAND() LIMIT 1
SQL;
        $db = Database::getInstance();
        $db->read($sql);
        $row = $db->getResult();

        return Song::expect((int)$row[0]['song_id']);
    }

    /**
     * I will try to load the list behind the given $fieldName from meta cache.
     *
     * @param string $fieldName
     *
     * @return array
     */
    public function fromCache(string $fieldName): array {
        try {
            return (array)Cache::getInstance()->get('SPOTIGAME_META', $fieldName);
        } catch (Exception) {
            return [];
        }
    }

    /**
     * I will put the given $list into the meta cache.
     *
     * @param string $fieldName
     * @param array $list
     *
     * @return void
     */
    public function toCache(string $fieldName, array $list): void {
        try {
            Cache::getInstance()->set('SPOTIGAME_META', $fieldName, $list);
        } catch (Exception) {
        }
    }

    /**
     * I will return the list of possible values for the given $fieldName.
     *
     * This will be stored in cache.
     *
     * @param string $fieldName
     *
     * @return array
     * @see \noxkiwi\spotigame\Model\SongModel::toCache()
     * @see \noxkiwi\spotigame\Model\SongModel::fromCache()
     *
     */
    public function getList(string $fieldName): array {
        $list = $this->fromCache($fieldName);
        if (!empty($list)) {
            return $list;
        }
        $songs = $this->search();
        $list = [];
        foreach ($songs as $song) {
            if (!in_array($song[$fieldName], $list, true)) {
                $list[$song['song_spotifyid']] = $song[$fieldName];
            }
        }
        $this->toCache($fieldName, $list);

        return $list;
    }

    /**
     * I will utilize the given $song's SpotifyID to verify whether we already possess meta on the given $song or not.
     *
     * @param \noxkiwi\spotigame\MediaEntity\Song\Song $song
     *
     * @return int
     */
    public static function getId(Song $song): int {
        $instance = new self();
        $instance->addFilter('song_spotifyid', $song->spotifyId);

        return (int)($instance->search()[0]['song_id'] ?? 0);
    }

    /**
     * From the given $song I will create an UNSAVED Entry object that MAY be saved.
     *
     * @param \noxkiwi\spotigame\MediaEntity\Song\Song $song
     *
     * @return \noxkiwi\dataabstraction\Entry
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @throws \noxkiwi\dataabstraction\Exception\EntryMissingException
     */
    private static function fetchEntry(Song $song): Entry {
        $songId = self::getId($song);
        if (!empty($songId)) {
            $entry = self::expect($songId);
        } else {
            $instance = new self();
            $entry = $instance->getEntry();
        }
        $entry->song_title = $song->title;
        $entry->song_spotifyid = $song->spotifyId;
        $entry->song_artist = $song->artist;
        $entry->song_album = $song->album;
        $entry->song_year = $song->year;
        $entry->song_track = $song->track;
        $entry->song_popularity = $song->popularity;
        $entry->song_image = $song->image;
        $entry->song_duration = $song->duration;
        $entry->category_id = 1;

        return $entry;
    }
}
