<?php declare(strict_types=1);

namespace noxkiwi\spotigame\GameEntity\Player;

use Exception;
use noxkiwi\core\Exception\AuthenticationException;
use noxkiwi\core\Session;
use noxkiwi\dataabstraction\Entry;
use noxkiwi\spotigame\Entity\AbstractEntity;
use noxkiwi\spotigame\Helper\SpotifyRedirect;
use noxkiwi\spotigame\Interfaces\PlayerInterface;
use noxkiwi\spotigame\MediaEntity\Song\Song;
use noxkiwi\spotigame\Model\PlayerModel;
use noxkiwi\spotigame\RemoteApi\Spotify;
use stdClass;

/**
 * I am a real Player.
 *
 * @package      noxkiwi\spotigame\GameEntity\Player
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class Player extends AbstractEntity implements PlayerInterface {
    protected const TYPE = 'player';
    public string $avatar;
    public int $points;
    public array $settings;

    /**
     * I will make the player's Spotify app play the given $song.
     *
     * @param \noxkiwi\spotigame\MediaEntity\Song\Song $song
     */
    public function playSong(Song $song): void {
        $this->logWarning("Playing $song for $this");
        $api = new Spotify(SpotifyRedirect::TO_GAME);
        $api->play($song);
    }

    public function earn(int $points, AbstractEntity $reason): void {
        $player = PlayerModel::expect($this->id);
        $player->player_points += $points;
        $player->save();

        $this->logWarning("$this earned $points points for $reason.");
    }

    /**
     * @return string
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @throws \noxkiwi\core\Exception\AuthenticationException
     */
    private static function getUri(): string {
        $session = Session::getInstance();
        $playerSpotifyId = $session->get('player_spotify_id');
        if (empty($playerSpotifyId)) {
            throw new AuthenticationException("Not logged in!", 42);
        }

        return $playerSpotifyId;
    }

    private static function fetchPlayer(): stdClass {
        return (new Spotify(SpotifyRedirect::TO_LOBBY_WELCOME))->getInfo();
    }

    /**
     * @param string $uri
     *
     * @return array
     * @throws \noxkiwi\singleton\Exception\SingletonException
     */
    private static function findPlayer(string $uri): array {
        $playerBackend = PlayerModel::getInstance();
        $playerBackend->addFilter('player_spotify_id', $uri);
        $playerRow = $playerBackend->search();

        return $playerRow[0] ?? [];
    }

    /**
     * @param \stdClass $player
     *
     * @return \noxkiwi\dataabstraction\Entry
     * @throws \Exception
     */
    private static function createPlayer(stdClass $player): Entry {
        try {
            // Check if the user already exists on the DB.
            $foundPlayer = self::findPlayer($player->uri);
            if (!empty(($foundPlayer))) {
                return PlayerModel::expect($foundPlayer['player_id']);
            }
            // If not found yet, create a new player entry.
            $entry = PlayerModel::getInstance()->getEntry();
            $entry->player_flags = 1;
            $entry->player_spotify_id = $player->uri;
            $entry->player_name = $player->display_name;
            $entry->email = $player->email;
            $entry->player_url = $player->external_urls->spotify;
            $entry->player_avatar = $player->images[0]->url ?? 'none';
            $entry->player_email = $player->email;
            $entry->player_points = 1;
            $entry->save();

            return $entry;
        } catch (Exception) {
            throw new Exception("Player already exists.");
        }
    }

    /**
     * @return \noxkiwi\spotigame\GameEntity\Player\Player
     * @throws \noxkiwi\dataabstraction\Exception\EntryMissingException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @throws \noxkiwi\core\Exception\InvalidArgumentException
     */
    public static function identify(): Player {
        try {
            $playerSpotifyId = self::getUri();
            $foundPlayer = self::findPlayer($playerSpotifyId);
            if (empty($foundPlayer)) {
                $playerData = self::fetchPlayer();
                $player = self::createPlayer($playerData);
            }
            return self::expect((int)$foundPlayer['player_id']);
        } catch (AuthenticationException) {
            // Not logged in yet, fetch from spotify!
            $playerData = self::fetchPlayer();
            $playerSpotifyId = $playerData->uri;
            $session = Session::getInstance();
            $session->set('player_spotify_id', $playerSpotifyId);
        }
        $player = self::createPlayer($playerData);

        return self::expect((int)$player->player_id);
    }

    /**
     * @param int $playerId
     *
     * @return static
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @throws \noxkiwi\dataabstraction\Exception\EntryMissingException
     */
    public static function expect(int $playerId): self {
        $player = new self();
        $entry = PlayerModel::expect($playerId);
        $player->setName($entry->player_name);
        $player->setId($playerId);
        $player->points = (int)$entry->player_points;
        $player->avatar = $entry->player_avatar;
        $player->settings = (array)$entry->player_settings;

        return $player;
    }
}
