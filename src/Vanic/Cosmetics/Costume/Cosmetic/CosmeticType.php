<?php

namespace Vanic\Cosmetics\Costume\Cosmetic;

enum CosmeticType: string { //Backed enum
  case CAPE = "CAPE";
  case HEAD = "HEAD";
  case UPPER_BODY = "UPPER_BODY";
  case LOWER_BODY = "LOWER_BODY";
  case FEET = "FEET";
}
