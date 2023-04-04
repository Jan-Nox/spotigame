<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Result;

use noxkiwi\spotigame\Song\Song;

/**
 * I am the spotigame App.
 *
 * @package      noxkiwi\spotigame
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class SongResult
{
    public Song $song;
    public array $questions;
}
