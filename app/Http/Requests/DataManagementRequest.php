<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2021-06-23
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class DataManagementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
  public function rules()
  {
    switch (Route::getCurrentRoute()->getActionMethod()){
      case 'update':
        return $this->getCustomRule();
        break;
      case 'store':
        return $this->getCustomRule();
        break;
      default:
        return [];
    }
  }

  public function getCustomRule(){
    if(Route::getCurrentRoute()->getActionMethod() == 'update'){
      return [
//        'file_name'      => "required",
      ];
    }
    if(Route::getCurrentRoute()->getActionMethod() == 'store'){
      return  [
//        'file_name'      => "required",
      ];
    }
  }

  public function messages()
  {
    return [
      'required' => ':attribute not null'
    ];
  }
}

