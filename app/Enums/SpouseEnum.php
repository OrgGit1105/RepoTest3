<?php
/**
 * Created by PhpStorm.
 * User: cuongn
 * Date: 11/23/2020
 */

namespace Enum;

/**
 * @OA\Schema(
 *     schema="Spouse",
 *     title="Spouse",
 *     description="Phối ngẫu:
 *     - `1`: No
 *     - `2`: Yes",
 *     type="int",
 *     enum={1,2},
 * )
 */
class SpouseEnum extends Enum
{
  const NO  = [1, 'No'];
  const YES = [2, 'YES'];
}
