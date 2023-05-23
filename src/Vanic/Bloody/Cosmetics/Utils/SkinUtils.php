<?php

namespace Vanic\Bloody\Cosmetics\Utils;

use pocketmine\entity\Skin;
use pocketmine\player\Player;
use Vanic\Bloody\Cosmetics\Main;
use pocketmine\utils\BinaryStream;

class SkinUtils {
  
  private static $main;
  
  public static function init(Main $plugin){
    self::$main = $plugin;
  }
  
  public static function getCapeSkin(Skin $skin, string $cape) : Skin{
    if($cape === "") return $skin;
    return new Skin("Custom", $skin->getSkinData(), self::getImageData(imagecreatefrompng(self::$main->getDataFolder() . "capes/" . "$cape.png")), $skin->getGeometryName(), $skin->getGeometryData());
  }
  
  public static function layerSkin(Skin $skin, string $cosmetic) : Skin{
    if($cosmetic === "") return $skin;
    $layer2 = self::skinToPNG($skin->getSkinData());
    $layer1 = imagecreatefrompng(self::$main->getDataFolder() ."$cosmetic/" .  "$cosmetic.png");
    
    imagepalettetotruecolor($layer2);
    imagepalettetotruecolor($layer1);
    imagesavealpha($layer1, true);
    imagealphablending($layer2, true);
    imagesavealpha($layer2, true);
    imagecopy($layer2, $layer1, 0, 0, 0, 0, 64, 64);
    
    return new Skin("Custom", self::getImageData($layer2), "", "geometry." . $cosmetic, file_get_contents(self::$main->getDataFolder() . "$cosmetic/" . "$cosmetic.json"));
  }
  
  public static function skinToPNG(string $skinData){
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
  
  public static function getImageData($image) : string{
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
  
  public static function getResetSkin(Skin $playerSkin) {
    return new Skin("Custom", self::getImageData(self::skinToPNG($playerSkin->getSkinData())));
  }
}