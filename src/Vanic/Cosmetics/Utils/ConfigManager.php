<?php

namespace Vanic\Cosmetics\Utils;

use pocketmine\utils\Config;
use StringBackedEnum;
use Vanic\Cosmetics\Costume\Cosmetic\Cosmetic;
use Vanic\Cosmetics\Costume\Cosmetic\CosmeticType;
use Vanic\Cosmetics\Main;

class ConfigManager {
  private static Main $plugin;
  private static Config $cosmeticsConfig;
  private static string $dataFolder;
  public static function init(Main $plugin) : void {
    self::$plugin = $plugin;
    self::$dataFolder = $plugin->getDataFolder();
    self::$cosmeticsConfig = new Config(self::$dataFolder . "cosmetics.yml", Config::YAML);
  }


  public static function getCosmeticByID(string $key) : ?Cosmetic{
    if(!self::validateCosmeticByID($key)) return null; //If something is wrong with the cosmetic, pretend it doesn't exist anymore.
    $cosmeticInfo = self::$cosmeticsConfig->get($key);
    $cosmeticType = CosmeticType::tryFrom($cosmeticInfo["type"]);
    $cosmetic = new Cosmetic($key, $cosmeticType, self::$plugin->getDataFolder() .  "/textures/" . $cosmeticInfo["texture_filename"]);
    if($cosmeticType === CosmeticType::CAPE) return $cosmetic; //Capes don't need any model info since it's built-in, so goa ahead.
    $cosmetic->specifyGeometryModel(self::$plugin->getDataFolder() .  "/models/" . $cosmeticInfo["model_filename"]); //Literally anything else does.
    return $cosmetic;
  }

  /*
   * Basically makes sure the specified cosmetic is configured correctly...
   * Makes an (elegant) scene on the console so the server owner's can fix it.
   *
   * Supposed to be called a lot to spam people's console to fix their stuff.
   */
  private static function validateCosmeticByID(string $key) : bool {
    $cosmeticInfo = self::$cosmeticsConfig->get($key);
    $logger = self::$plugin->getLogger();
    if(is_array($cosmeticInfo)) { //Make sure the provided key still exists and wasn't just configured out.
      if (array_key_exists("enabled", $cosmeticInfo) && $cosmeticInfo["enabled"]) { //Make sure that the cosmetic is enabled, otherwise pretend it doesn't exist.
        $cosmeticType = $cosmeticInfo["type"];
        if (array_key_exists("type", $cosmeticInfo) && (CosmeticType::tryFrom($cosmeticInfo["type"]) instanceof CosmeticType)) { //If the type is a valid cosmetic type. valid types are CAPE, HEAD, UPPER_BODY, LOWER_BODY and FEET,
          if (array_key_exists("required_permission", $cosmeticInfo)) {
            if (array_key_exists("display_name", $cosmeticInfo)) {
              if (array_key_exists("texture_filename", $cosmeticInfo) && file_exists(self::$dataFolder . '/textures/' . $cosmeticInfo["texture_filename"])) {
                if ($cosmeticType == CosmeticType::CAPE->value) return true; //Capes have less requirements, so if they made it this far they're okay.
                elseif(array_key_exists("model_filename", $cosmeticInfo) && file_exists(self::$dataFolder . '/models/' . $cosmeticInfo["model_filename"])) {
                  return true; //Cosmetic has all the components it needs to work.
                } else $logger->warning("The cosmetic under ID " . $key . " requires a valid model_filename but none was provided! Please provide the name of the .json model in /models/.");
              } else $logger->warning("The cosmetic under ID " . $key . " requires a valid texture_filename. Please provide the name of the .png texture in /textures/.");
            } else $logger->warning("The cosmetic under ID " . $key . " is missing a display_name.");
          } else $logger->warning("The cosmetic under ID " . $key . " is missing a required_permission.");
        } else $logger->warning("The cosmetic under ID " . $key . " has an invalid type. Valid types are CAPE, HEAD, UPPER_BODY, LOWER_BODY and FEET.");
      } //Deliberately disabled, so no warning needed.
    }
    return false;
  }

  public static function getAllCosmeticsOfType(CosmeticType $type) : array {
    $cosmetics = array();
    foreach(self::$cosmeticsConfig->getAll(true) as $cosmeticKey) {
      if(!self::validateCosmeticByID($cosmeticKey)) continue; //If this cosmetic is disabled or broken, skip it.
      $cosmeticInfo = self::$cosmeticsConfig->get($cosmeticKey);
      if(array_key_exists("type", $cosmeticInfo) && $cosmeticInfo["type"] === $type->value)
        $cosmetics[] = array(
          "cosmetic_id" => $cosmeticKey,
          "display_name" => $cosmeticInfo["display_name"],
          "required_permission" => $cosmeticInfo["required_permission"]
        );
    }
    return $cosmetics;
  }
}