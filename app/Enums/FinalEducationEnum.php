<?php


namespace Enum;

/**
 * @OA\Schema(
 *     schema="Final_education",
 *     title="Final_education",
 *     description=":
 *     - `0`: Primary school
 *     - `1`: Junior high school
 *     - `2`: High school
 *     - `3`: Vocational school
 *     - `4`: College of technology
 *     - `5`: Junior college
 *     - `6`: University",
 *     type="int",
 *     enum={0,1,2,3,4,5,6},
 * )
 */
class FinalEducationEnum extends Enum
{
  const Primaryschool  = [0, 'Primary school'];
  const Juniorhighschool = [1, 'Junior high school'];
  const Highschool= [2, 'High school'];
  const Vocationalschool = [3, 'Vocational school'];
  const Collegeoftechnology = [4, 'College of technology'];
  const Juniorcollege = [5, 'Junior college'];
  const University = [6, 'University'];
}
