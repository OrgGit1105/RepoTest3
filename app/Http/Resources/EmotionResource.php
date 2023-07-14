<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-07-10
 */

namespace App\Http\Resources;

class EmotionResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
