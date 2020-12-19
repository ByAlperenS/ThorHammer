<?php

namespace ThorHammer\Item;

use pocketmine\network\mcpe\convert\ItemTypeDictionary;
use pocketmine\network\mcpe\protocol\types\ItemTypeEntry;
use ThorHammer\ThorHammer;
use const pocketmine\RESOURCE_PATH;

class Hammer {

    /** @var int */
    public static $id = 11111;

    /** @var string */
    public static $name = "Thor's Hammer";

    /** @var ItemTypeEntry[] */
    public static $entries = [];

    public static $simpleNetToCoreMapping = [];
    public static $simpleCoreToNetMapping = [];

    public static function init(ThorHammer $plugin){
        $file = json_decode(file_get_contents($plugin->getDataFolder() . "id.json"), true);
        $data = file_get_contents(RESOURCE_PATH . '/vanilla/r16_to_current_item_map.json');
        $json = json_decode($data, true);
        $add = $file["r16_to_current_item_map"];
        $json["simple"] = array_merge($json["simple"], $add["simple"]);

        $legacyStringToIntMapRaw = file_get_contents(RESOURCE_PATH . '/vanilla/item_id_map.json');
        $add = $file["item_id_map"];
        $legacyStringToIntMap = json_decode($legacyStringToIntMapRaw, true);
        $legacyStringToIntMap = array_merge($add, $legacyStringToIntMap);

        /** @phpstan-var array<string, int> $simpleMappings */
        $simpleMappings = [];
        foreach($json["simple"] as $oldId => $newId){
            $simpleMappings[$newId] = $legacyStringToIntMap[$oldId];
        }
        foreach($legacyStringToIntMap as $stringId => $intId){
            $simpleMappings[$stringId] = $intId;
        }

        /** @phpstan-var array<string, array{int, int}> $complexMappings */
        $complexMappings = [];
        foreach($json["complex"] as $oldId => $map){
            foreach($map as $meta => $newId){
                $complexMappings[$newId] = [$legacyStringToIntMap[$oldId], (int) $meta];
            }
        }
        $old = json_decode(file_get_contents(RESOURCE_PATH  . '/vanilla/required_item_list.json'), true);
        $add = $file["required_item_list"];
        $table = array_merge($old, $add);
        $params = [];
        foreach($table as $name => $entry){
            $params[] = new ItemTypeEntry($name, $entry["runtime_id"], $entry["component_based"]);
        }
        self::$entries = $entries = (new ItemTypeDictionary($params))->getEntries();
        foreach($entries as $entry){
            $stringId = $entry->getStringId();
            $netId = $entry->getNumericId();
            if (isset($complexMappings[$stringId])){
                //
            }elseif(isset($simpleMappings[$stringId])){
                self::$simpleCoreToNetMapping[$simpleMappings[$stringId]] = $netId;
                self::$simpleNetToCoreMapping[$netId] = $simpleMappings[$stringId];
            }
        }
    }
}