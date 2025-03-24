<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Auth;

use Exception;
use noxkiwi\core\Environment;
use noxkiwi\core\ErrorHandler;
use noxkiwi\core\Session;
use function in_array;
use const E_USER_NOTICE;

/**
 * I am the Authenticator for Spotigame.
 *
 * @package      noxkiwi\spotigame\Auth
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
abstract class SpotigameAuth
{
    /**
     * I will solely return whether the current user is an administrator.
     *
     * @return bool
     */
    public static function isAdmin(): bool
    {
        return in_array(self::getPlayerId(), self::getAdmins(), true);
    }

    /**
     * I will return the list of administrators from the Environment.
     *
     * @example:<<< environment.json:
     *           {
     *             "admins": [
     *               "spotify:user:13",
     *               "spotify:user:42",
     *               "spotify:user:69"
     *              ]
     *           }
     * @return array
     */
    public static function getAdmins(): array
    {
        try {
            $environment = Environment::getInstance();

            return (array)$environment->get("admins", []);
        } catch (Exception $exception) {
            ErrorHandler::handleException($exception, E_USER_NOTICE);
        }

        return [];
    }

    /**
     * I will solely return the currently logged in Player's spotify_player_id.
     * @return string
     */
    public static function getPlayerId(): string
    {
        try {
            $session = Session::getInstance();

            return (string)$session->get('player_spotify_id');
        } catch (Exception $exception) {
            ErrorHandler::handleException($exception, E_USER_NOTICE);
        }

        return 'JOHN_DOE';
    }
}
