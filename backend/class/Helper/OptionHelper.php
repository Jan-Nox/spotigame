<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Helper;

use function array_rand;
use function shuffle;

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
abstract class OptionHelper
{
    /**
     * I will randomly pick $count-1 elements from $context the $correct one before shuffling the result.
     *
     * @param array  $context I am the list of possible values to select from.
     * @param string $correct I am the correct value that will always be added to the list.
     * @param int    $count   I am the count of entries to return from the given $list.
     *
     * @return array
     */
    public static function randomPick(array $context, string $correct, int $count): array
    {
        $result = [];
        for ($row = 1; $row <= $count - 1; $row++) {
            $key    = array_rand($context);
            $artist = $context[$key];
            if ($artist === $correct) {
                continue;
            }
            $result[] = $artist;
        }
        $result[] = $correct;
        shuffle($result);

        return $result;
    }
}
