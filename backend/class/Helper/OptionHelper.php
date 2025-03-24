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
abstract class OptionHelper
{
    /**
     * I will randomly pick $count-1 elements from $context the $correct one before shuffling the result.
     *
     * @param array $context I am the list of possible values to select from.
     * @param string $correct I am the correct value that will always be added to the list.
     * @param int $count I am the count of entries to return from the given $list.
     *
     * @return array
     */
    public static function randomPick(array $context, AbstractEntity $correct, int $count): array
    {
        $picks[] = [
            'label' => $correct->name,
            'value' => $correct->uuid
        ];
        while (count($picks) <= $count - 1) {
            $randomPickKey = array_rand($context);
            if (array_key_exists($randomPickKey, $picks)) {
                continue;
            }
            $picks[$randomPickKey] = [
                'label' => $context[$randomPickKey],
                'value' => $randomPickKey
            ];
        }
        $picks = self::shuffle($picks);

        return array_values($picks);
    }

    public static function shuffle(array $list): array
    {
        if (!is_array($list)) {
            return $list;
        }

        $keys = array_keys($list);
        shuffle($keys);
        $random = [];
        foreach ($keys as $key) {
            $random[$key] = $list[$key];
        }
        return $random;
    }
}
