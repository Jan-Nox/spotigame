<?php declare(strict_types=1);

namespace noxkiwi\spotigame\Helper;

use noxkiwi\spotigame\Entity\AbstractEntity;

/**
 * I am the Option helper. I will help generating options and lists.
 *
 * @package      noxkiwi\spotigame\Helper
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
abstract class SpotifyRedirect
{
    public const TO_LOBBY_WELCOME = '?context=game&action=welcome';
    public const TO_GAME          = '?context=game&action=ask';
    public const TO_PLAYLIST_IMPORTER      = '?context=feed&action=playlist';
    public const TO_TRACK_IMPORTER = '?context=feed&action=track';
    public const TO_ALBUM_IMPORT= '?context=feed&action=album';
}
