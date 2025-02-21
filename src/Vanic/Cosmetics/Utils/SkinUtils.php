<?php

namespace Vanic\Cosmetics\Utils;

use GdImage;
use pocketmine\entity\Skin;
use Vanic\Cosmetics\Costume\Cosmetic\CosmeticType;
use Vanic\Cosmetics\Costume\Costume;
use pocketmine\utils\BinaryStream;
use Vanic\Cosmetics\Main;

class SkinUtils {

  //This class still feels like fine italian cuisine, but it is what it is.
  public static Main $plugin;

  public static function init(Main $plugin){
    self::$plugin = $plugin;
  }

  /**
   * @throws \JsonException
   */
  public static function getMergedSkin(Costume $costume) : Skin {
    $originalSkin = $costume->getWearer()->getSkin();
    $capeData = ""; //Will be overwritten if the player has a cape cosmetic equipped.

    //Consider the default body type of the player. If they explicitly logged on with a slim skin, keep it that way. Otherwise, set them to the default player model.
    $bodyType = "geometry.humanoid.custom";
    var_dump($originalSkin->getGeometryName());
    if(str_contains($originalSkin->getGeometryName(), "Slim")) $bodyType = "geometry.humanoid.customSlim";
    //This plugin can only work for players wearing 64x64 skins. If a player has too large of a skin, their skin is replaced with one that works.

    $layeredSkin = null;
    if(strlen($originalSkin->getSkinData()) != 16384) { //If the player logs on with an unsuable highres custom skin.
      if($bodyType === "geometry.humanoid.customSlim") $layeredSkin = imagecreatefrompng(self::$plugin->getDataFolder() . "/required/" . "default_player_skinSlim.png");
      else $layeredSkin = imagecreatefrompng(self::$plugin->getDataFolder() . "/required/" . "default_player_skin.png");
    }else $layeredSkin = self::skinToImage($originalSkin->getSkinData()); //The player's original skin.

    $geometryToMerge[] = json_decode(file_get_contents(self::$plugin->getDataFolder() . "/required/" . "$bodyType.json"), true); //Start off with the default player geometry.

    foreach(CosmeticType::cases() as $type) {
      if (!is_null($costume->getCosmetic($type))) { //If that type of cosmetic is equipped at all
        if($type === CosmeticType::CAPE) $capeData = self::getDataFromImage($costume->getCosmetic($type)->getTexture());
        else {
          $layeredSkin = self::layerPNGs($costume->getCosmetic($type)->getTexture(), $layeredSkin); //Merge the previous image with the new image to make one for the next round
          $geometryToMerge[] = json_decode(file_get_contents($costume->getCosmetic($type)->getModelPath()), true); //Keep adding more and more layers.
        }
      }
    }
    return new Skin("Custom", self::getDataFromImage($layeredSkin), $capeData, $bodyType, self::mergeJSONToData($geometryToMerge));
  }

  private static function layerPNGs(GdImage $layer1, GdImage $layer2) : GdImage {
    imagepalettetotruecolor($layer2);
    imagepalettetotruecolor($layer1);
    imagesavealpha($layer1, true);
    imagealphablending($layer2, true);
    imagesavealpha($layer2, true);
    imagecopy($layer2, $layer1, 0, 0, 0, 0, 64, 64);
    return $layer2;
  }

  private static function mergeJSONToData(array $geometryToMerge) : string{
    $baseGeometry = $geometryToMerge[0]; //The base player model with no frills
    $combinedBones = array();
    $bonesToSkip = array(); //The key is the bone name, and the value is the parent bone. Both must be identical to something in here to be skipped.
    foreach($geometryToMerge as $geometry){ //Get all the json arrays from the different files...
      foreach($geometry["minecraft:geometry"][0]["bones"] as $bone){
        $parent = array_key_exists("parent", $bone) ? $bone["parent"] : null; //Special consideration for bones with no parent (root bone)
        if(array_key_exists($bone["name"], $bonesToSkip) && $bonesToSkip[$bone["name"]] === $parent) continue; //Skip it if it's a duplicate
          $combinedBones[] = $bone;
          $bonesToSkip[$bone["name"]] = $parent; //Add it to the list of things already looked over
      }
    }
    $baseGeometry["minecraft:geometry"][0]["bones"] = $combinedBones; //Add all the bones from the other files we just collected into this one
    return json_encode($baseGeometry);
  }

  //Not my code after this line.
  private static function skinToImage(string $skinData){
    // https://github.com/moskadev/EverybodyThonk/blob/master/src/supermaxalex/EverybodyThonk/SkinManager.php
    $img = imagecreatetruecolor(64, 64);
    imagealphablending($img, false);
    imagesavealpha($img, true);
    $stream = new BinaryStream($skinData);

    for($y = 0; $y < 64; ++$y){
      for($x = 0; $x < 64; ++$x){
        $r = $stream->getByte();
        $g = $stream->getByte();
        $b = $stream->getByte();
        $a = 127 - (int) floor($stream->getByte() / 2);

        $colour = imagecolorallocatealpha($img, $r, $g, $b, $a);
        imagesetpixel($img, $x, $y, $colour);
      }
    }
    return $img;
  }
  private static function getDataFromImage($image) : string{
    $skinbytes = "";
    for ($y = 0; $y < imagesy($image); $y++) {
      for ($x = 0; $x < imagesx($image); $x++) {
        $colorat = @imagecolorat($image, $x, $y);
        $a = ((~((int)($colorat >> 24))) << 1) & 0xff;
        $r = ($colorat >> 16) & 0xff;
        $g = ($colorat >> 8) & 0xff;
        $b = $colorat & 0xff;
        $skinbytes .= chr($r) . chr($g) . chr($b) . chr($a);
      }
    }
    imagedestroy($image);

    return $skinbytes;
  }
}