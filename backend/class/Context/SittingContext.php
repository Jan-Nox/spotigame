<?php declare(strict_types=1);

namespace noxkiwi\spotigame\Context;

use InvalidArgumentException;
use noxkiwi\spotigame\DataContract\LobbyDataContract;
use noxkiwi\spotigame\GameEntity\Sitting\Sitting;
use noxkiwi\spotigame\Result\Album;
use noxkiwi\spotigame\Result\Artist;
use noxkiwi\spotigame\Result\Genre;
use noxkiwi\spotigame\Result\Move;
use noxkiwi\spotigame\Result\Question;
use noxkiwi\spotigame\Result\Song;
use noxkiwi\spotigame\Result\User;

/**
 * I am the Context that processes Sitting actions.
 * Mainly I will hande opening and joining a Sitting.
 *
 * @package      noxkiwi\spotigame\Context
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2025 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class SittingContext extends AuthenticatedSpotigameContext
{

    /**
     * @return void
     * @throws \noxkiwi\dataabstraction\Exception\EntryMissingException
     * @throws \noxkiwi\database\Exception\DatabaseException
     * @throws \noxkiwi\singleton\Exception\SingletonException
     * @throws \noxkiwi\core\Exception\InvalidArgumentException
     */
    protected function actionCreate(): void
    {
        $this->logInfo("SittingContext::actionCreate called by " . $this->player);

        $this->request->set('template', 'json');
        // Create Lobby post
        $request = $this->request->get('Lobby', []);
        $lobby = LobbyDataContract::fromArray($request);

        // Create Sitting
        $sitting = new Sitting();
        $sitting->create($lobby);
        $lobby->code = $sitting->getName();

        // Join the player
        $sitting->join($this->player);

        // Add data to response for the front-end.
        $this->response->set('Lobby', $lobby);
    }

    protected function actionJoin(): void
    {
        $sittingCode = $this->request->get('sittingCode', '');
        if (!$sittingCode) {
            throw new InvalidArgumentException("sittingCode is required");
        }

        // Fetch the sitting from the given $sittingCode.
        $sitting = Sitting::expectFromCode($sittingCode);

        // Then join it.
        $sitting->join($this->player);

        exit(0);
    }
}
