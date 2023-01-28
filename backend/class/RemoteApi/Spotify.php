<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\RemoteApi;

use JetBrains\PhpStorm\NoReturn;
use noxkiwi\core\Environment;
use noxkiwi\core\Helper\WebHelper;
use noxkiwi\spotigame\Song\Song;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;
use stdClass;
use function header;

/**
 * I am just a funnel for Spotify.
 *
 * @package      noxkiwi\spotigame\Player
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class Spotify
{
    private SpotifyWebAPI $webAPI;
    private Session       $session;

    /**
     * @param string $url
     *
     * @throws \noxkiwi\singleton\Exception\SingletonException
     */
    public function __construct(string $url)
    {
        $this->webAPI = new SpotifyWebAPI();
        $this->setSession($url);
    }

    public function getTracks(string $playlistId, int $offset = 0): mixed
    {
        if (empty($_GET['code'])) {
            $this->authenticate();

            return new stdClass();
        }
        $this->session->requestAccessToken($_GET['code']);
        $this->webAPI->setAccessToken($this->session->getAccessToken());

        return $this->webAPI->getPlaylistTracks($playlistId, ['limit' => 100, 'offset' => $offset]);
    }

    /**
     * @param string $url
     *
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return void
     */
    private function setSession(string $url): void
    {
        $e             = Environment::getInstance();
        $this->session = new Session(
            $e->get('spotify>id'),
            $e->get('spotify>secret'),
            "{$e->get('server>hostname')}$url"
        );
    }

    #[NoReturn] public function authenticate(): void
    {
        $options = [
            'scope' => [
                'user-read-email',
                'user-read-playback-state',
                'user-modify-playback-state',
                'user-read-currently-playing'
            ],
        ];
        header('Location: ' . $this->session->getAuthorizeUrl($options));
        exit(WebHelper::HTTP_TEMPORARY_REDIRECT);
    }

    public function getInfo(): stdClass
    {
        if (empty($_GET['code'])) {
            $this->authenticate();

            return new stdClass();
        }
        $this->session->requestAccessToken($_GET['code']);
        $this->webAPI->setAccessToken($this->session->getAccessToken());

        return $this->webAPI->me();
    }

    public function play(Song $song): void
    {
        if (empty($_GET['code'])) {
            $this->authenticate();

            return;
        }
        $this->session->requestAccessToken($_GET['code']);
        $this->webAPI->setAccessToken($this->session->getAccessToken());
        $this->webAPI->play(false, ['uris' => ["spotify:track:$song->spotifyId"]]);
    }
}
