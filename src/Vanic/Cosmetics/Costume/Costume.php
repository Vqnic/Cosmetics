<?php

namespace Vanic\Cosmetics\Costume;

use pocketmine\player\Player;
use Vanic\Cosmetics\Costume\Cosmetic\Cosmetic;
use Vanic\Cosmetics\Costume\Cosmetic\CosmeticType;
use Vanic\Cosmetics\Utils\SkinUtils;

class Costume {
  private Player $wearer;
  private array $cosmetics; //Will contain all cosmetics the player has.

    /**
     * @throws \JsonException
     */
    public function __construct(Player $wearer, ?Cosmetic $cape, ?Cosmetic $head, ?Cosmetic $upperBody, ?Cosmetic $lowerBody, ?Cosmetic $feet) {
    $this->wearer = $wearer;
    $this->cosmetics[CosmeticType::CAPE->value] = $cape;
    $this->cosmetics[CosmeticType::HEAD->value] = $head;
    $this->cosmetics[CosmeticType::UPPER_BODY->value] = $upperBody;
    $this->cosmetics[CosmeticType::LOWER_BODY->value] = $lowerBody;
    $this->cosmetics[CosmeticType::FEET->value] = $feet;
    $this->wearer->setSkin(SkinUtils::getMergedSkin($this)); //Update skin to show the change.
    $this->wearer->sendSkin();
  }

    /**
     * @throws \JsonException
     */
    public function setCosmetic(CosmeticType $slot, ?Cosmetic $cosmetic): void {
    $this->cosmetics[$slot->value] = $cosmetic; //If cosmetic is null, the cosmetic will be entirely removed
    $this->wearer->setSkin(SkinUtils::getMergedSkin($this)); //Update skin to show the change.
    $this->wearer->sendSkin();
  }

  public function getCosmetic(CosmeticType $type) : ?Cosmetic {
    return $this->cosmetics[$type->value];
  }

  public function getWearer() : Player { return $this->wearer; }
}