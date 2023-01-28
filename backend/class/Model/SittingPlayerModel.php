<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Model;

use noxkiwi\dataabstraction\Model;
use noxkiwi\spotigame\Player\Player;
use noxkiwi\spotigame\Sitting\Sitting;

/**
 * I am the storage for all Sitting<->Player relations.
 *
 * @package      noxkiwi\spotigame\Model
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class SittingPlayerModel extends Model
{
    public const TABLE = 'sitting_player';

    /**
     * @param \noxkiwi\spotigame\Sitting\Sitting $sitting
     *
     * @throws \noxkiwi\dataabstraction\Exception\EntryMissingException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return \noxkiwi\spotigame\Player\Player[]
     */
    public static function getPlayers(Sitting $sitting): array
    {
        $playerSittingModel = self::getInstance();
        $playerSittingModel->addFilter('sitting_id', $sitting->getId());
        $sittingPlayers = $playerSittingModel->search();
        $players        = [];
        foreach ($sittingPlayers as $sittingPlayer) {
            $players[] = Player::expect($sittingPlayer['player_id']);
        }

        return $players;
    }
}
