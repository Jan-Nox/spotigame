<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\Answer;

use noxkiwi\spotigame\Entity\AbstractEntity;

/**
 * I am an abstract Answer Entity.
 *
 * @package      noxkiwi\spotigame\Album
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
abstract class AbstractAnswer extends AbstractEntity
{
    public const COLOUR_RIGHT   = 'correct';
    public const COLOUR_WRONG   = 'wrong';
    public const COLOUR_PARTIAL = 'partly';
    public const FLAG_CORRECT   = 2;
    public const FLAG_WRONG     = 4;
    public const FLAG_PARTIAL   = 8;
    public int    $voteId;
    public int    $questionId;
    public mixed  $input;
    public string $correct;
    public int    $points;
    public string $colour;
}
