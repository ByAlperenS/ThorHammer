<?php

namespace ThorHammer\Event;

use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\{PlaySoundPacket, AddActorPacket, StartGamePacket};
use pocketmine\level\Explosion;
use pocketmine\level\Position;
use pocketmine\Server;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\{CompoundTag, ListTag, DoubleTag, FloatTag};
use ThorHammer\Item\Hammer;
use ThorHammer\ThorHammer;

use const pocketmine\RESOURCE_PATH;

class EventListener implements Listener{

    /** @var ThorHammer */
    private $plugin;

    public function __construct(ThorHammer $plugin){
        $this->plugin = $plugin;
    }

    /**
     * @param DataPacketSendEvent $event
     */
    public function onPacket(DataPacketSendEvent $event){
        $packet = $event->getPacket();
        if ($packet instanceof StartGamePacket) {
            $old = json_decode(file_get_contents(RESOURCE_PATH . '/vanilla/item_id_map.json'), true);
            $add = json_decode(file_get_contents($this->plugin->getDataFolder()."id.json"), true);
            $packet->itemTable = array_merge($old, $add);
        }
    }

    public function interactEvent(PlayerInteractEvent $e){
        $p = $e->getPlayer();
        $action = $e->getAction();
        $item = $e->getItem();
        $block = $e->getBlock();

        if ($item->getId() == Hammer::HAMMER_ID){
            if ($action == PlayerInteractEvent::RIGHT_CLICK_BLOCK){
                $pk = new AddActorPacket();
                $pk->type = "minecraft:lightning_bolt";
                $pk->entityRuntimeId = Entity::$entityCount++;
                $pk->metadata = [];
                $pk->motion = null;
                $pk->yaw = $p->getYaw();
                $pk->pitch = $p->getPitch();
                $pk->position = new Vector3($block->getX(), $block->getY(), $block->getZ());
                Server::getInstance()->broadcastPacket($block->getLevel()->getPlayers(), $pk);
                $sound = new PlaySoundPacket();
                $sound->soundName = "ambient.weather.thunder";
                $sound->x = $block->getX();
                $sound->y = $block->getY();
                $sound->z = $block->getZ();
                $sound->volume = 1;
                $sound->pitch = 1;
                Server::getInstance()->broadcastPacket($block->getLevel()->getPlayers(), $sound);
                $radius = 4;
                $position = new Position($block->getX(), $block->getY(), $block->getZ(), $block->getLevel());
                $explosion = new Explosion($position, $radius);
                $explosion->explodeB();
            }
        }
    }
}
