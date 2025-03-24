<?php declare(strict_types=1);

namespace noxkiwi\spotigame\Verification;


use noxkiwi\core\ErrorHandler;
use noxkiwi\log\Traits\LogTrait;
use noxkiwi\spotigame\GameEntity\Answer\Answer;
use noxkiwi\spotigame\GameEntity\GameMode\GameMode;
use noxkiwi\spotigame\GameEntity\Move\Move;
use noxkiwi\spotigame\GameEntity\Player\Player;
use noxkiwi\spotigame\GameEntity\Question\AbstractQuestion;
use noxkiwi\spotigame\GameEntity\Vote\Vote;
use noxkiwi\spotigame\MediaEntity\Song\Song;

/**
 * I am the spotigame App.
 *
 * @package      noxkiwi\spotigame
 * @author       Jan Nox <jan@nox.kiwi>
 * @license      https://nox.kiwi/license
 * @copyright    2024 noxkiwi
 * @version      1.0.0
 * @link         https://nox.kiwi/
 */
class Verification {
    use LogTrait;

    private array $postedStuff;

    /** @var Answer[] */
    public array $Answers;
    /** @var AbstractQuestion[] $Questions */
    public array $Questions;
    public Player $Player;
    public Vote $Vote;
    public Move $Move;


    public function __construct(Player $Player, Move $Move, array $postedStuff) {
        $this->Player = $Player;
        $this->Move = $Move;

        $this->Questions = $this->Move->getQuestions($this->Move->Song, new GameMode(''));
        $this->Vote = new Vote();
        $this->Vote->Move = $this->Move;

        $this->postedStuff = $postedStuff;
    }

    private function generate(AbstractQuestion $Question, string $correctValue, string $correctLabel, string $decision, int $maxPoints): Answer {
        $points = 0;

        // Check if the decision is correct.
        if ($correctValue === $decision) {
            $points = $maxPoints;
        }

        $Answer = new Answer();
        $Answer->Question = $Question;
        $Answer->Vote = $this->Vote;
        $Answer->answerCorrect = $correctValue;
        $Answer->answerInput = $decision;
        $Answer->points = $points;
        $Answer->result = [
            'earnedPoints' => $points,
            'colour'       => $points === $maxPoints ? 'success' : 'danger'
        ];
        $Answer->correct = [
            'uuid'  => $correctValue,
            'label' => $correctLabel
        ];
        return $Answer;
    }

    public function verify(): void {
        foreach ($this->Move->Questions as $Question) {
            $Question->decision = $this->postedStuff['Questions'][$Question->uuid]['decision'];
            switch ($Question->uuid) {
                case 'ArtistMultipleChoice': // ARTIST
                    $answer = $this->generate($Question, $this->Move->Song->spotifyId, $this->Move->Song->artist->name, $Question->decision, 5);
                    break;
                case 'AlbumMultipleChoice': // ALBUM
                    $answer = $this->generate($Question, $this->Move->Song->spotifyId, $this->Move->Song->album->name, $Question->decision, 5);
                    break;
                case 'TitleMultipleChoice': // SONG
                    $answer = $this->generate($Question, $this->Move->Song->spotifyId, $this->Move->Song->name, $Question->decision, 5);
                    break;
                default:
                    break;
            }
            $this->Answers[$Question->uuid] = $answer;
        }
    }

    public function save() {
        try {
            // Update the Vote entry with the earned points.
            $this->Vote->points = 0;
            $this->Vote->save();

            // For every Question, build an Answer.
            foreach ($this->Answers as $Answer) {

                // Save it.
                $Answer->Vote = $this->Vote;
                $Answer->save();

                // Player earns points.
                $this->Player->earn($Answer->points, $Answer);
            }

            // Let the player earn points.
        } catch (\Exception $exception) {
            ErrorHandler::handleException($exception);
        }
    }
}