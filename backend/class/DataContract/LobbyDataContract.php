<?php declare(strict_types=1);

namespace noxkiwi\spotigame\DataContract;

/**
 * I am the DataContract for the Lobby.
 *
 * POSTed from front-end to create a new Lobby.
 * Response from back-end contains the Lobby>code to invite friends.
 *
 * @package      noxkiwi\spotigame\DataContract\Player
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2025 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class LobbyDataContract {
    public int $songs = 10;
    public int $timeout = 30;
    public string $code = '';

    public static function fromArray(array $data): self {
        $lobby = new self();
        $lobby->songs = min(max(3, (int)($data['songs'] ?? 3)), 100);
        $lobby->timeout = min(max((int)($data['timeout'] ?? 10), 10), 300);

        return $lobby;
    }
}
