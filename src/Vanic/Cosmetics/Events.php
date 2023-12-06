<?php

namespace Vanic\Cosmetics;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use Vanic\Cosmetics\Utils\SkinUtils;

class Events implements Listener {
  
  private $playerdata;
  private Main $main;
  
  public function __construct(Main $main){
    $this->main = $main;
    $this->playerdata = $main->getPlayerDataFile();
  }
  
  public function onPlayerJoin(PlayerJoinEvent $event) : void{
    $player = $event->getPlayer();
    if(!$this->playerdata->exists($player->getName())) {
      $this->main->getPlayerDataFile()->setNested($player->getName(), ["", ""]);
      $this->playerdata->save();
    }else{
      $clothes = $this->playerdata->get($player->getName())[0];
      $cape = $this->playerdata->get($player->getName())[1];
      
      if($clothes !== ""){
        $player->setSkin(SkinUtils::layerSkin($player->getSkin(), $clothes));
      }if($cape !== ""){
        $player->setSkin(SkinUtils::getCapeSkin($player->getSkin(), $cape));
      }
    }
    $player->sendSkin();
    
  }
}