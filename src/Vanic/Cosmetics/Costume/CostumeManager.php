<?php

namespace Vanic\Cosmetics\Costume;

use pocketmine\player\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use Vanic\Cosmetics\Costume\Cosmetic\CosmeticType;
use Vanic\Cosmetics\Main;
use Vanic\Cosmetics\Utils\DatabaseManager;

class CostumeManager implements Listener {
  private $playerCostumes;

  public function __construct() {
    $this->playerCostumes = array();
  }

  public function loadCostume(PlayerJoinEvent $event): void {
    $player = $event->getPlayer();
    DatabaseManager::addPlayer($player); //Also harmless if the player already exists
    //Set the player's cosmetic to what was last saved in their data. If there's nothing, that's alright! They just will have blank slots.
    $this->playerCostumes[$player->getXuid()] = new Costume($player,
      DatabaseManager::getEquippedCosmetic($player, CosmeticType::CAPE),
      DatabaseManager::getEquippedCosmetic($player, CosmeticType::HEAD),
      DatabaseManager::getEquippedCosmetic($player, CosmeticType::UPPER_BODY),
      DatabaseManager::getEquippedCosmetic($player, CosmeticType::LOWER_BODY),
      DatabaseManager::getEquippedCosmetic($player, CosmeticType::FEET)
    );
  }

  /*
   * Until the moment they actually disconnect, their Costume manages all their equipped cosmetic info for them.
   * This saves their equipped cosmetics to their playerdata.db file once they finally disconnect so they can be loaded back
   * when they next connect to the server.
  */
  public function saveCostumeToFile(PlayerQuitEvent $event) : void {
    $player = $event->getPlayer();
    $costume = $this->playerCostumes[$player->getXuid()];
    foreach (CosmeticType::cases() as $cosmeticSlot) { //Save each cosmetic of each possible type all to the db
      DatabaseManager::savePlayerCosmetic($player, $costume->getCosmetic($cosmeticSlot), $cosmeticSlot);
    }
  }

  public function getCostume(Player $player) : Costume {
    return $this->playerCostumes[$player->getXuid()];
  }
}