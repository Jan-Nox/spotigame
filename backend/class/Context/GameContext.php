<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Context;

use noxkiwi\core\Constants\Mvc;
use noxkiwi\core\Context;
use noxkiwi\core\Environment;
use noxkiwi\core\Helper\WebHelper;
use noxkiwi\core\Response;
use noxkiwi\dataabstraction\Exception\EntryMissingException;
use noxkiwi\spotigame\Helper\OptionHelper;
use noxkiwi\spotigame\Model\SittingModel;
use noxkiwi\spotigame\Model\SongModel;
use noxkiwi\spotigame\Player\Player;
use noxkiwi\spotigame\Sitting\Sitting;
use stdClass;
use function header;

/**
 * I am the main view context.
 *
 * @package      noxkiwi\spotigame\Context
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
class GameContext extends Context
{
    private SittingModel $sittingModel;

    /**
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return void
     */
    protected function initialize(): void
    {
        parent::initialize();
        $this->sittingModel = SittingModel::getInstance();
    }

    /**
     * @inheritDoc
     */
    public function isAllowed(): bool
    {
        parent::isAllowed();
        $this->request->set(Mvc::TEMPLATE, 'spotigame');
        $this->response->set(Mvc::TEMPLATE, 'spotigame');

        return true;
    }

    /**
     * @throws \noxkiwi\core\Exception\InvalidArgumentException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return \noxkiwi\spotigame\Sitting\Sitting
     */
    protected function getSitting(): Sitting
    {
        try {
            $player = Player::identify();
            Response::getInstance()->set('player', $player);

            return $this->sittingModel->fetchSitting($player);
        } catch (EntryMissingException) {
            $e        = Environment::getInstance();
            $hostName = $e->get('server>hostname');
            header("Location: $hostName?context=sitting&action=create");
            exit(WebHelper::HTTP_TEMPORARY_REDIRECT);
        }
    }

    /**
     * @throws \noxkiwi\core\Exception\InvalidArgumentException
     * @throws \noxkiwi\dataabstraction\Exception\EntryMissingException
     * @throws \noxkiwi\database\Exception\DatabaseException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return void
     */
    public function viewAsk(): void
    {
        $sitting = $this->getSitting();
        if ($this->request->exists('next')) {
            $move = $sitting->getNextMove();
        } else {
            $move = $sitting->getCurrentMove();
        }
        $allSteps = $move->buildSetup();
        //
        Player::identify()->playSong($move->song);
        $this->response->set('stepSetup', $allSteps);
        $this->response->set('song', $move->song);
        $this->response->set('songsPlayed', implode(',', $sitting->getPlayedSongs()));
        $this->response->set('sitting', $sitting);
    }
}
