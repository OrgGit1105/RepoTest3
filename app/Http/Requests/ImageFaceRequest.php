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
                case 'index':
                  return $this->getCustomRule();
                case 'compareFace':
                  return $this->getCustomRule();
                case 'checkImage':
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
              'file'     => 'required|array',
              'file.*'     => 'required|mimes:jpg,jpeg,png',
              'user_id'     => 'required|numeric',
              'type' => 'required|in:WithoutMask,WithMask',
            ];
        }
       if(Route::getCurrentRoute()->getActionMethod() == 'index'){
         return  [
           'user_id'     => 'required|numeric',
         ];
       }
       if(Route::getCurrentRoute()->getActionMethod() == 'compareFace'){
         return [
           'time' => 'required|in:in,out',
//           'type' => 'required|in:WithoutMask,WithMask',
           'file' => 'required|mimes:jpg,jpeg,png'
         ];
       }
       if(Route::getCurrentRoute()->getActionMethod() == 'checkImage'){
         return [
           'file' => 'required|mimes:jpg,jpeg,png'
         ];
       }
     }

    public function messages()
    {
        return [
            'required' => ':attribute not null',
            'type.in' => ':attribute must in WithoutMask or WithMask',
        ];
    }
}
