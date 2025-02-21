<?php

namespace Vanic\Cosmetics\Utils;

use pocketmine\player\Player;
use SQLite3;
use Vanic\Cosmetics\Costume\Cosmetic\Cosmetic;
use Vanic\Cosmetics\Costume\Cosmetic\CosmeticType;
use Vanic\Cosmetics\Main;

class DatabaseManager {
  private static Main $plugin;
  private static SQLite3 $db;
  public static function init ($plugin) {
    self::$plugin = $plugin;
    $dataFolder = $plugin->getDataFolder();
    self::$db = new SQLite3($dataFolder . "playerdata.db");
    //The names of rows in the database are identical to the names of the CosmeticTypes
    self::$db->exec('CREATE TABLE IF NOT EXISTS "playerdata" (' .
                    '"XUID" TEXT  PRIMARY KEY,' .
                    CosmeticType::CAPE->value . ' TEXT ,' .
                    CosmeticType::HEAD->value . ' TEXT ,' .
                    CosmeticType::UPPER_BODY->value . ' TEXT ,' .
                    CosmeticType::LOWER_BODY->value . ' TEXT ,' .
                    CosmeticType::FEET->value . ' TEXT )');
  }
  public static function addPlayer(Player $player) : void {
    $playerID = $player->getXuid();
    $sql = 'INSERT OR IGNORE INTO "playerdata" ("XUID") VALUES(' . $playerID .  ')';
    self::$db->exec($sql);
  }

  public static function getEquippedCosmetic(Player $player, CosmeticType $type) : ?Cosmetic {
    $playerID = $player->getXuid();
    $sql = "SELECT DISTINCT " . $type->value . " FROM playerdata WHERE XUID = " . $playerID;
    $cosmeticID = self::$db->querySingle($sql); //Could be null
    return (is_null($cosmeticID) ? null : ConfigManager::getCosmeticByID($cosmeticID)); //Recreates the cosmetic with info stored in the config, and if it doesn't even exist (or the player doesn't), return null anyway.
  }

  public static function savePlayerCosmetic(Player $player, ?Cosmetic $cosmetic, CosmeticType $type) : void {
    $sql = "UPDATE playerdata SET " . $type->value . " = '" . (is_null($cosmetic) ? null : $cosmetic->getID()) . "' WHERE XUID = " . $player->getXuid();
    self::$db->exec($sql);
  }
}