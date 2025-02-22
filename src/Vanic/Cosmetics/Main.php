<?php

namespace Vanic\Cosmetics;


use pocketmine\plugin\PluginBase;
use Vanic\Cosmetics\Costume\CostumeManager;
use Vanic\Cosmetics\Utils\ConfigManager;
use Vanic\Cosmetics\Utils\DatabaseManager;
use Vanic\Cosmetics\Utils\SkinUtils;

class Main extends PluginBase {
  private CostumeManager $costumeManager;

  public function onEnable(): void {
    $this->saveResourcesToDataFolder();
    SkinUtils::init($this);
    ConfigManager::init($this);
    DatabaseManager::init($this);
    $this->costumeManager = new CostumeManager();
    $this->getServer()->getPluginManager()->registerEvents($this->costumeManager, $this);
    $this->getServer()->getCommandMap()->register($this->getName(), new CosmeticsCommand($this, "cosmetics", "Equip capes, hats, wings and more on your skin!", "/cosmetics"));
  }

  public function getCostumeManager() : CostumeManager {
    return $this->costumeManager;
  }

  private function saveResourcesToDataFolder() : void {
    //This is where server owners add or remove cosmetics.
    $this->saveResource("cosmetics.yml");
    $folderNames = ["/models/", "/required/", "/textures/"]; //Important folder names to populate with default files if needed.
    foreach($folderNames as $folderName) {
      $requiredPath = $this->getDataFolder() . $folderName;
      if(!file_exists($requiredPath)) {
        @mkdir($requiredPath, 0777, true);
        foreach(scandir($this->getResourceFolder() . $folderName) as $file) {
          $this->saveResource($folderName . $file);
        }
      }
    }
  }
}