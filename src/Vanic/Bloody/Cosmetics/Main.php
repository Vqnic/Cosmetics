<?php

namespace Vanic\Bloody\Cosmetics;

use pocketmine\utils\Config;
use pocketmine\plugin\PluginBase;
use pocketmine\permission\Permission;
use Vanic\Bloody\Cosmetics\Utils\SkinUtils;
use pocketmine\permission\DefaultPermissions;
use Vanic\Bloody\Cosmetics\Command\CapesCommand;
use Vanic\Bloody\Cosmetics\Command\ClothesCommand;

class Main extends PluginBase {
  
  private Config $playerdatafile;
  private array $clothes;
  private array $capes;
  private Config $messages;
  
  public function onEnable() : void {
    parent::onEnable();
    
    $this->saveResource('clothes.yml');
    $this->saveResource('capes.yml');
    $this->saveResource('messages.yml');
  
  
    $this->saveResource("fallenangel/" . 'fallenangel.json');
    $this->saveResource("fallenangel/" . 'fallenangel.png');
    $this->saveResource("phoenix/" . 'phoenix.json');
    $this->saveResource("phoenix/" . 'phoenix.png');
    $this->saveResource("capes/" . 'coin.png');
    $this->saveResource("capes/" . 'infernal.png');
    $this->saveResource("capes/" . 'creeper.png');
    $this->saveResource("capes/" . 'zombie.png');
    $this->saveResource("capes/" . 'moyai.png');
    $this->saveResource("capes/" . 'emoji_angry.png');
    $this->saveResource("capes/" . 'emoji_cool.png');
    $this->saveResource("capes/" . 'emoji_nerd.png');
    $this->saveResource("capes/" . 'emoji_sad.png');
    $this->saveResource("capes/" . 'emoji_skull.png');
    $this->saveResource("capes/" . 'emoji_smirk.png');
    $this->saveResource("capes/" . 'emoji_weary.png');
  
    $clothesconfig = new Config($this->getDataFolder() . "clothes.yml", Config::YAML);
    $capesconfig = new Config($this->getDataFolder() . "capes.yml", Config::YAML);
    
    $this->playerdatafile = new Config($this->getDataFolder() . "playerdata.yml", Config::YAML);
    $this->messages = new Config($this->getDataFolder() . "messages.yml", Config::YAML);
    $this->getServer()->getPluginManager()->registerEvents(new Events($this), $this);
    
    SkinUtils::init($this);
    
    $loaded = 0;
    
    foreach($clothesconfig->getAll() as $clothes) {
      $name = $clothes['name'];
      if (file_exists($this->getDataFolder() . "$name/" . $name . ".png") && file_exists($this->getDataFolder() . "$name/" . $name . ".json")) {
        if($clothes['enabled']) {
          $this->getLogger()->info("Loaded cosmetic '" . $name . "'!");
          $permission = $clothes['permission'];
          DefaultPermissions::registerPermission(new Permission($permission, "Cosmetic unlock."));
          $this->clothes[] = array(
            "name" => $clothes['name'],
            "display-name" => $clothes['display-name'],
            "permission" => $permission
          );
          $loaded++;
        } else {
          $this->getLogger()->warning("Yikes! There aren't any valid clothes to load!");
        }
      }
    }
    $this->getLogger()->info("Loaded a total of $loaded clothes!");
    
    $loaded = 0;
  
    foreach($capesconfig->getAll() as $capes) {
      $name = $capes['name'];
      if (file_exists($this->getDataFolder() . "capes/" . $name . ".png")) {
        if($capes['enabled']) {
          $this->getLogger()->info("Loaded cape '" . $name . "'!");
          $permission = $capes['permission'];
          DefaultPermissions::registerPermission(new Permission($permission));
          $this->capes[] = array(
            "name" => $capes['name'],
            "display-name" => $capes['display-name'],
            "permission" => $permission
          );
          $loaded++;
        }
      } else {
        $this->getLogger()->warning("Yikes! There aren't any valid capes to load!");
      }
    }
    $this->getLogger()->info("Loaded a total of $loaded capes!");
    
    $this->saveResource('playerdata.yml');
  
    $this->getServer()->getCommandMap()->register(CapesCommand::class, new CapesCommand("capes", $this, "Equip your cosmetics!", ""));
    $this->getServer()->getCommandMap()->register(ClothesCommand::class, new ClothesCommand("clothes", $this, "Equip your cosmetics!", ""));
  }
  
  public function getPlayerDataFile(): Config {
    return $this->playerdatafile;
  }
  
  public function getMessagesConfig(): Config {
    return $this->messages;
  }
  
  public function getCapes(): array {
    return $this->capes;
  }
  
  public function getClothes(): array {
    return $this->clothes;
  }
}