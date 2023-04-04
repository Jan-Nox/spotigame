<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Context;

use Exception;
use noxkiwi\core\Context;
use noxkiwi\core\Helper\LinkHelper;
use noxkiwi\core\Session;
use noxkiwi\database\Database;
use noxkiwi\spotigame\Auth\SpotigameAuth;
use noxkiwi\spotigame\Manipulator;
use noxkiwi\spotigame\Player\Player;
use function header;

/**
 * I am the Context object that manages data transfer between Crud Frontend and Crud backend.
 * This overwriting is necessary to add the fitting Manipulator class.
 *
 * @package      noxkiwi\spotigame\Context
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class SettingsContext extends Context
{
    private Player $player;
    private const SETTING_SHOW_ON_LEADERBOARD = 'leaderBoard';

    /**
     * @inheritDoc
     */
    public function isAllowed(): bool
    {
        try {
            $this->player = Player::identify();

            return true;
        } catch (Exception) {
            return false;
        }
    }

    protected function viewShow(): void
    {
        $this->request->set('template', 'game');
    }

    protected function actionRemoveAccount(): void
    {
        $db = Database::getInstance();
        $db->read("DELETE answer FROM answer INNER JOIN vote USING (vote_id) WHERE vote.player_id =  {$this->player->id};");
        $db->read("DELETE FROM vote WHERE player_id =  {$this->player->id};");
        $db->read("DELETE FROM sitting_player WHERE player_id =  {$this->player->id};");
        $db->read("DELETE FROM player WHERE player.player_id = {$this->player->id};");
        $se = Session::getInstance();
        $se->destroy();
        LinkHelper::forward('https://www.spotify.com/de/account/apps/');
    }

    protected function actionSet(): void
    {
        $this->player->settings[self::SETTING_SHOW_ON_LEADERBOARD] = true;
    }
}
