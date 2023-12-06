<?php

namespace Vanic\Cosmetics;

use pocketmine\entity\animation\Animation;
use pocketmine\network\mcpe\protocol\AnimateEntityPacket;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\network\mcpe\protocol\EmotePacket;
use pocketmine\network\mcpe\protocol\types\skin\SkinAnimation;
use pocketmine\player\Player;
use pocketmine\scheduler\CancelTaskException;
use pocketmine\scheduler\Task;

class FlapTask extends Task {
  private Main $main;
  
  /**
   * @param Main $param
   */
  public function __construct(Main $main) {
    $this->main = $main;
  }
  
  public function onRun(): void {
    $server = $this->main->getServer();
    foreach ($server->getWorldManager()->getWorlds() as $level) {
      foreach($level->getEntities() as $player) {
        if ($player instanceof Player) {
          $packet = new AnimateEntityPacket();
          $packet = $packet->create("animation.player.flapwings_1", "", "", 0, "", 0, [$player->getId()]);
          $player->getNetworkSession()->sendDataPacket($packet);
        }
      }
    }
  }
}