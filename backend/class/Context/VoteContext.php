<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Context;

use JetBrains\PhpStorm\NoReturn;
use noxkiwi\core\Context;
use noxkiwi\core\Helper\JsonHelper;
use noxkiwi\spotigame\Model\SittingModel;
use noxkiwi\spotigame\Player\Player;
use noxkiwi\spotigame\Sitting\Sitting;
use noxkiwi\spotigame\Vote\Vote;
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
class VoteContext extends Context
{
    private SittingModel $sittingModel;

    /**
     * @inheritDoc
     * @throws \noxkiwi\core\Exception
     */
    protected function initialize(): void
    {
        header('Content-Type: application/json');
        parent::initialize();
        $this->sittingModel = SittingModel::getInstance();
    }

    /**
     * @inheritDoc
     */
    public function isAllowed(): bool
    {
        parent::isAllowed();

        return true;
    }

    /**
     * @throws \Exception
     * @return void
     */
    #[NoReturn] protected function actionVote(): void
    {
        // Fetch the sitting
        $sitting = $this->getSitting();
        // Fetch the user's Vote
        $vote = $this->buildVote();
        // Fetch the current Move
        $move = $sitting->getCurrentMove();
        // Use the Move to evaluate the Vote
        $entry        = $move->evaluate($vote, $move);
        $vote->points = (int)$entry->vote_points;
        $this->response->set('data', $entry);
        $this->response->set('points', $entry->vote_points);
        $move->end();
        die(JsonHelper::encode($vote));
    }

    /**
     * I will build the data-contract between the user interface and the OR-Model.
     * @throws \Exception
     * @return \noxkiwi\spotigame\Vote\Vote
     */
    protected function buildVote(): Vote
    {
        $vote          = new Vote();
        $vote->title   = (string)$this->request->get('title');
        $vote->artist  = (string)$this->request->get('artist');
        $vote->year    = (int)$this->request->get('year');
        $vote->album   = (string)$this->request->get('album');
        $vote->player  = Player::identify();
        $vote->sitting = $this->getSitting();
        $vote->move    = $vote->sitting->getCurrentMove();

        return $vote;
    }

    /**
     * @throws \noxkiwi\core\Exception\InvalidArgumentException
     * @throws \noxkiwi\dataabstraction\Exception\EntryMissingException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @return \noxkiwi\spotigame\Sitting\Sitting
     */
    protected function getSitting(): Sitting
    {
        return $this->sittingModel->fetchSitting(Player::identify());
    }
}
