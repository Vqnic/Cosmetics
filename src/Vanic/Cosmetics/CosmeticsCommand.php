<?php

namespace Vanic\Cosmetics;

use jojoe77777\FormAPI\CustomForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Vanic\Cosmetics\Costume\Cosmetic\CosmeticType;
use Vanic\Cosmetics\Utils\ConfigManager;

class CosmeticsCommand extends Command {

  private Main $plugin;

  public function __construct($plugin, $name, $description, $usage) {
    $this->plugin = $plugin;
    $this->setPermission("cosmetics.command");
    parent::__construct($name, $description, $usage);
  }

  /**
   * @inheritDoc
   */
  public function execute(CommandSender $sender, string $commandLabel, array $args) {
    if($sender instanceof Player) { //Technically, a non-player could run this command.

      $capes = $this->getOwnedCosmetics($sender, CosmeticType::CAPE);
      $heads = $this->getOwnedCosmetics($sender, CosmeticType::HEAD);
      $upperBodies = $this->getOwnedCosmetics($sender, CosmeticType::UPPER_BODY);
      $lowerBodies = $this->getOwnedCosmetics($sender, CosmeticType::LOWER_BODY);
      $feet = $this->getOwnedCosmetics($sender, CosmeticType::FEET);

      $form = new CustomForm(function (Player $player, ?array $data) use ($capes, $heads, $upperBodies, $lowerBodies, $feet) {
        if($data === null) return true;

        $costumeManager = $this->plugin->getCostumeManager();

        $capeChoice = $capes["cosmetic_id"][$data[0]]; //"Owned Capes" dropdown results
        if($capeChoice !== null) $costumeManager->getCostume($player)->setCosmetic(CosmeticType::CAPE, ConfigManager::getCosmeticByID($capeChoice));
        else $costumeManager->getCostume($player)->setCosmetic(CosmeticType::CAPE, null);

        $headChoice = $heads["cosmetic_id"][$data[1]]; //"Owned Head Cosmetics" dropdown results
        if($headChoice !== null) $costumeManager->getCostume($player)->setCosmetic(CosmeticType::HEAD, ConfigManager::getCosmeticByID($headChoice));
        else $costumeManager->getCostume($player)->setCosmetic(CosmeticType::HEAD, null);

        $upperBodyChoice = $upperBodies["cosmetic_id"][$data[2]]; //"Owned Upper Body Cosmetics" dropdown results
        if($upperBodyChoice !== null) $costumeManager->getCostume($player)->setCosmetic(CosmeticType::UPPER_BODY, ConfigManager::getCosmeticByID($upperBodyChoice));
        else $costumeManager->getCostume($player)->setCosmetic(CosmeticType::UPPER_BODY, null);

        $lowerBodyChoice = $lowerBodies["cosmetic_id"][$data[3]]; //"Owned Lower Body Cosmetics" dropdown results
        if($lowerBodyChoice !== null) $costumeManager->getCostume($player)->setCosmetic(CosmeticType::LOWER_BODY, ConfigManager::getCosmeticByID($lowerBodyChoice));
        else $costumeManager->getCostume($player)->setCosmetic(CosmeticType::LOWER_BODY, null);

        $feetChoice = $feet["cosmetic_id"][$data[4]]; //"Owned Feet Cosmetics" dropdown results
        if($feetChoice !== null) $costumeManager->getCostume($player)->setCosmetic(CosmeticType::FEET, ConfigManager::getCosmeticByID($feetChoice));
        else $costumeManager->getCostume($player)->setCosmetic(CosmeticType::FEET, null);

        return true;
      });
      $form->setTitle("Equip Cosmetics Here");

      $form->addDropdown("Owned Capes", $capes["display_name"]);
      $form->addDropdown("Owned Head Cosmetics", $heads["display_name"]);
      $form->addDropdown("Owned Upper Body Cosmetics", $upperBodies["display_name"]);
      $form->addDropdown("Owned Lower Body Cosmetics", $lowerBodies["display_name"]);
      $form->addDropdown("Owned Feet Cosmetics", $feet["display_name"]);

      $form->sendToPlayer($sender);
    }
  }

  private function getOwnedCosmetics(Player $sender, CosmeticType $type) : array {
    $ownedCosmetics = array(
      "cosmetic_id" => array(null), //always a "no thanks" option for people to remove cosmetics"
      "display_name" => array("None") //always a "no thanks" option for people to remove cosmetics"
    );
    foreach(ConfigManager::getAllCosmeticsOfType($type) as $cosmetic) {
      if($sender->hasPermission($cosmetic["required_permission"])){
        $ownedCosmetics["cosmetic_id"][] = $cosmetic["cosmetic_id"];
        $ownedCosmetics["display_name"][] = $cosmetic["display_name"]; //Add it to the array.
      }
    }
    return $ownedCosmetics;
  }
}