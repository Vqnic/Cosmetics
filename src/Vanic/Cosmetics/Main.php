<?php

namespace Vanic\Cosmetics;


use pocketmine\plugin\PluginBase;
use Vanic\Cosmetics\Costume\CostumeManager;
use Vanic\Cosmetics\Utils\ConfigManager;
use Vanic\Cosmetics\Utils\DatabaseManager;
use Vanic\Cosmetics\Utils\SkinUtils;

class Main extends PluginBase {
  private ConfigManager $configManager;
  private CostumeManager $costumeManager;

  public function onEnable(): void {
    $this->saveResourcesToDataFolder();
    SkinUtils::init($this);
    ConfigManager::init($this);
    DatabaseManager::init($this);
    $this->costumeManager = new CostumeManager($this);
    $this->getServer()->getPluginManager()->registerEvents($this->costumeManager, $this);
    $this->getServer()->getCommandMap()->register($this->getName(), new CosmeticsCommand($this, "cosmetics", "Equip capes, hats, wings and more on your skin!", "/cosmetics"));
  }

  public function getCostumeManager() : CostumeManager {
    return $this->costumeManager;
  }

  private function saveResourcesToDataFolder() : void {
    //This is where server owners add or remove cosmetics.
    $this->saveResource("cosmetics.yml");
    //These serve as placeholders for invalid skins.
    $this->saveResource("/required/default_player_model.json");
    $this->saveResource("/required/default_player_64x_texture.json");

    $modelsPath = $this->getDataFolder() . "/models/";
    if(!file_exists($modelsPath)) {
      @mkdir($modelsPath, 0777, true);
      foreach(scandir($this->getResourceFolder() . "/models/") as $file) {
        $this->saveResource("/models/" . $file);
      }
    }

    $texturesPath = $this->getDataFolder() . "/textures/";
    if(!file_exists($texturesPath)) {
      @mkdir($texturesPath, 0777, true);
      foreach(scandir($this->getResourceFolder() . "/textures/") as $file) {
        $this->saveResource("/textures/" . $file);
      }
    }
  }
}