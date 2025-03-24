<?php declare(strict_types=1);

namespace noxkiwi\spotigame\Context;

use noxkiwi\core\Constants\Mvc;
use noxkiwi\core\Context;
use \Exception;
use noxkiwi\core\Environment;
use noxkiwi\spotigame\GameEntity\Player\Player;
use noxkiwi\spotigame\GameEntity\Sitting\Sitting;

/**
 * I am the base Context for all those that lay behind the authentication.
 *
 * @package      noxkiwi\spotigame\Context
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2025 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
abstract class AuthenticatedSpotigameContext extends Context
{
    /** @var Player I am the authenticated Player that is active. */
    protected Player $player;

    /** @var Sitting I am the Sitting the Player is currently playing. */
    protected Sitting $sitting;


    protected Environment $environment;


    /**
     * I will initialize the Context.
     * @throws \noxkiwi\singleton\Exception\SingletonException
     */
    protected function initialize(): void
    {
        parent::initialize();
        $this->request->set('template', 'json');
        $this->environment = Environment::getInstance();
    }

    public function isAllowed(): bool
    {
        parent::isAllowed();
        try {
            $this->request->set(Mvc::TEMPLATE, 'game');
            $this->response->set(Mvc::TEMPLATE, 'game');
            // Fetch player
            $this->player = Player::identify();
            $this->response->set('player', $this->player);
        } catch (Exception) {
            return false;
        }

        return true;
    }
}
