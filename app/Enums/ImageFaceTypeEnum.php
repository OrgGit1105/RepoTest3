<?php

namespace App\Enums;

use Enum\Enum;

class ImageFaceTypeEnum extends Enum
{
  const WITH_MASK = [1, 'WITH_MASK'];
  const WITHOUT_MASK = [2, 'WITHOUT_MASK'];
}
