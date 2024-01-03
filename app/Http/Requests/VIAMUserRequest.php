<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-07-10
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class VIAMUserRequest extends FormRequest
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
                case 'store':
                    return $this->getCustomRule();
                default:
                    return [];
          }
    }

     public function getCustomRule(){
        if(Route::getCurrentRoute()->getActionMethod() == 'store'){
            return [
                'name' => 'required|unique:viam_users,name,max:255',
                'policy_id' => 'required|array',
                'policy_id.*' => 'required|exists:policies,id',
                'description' => 'nullable|string'
            ];
        }
        if(Route::getCurrentRoute()->getActionMethod() == 'update'){
            return  [
                'name' => 'required|unique:viam_users,name,' . $this->route('viam_user'). '|max:255',
                'policy_id' => 'required|array',
                'policy_id.*' => 'required|exists:policies,id',
                'description' => 'nullable|string'
            ];
        }
     }
}
