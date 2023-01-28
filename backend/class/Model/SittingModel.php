<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Model;

use noxkiwi\core\Exception;
use noxkiwi\core\Exception\AuthenticationException;
use noxkiwi\core\Session;
use noxkiwi\dataabstraction\Model;
use noxkiwi\spotigame\Player\Player;
use noxkiwi\spotigame\Sitting\Sitting;
use function uniqid;

/**
 * I am the storage for all Sittings.
 *
 * @package      noxkiwi\spotigame\Model
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class SittingModel extends Model
{
    public const TABLE = 'sitting';

    /**
     * @param \noxkiwi\spotigame\Player\Player $player
     *
     * @throws \noxkiwi\core\Exception\AuthenticationException
     * @throws \noxkiwi\dataabstraction\Exception\EntryMissingException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return \noxkiwi\spotigame\Sitting\Sitting
     */
    private function getOpenSitting(Player $player): Sitting
    {
        $session   = Session::getInstance();
        $sittingId = (int)$session->get("sittingId$player->id", -1);
        if ($sittingId <= 0) {
            throw new AuthenticationException("No open sitting for player $player->id", 42);
        }
        $entry         = self::expect($sittingId);
        $sitting       = new Sitting();
        $sitting->id   = (int)$entry->sitting_id;
        $sitting->name = $entry->sitting_code;

        return $sitting;
    }

    /**
     * I will solely create a new Sitting for the given $player.
     *
     * @param \noxkiwi\spotigame\Player\Player $player
     *
     * @throws \noxkiwi\core\Exception\InvalidArgumentException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return \noxkiwi\spotigame\Sitting\Sitting
     */
    private function createSitting(Player $player): Sitting
    {
        $entry               = $this->getEntry();
        $entry->sitting_code = uniqid('sitting_');
        $entry->save();
        $sitting       = new Sitting();
        $sitting->id   = (int)$entry->sitting_id;
        $sitting->name = $entry->sitting_code;
        $session       = Session::getInstance();
        $session->set("sittingId$player->id", $sitting->id);

        return $sitting;
    }

    /**
     * @param \noxkiwi\spotigame\Player\Player $player
     *
     * @throws \noxkiwi\core\Exception\InvalidArgumentException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return \noxkiwi\spotigame\Sitting\Sitting
     */
    public function fetchSitting(Player $player): Sitting
    {
        try {
            return $this->getOpenSitting($player);
        } catch (Exception) {
            return $this->createSitting($player);
        }
    }
}
