<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckIDRule implements Rule
{
  protected $model;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($model)
    {
        //
      $this->model=$model;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //
      if ($value) {
        $data = $this->model->where('id', $value)->first();
        if (!$data) {
          return false;
        }
      }
      return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
      return trans('api.model.nonexistent');
    }
}
