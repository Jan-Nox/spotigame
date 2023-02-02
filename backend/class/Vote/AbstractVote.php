<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Vote;

use noxkiwi\dataabstraction\Entry;
use noxkiwi\spotigame\Entity\AbstractEntity;
use noxkiwi\spotigame\Move\Move;
use noxkiwi\spotigame\Player\Player;
use noxkiwi\spotigame\Sitting\Sitting;

/**
 * I am an abstract Vote.
 *
 * @package      noxkiwi\spotigame\Vote
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
abstract class AbstractVote extends AbstractEntity
{
    protected const TYPE = 'vote';
    public Player  $player;
    public Sitting $sitting;
    public Move    $move;
    public int     $points;
    public int     $flags;
    public ?int    $year;
    public ?string $artist;
    public ?string $album;
    public ?string $title;
    /** @var \noxkiwi\spotigame\Answer\Answer[] */
    public array $answers;
}
