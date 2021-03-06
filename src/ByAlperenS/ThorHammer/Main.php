<?php

namespace ByAlperenS\ThorHammer;

use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\plugin\PluginBase;
use ReflectionObject;
use ByAlperenS\ThorHammer\Event\EventListener;
use ByAlperenS\ThorHammer\Item\Hammer;
use pocketmine\network\mcpe\convert\ItemTranslator;

class Main extends PluginBase{

    private static $instance;

    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->saveResource("id.json");
        ItemFactory::registerItem(new Item(Hammer::$id, 0, Hammer::$name));
        Item::addCreativeItem(Item::get(Hammer::$id));

        Hammer::init($this);

        $instance = ItemTranslator::getInstance();
        $ref = new ReflectionObject($instance);
        $r1 = $ref->getProperty("simpleCoreToNetMapping");
        $r2 = $ref->getProperty("simpleNetToCoreMapping");
        $r1->setAccessible(true);
        $r2->setAccessible(true);
        $r1->setValue($instance, Hammer::$simpleCoreToNetMapping);
        $r2->setValue($instance, Hammer::$simpleNetToCoreMapping);
    }


    public function onLoad(){
        self::$instance = $this;
    }

    public static function getInstance(): Main{
        return self::$instance;
    }
}
