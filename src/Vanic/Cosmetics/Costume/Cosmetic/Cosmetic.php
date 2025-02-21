<?php

namespace Vanic\Cosmetics\Costume\Cosmetic;

use GdImage;
use pocketmine\permission\Permission;

class Cosmetic {

  private string $cosmeticID;
  private CosmeticType $type;
  private string $modelPath;
  private string $texturePath;

  public function __construct(string $key, CosmeticType $type, string $texturePath) {
    $this->cosmeticID = $key;
    $this->type = $type;
    $this->texturePath = $texturePath;
  }
  public function getID(): string {
    return $this->cosmeticID;
  }
  public function getType() : CosmeticType {
    return $this->type;
  }
  public function getTexture() : GdImage{
    return imagecreatefrompng($this->texturePath);
  }
  public function specifyGeometryModel(string $modelPath): void{
    $this->modelPath = $modelPath;
  }

  public function getModelPath() : string {
      return $this->modelPath;
  }
}