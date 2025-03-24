<?php declare(strict_types=1);

namespace noxkiwi\spotigame\GameEntity\Question;

use noxkiwi\core\Request;
use noxkiwi\spotigame\GameEntity\Answer;
use noxkiwi\spotigame\GameEntity\GameMode\GameMode;
use noxkiwi\spotigame\Helper\OptionHelper;
use noxkiwi\spotigame\Model\SongModel;
use noxkiwi\spotigame\MediaEntity\Song\Song;
use noxkiwi\spotigame\GameEntity\Vote\Vote;

/**
 * I am the question that lets the Player select one of multiple title.
 * Only the correct selection will grant one single point to the Player.
 *
 * @package      noxkiwi\spotigame\GameEntity\Player
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class TitleMultipleChoice extends AbstractQuestion
{
    public const    QUESTION_ID = 2;
    public const QUESTION = 'title_multiple_choice';
    public string $emoji = '🎼';

    /**
     * I am overwritten to create random options to pick from.
     *
     * @param \noxkiwi\spotigame\MediaEntity\Song\Song $song
     *
     * @throws \noxkiwi\singleton\Exception\SingletonException
     */
    public function __construct(Song $song, GameMode $gameMode)
    {
        $this->name = 'What is the title?';
        $this->emoji = '🎼';
        $this->uuid = 'What is the title?';
        $this->id = 3;
        $this->options = OptionHelper::randomPick(SongModel::getInstance()->getList('song_title'), $song, 3);
        parent::__construct($song, $gameMode);
        $this->uuid = 'TitleMultipleChoice';
    }

    /**
     * @inheritDoc
     */
    public function validate(Vote $vote, Request $request): Answer
    {
        $answer = $this->prepareAnswer($vote, $request);
        $answer->correct = $this->song->title;
        // @todo: Use the spotify URI instead of the artist name!
        if ($answer->correct === $answer->input) {
            $answer->colour = Answer::COLOUR_RIGHT;
            $answer->points++;
        }

        return $answer;
    }
}
