<?php

namespace ThorHammer;

use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\plugin\PluginBase;
use ThorHammer\Event\EventListener;
use ThorHammer\Item\Hammer;

class ThorHammer extends PluginBase{

    private static $instance;

    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getLogger()->info("Plugin Enable - ByAlperenS");
        $this->getLogger()->info("Github: https://github.com/ByAlperenS/ThorHammer");
        $this->saveResource("id.json");
        ItemFactory::registerItem(new Item(Hammer::HAMMER_ID, 0, Hammer::HAMMER_NAME));
        Item::addCreativeItem(Item::get(Hammer::HAMMER_ID));
    }


    public function onLoad(){
        self::$instance = $this;
    }

    public static function getInstance(): ThorHammer{
        return self::$instance;
    }
}
