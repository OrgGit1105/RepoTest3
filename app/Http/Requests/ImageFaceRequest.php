<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-06-07
 */

namespace App\Http\Requests;

use App\Models\ImageFace;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class ImageFaceRequest extends FormRequest
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
                case 'create':
                    return $this->getCustomRule();
                default:
                    return [];
          }
    }

     public function getCustomRule(){
        if(Route::getCurrentRoute()->getActionMethod() == 'update'){
            return [

            ];
        }
        if(Route::getCurrentRoute()->getActionMethod() == 'create'){
            return  [
              'name'     => 'required',
              'file'     => 'required',
              'user_id'     => 'required|numeric',
              'type' => 'required|in:WITH_MASK,WITHOUT_MASK',
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
