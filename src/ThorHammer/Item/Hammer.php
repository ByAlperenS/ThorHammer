<?php

namespace ThorHammer\Item;

use pocketmine\item\Tool;

class Hammer extends Tool {

    /** @var int */
    public const HAMMER_ID = 11111;

    /** @var string */
    public const HAMMER_NAME = "Thor's Hammer";

    public function getMaxStackSize() : int{
        return 1;
    }

    public function getMaxDurability(): int{
        return 3;
    }
}