<?php declare(strict_types=1);

namespace noxkiwi\spotigame\Context;

use noxkiwi\spotigame\Result\Album;
use noxkiwi\spotigame\Result\Artist;
use noxkiwi\spotigame\Result\FullResult;
use noxkiwi\spotigame\Result\Genre;
use noxkiwi\spotigame\Result\Move;
use noxkiwi\spotigame\Result\Question;
use noxkiwi\spotigame\Result\Song;
use noxkiwi\spotigame\Result\User;
use noxkiwi\spotigame\GameEntity\Sitting\Sitting;

/**
 * I am the Resource Context
 *
 * @package      noxkiwi\spotigame\Context
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class ResultsContext extends AuthenticatedSpotigameContext
{
    protected function actionStats(): void
    {
        $sittingId = (int)$this->request->get('sittingId', -1);

        if ($sittingId <= 0) {
            throw new \InvalidArgumentException('sittingId is invalid');
        }

        $sittingEntry = Sitting::expect($sittingId);

        echo json_encode(new FullResult($sittingEntry), JSON_PRETTY_PRINT);
        exit(0);
    }

}
