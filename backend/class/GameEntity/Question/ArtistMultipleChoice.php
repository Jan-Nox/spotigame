<?php declare(strict_types = 1);
namespace noxkiwi\spotigame\GameEntity\Question;

use noxkiwi\core\Request;
use noxkiwi\spotigame\GameEntity\Answer;
use noxkiwi\spotigame\GameEntity\GameMode\GameMode;
use noxkiwi\spotigame\Helper\OptionHelper;
use noxkiwi\spotigame\Model\SongModel;
use noxkiwi\spotigame\MediaEntity\Song\Song;
use noxkiwi\spotigame\GameEntity\Vote\Vote;

/**
 * I am the question that lets the Player select one of multiple artists.
 * Only the correct selection will grant one single point to the Player.
 *
 * @package      noxkiwi\spotigame\GameEntity\Player
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2023 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
final class ArtistMultipleChoice extends AbstractQuestion
{
    public const    QUESTION_ID = 1;
    public const QUESTION = 'artist_multiple_choice';
    public string $emoji    = '🎤';

    /**
     * @inheritDoc
     */
    public function validate(Vote $vote, Request $request): Answer
    {
        $answer          = $this->prepareAnswer($vote, $request);
        $answer->correct = $this->song->artist;
        // @todo: Use the spotify URI instead of the artist name!
        if ($answer->correct === $answer->input) {
            $answer->points++;
            $answer->colour = Answer::COLOUR_RIGHT;
        }

        return $answer;
    }

    /**
     * I am overwritten to create random options to pick from.
     *
     * @param \noxkiwi\spotigame\MediaEntity\Song\Song $song
     *
     * @throws \noxkiwi\singleton\Exception\SingletonException
     */
    public function __construct(Song $song, GameMode $GameMode)
    {
        $this->name = 'Who is the artist?';
        $this->emoji = '🎤';
        $this->uuid = 'Who is the artist?';
        $this->id = 1;
        $this->options = OptionHelper::randomPick(SongModel::getInstance()->getList('song_artist', $GameMode), $song->artist, 3);
        parent::__construct($song, $GameMode);
        $this->uuid = 'ArtistMultipleChoice';
    }
}
