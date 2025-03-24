<?php declare(strict_types=1);

namespace noxkiwi\spotigame\Result;

use noxkiwi\spotigame\GameEntity\Player\Player;

final class Rank {
    public Player $Player;
    public int $Points;
    public int $Rank;
    public string $Time;
}