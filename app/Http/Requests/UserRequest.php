<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-06-07
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class UserRequest extends FormRequest
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
         if (Route::getCurrentRoute()->getActionMethod() == 'update') {
             return [
                 'name' => 'required',
                 'email' => 'required|email',
                 'gender' => 'nullable|in:0,1',
                 'birthday' => 'nullable|date-format:Y-m-d',
                 'address' => 'nullable|string',
                 'telephone' => 'nullable|string',
                 'paid_off' => 'nullable|numeric',
                 'slack_id' => 'nullable|string',
                 'skype_id' => 'nullable|string',
                 'github_id' => 'nullable|string',
                 'viam_user_id' => 'required|numeric',
                 'retirement_date' => 'nullable|date-format:Y-m-d',
             ];
         }
         if (Route::getCurrentRoute()->getActionMethod() == 'store') {
             return [
                 'name' => 'required',
                 'email' => 'required|unique:users,email|email',
                 'gender' => 'nullable|in:0,1',
                 'birthday' => 'nullable|date-format:Y-m-d',
                 'address' => 'nullable|string',
                 'telephone' => 'nullable|string',
                 'entry_date' => 'nullable|date-format:Y-m-d',
                 'paid_off' => 'nullable|numeric',
                 'slack_id' => 'nullable|string',
                 'skype_id' => 'nullable|string',
                 'github_id' => 'nullable|string',
                 'viam_user_id' => 'required|numeric',
                 'password' => 'required|min:3|confirmed',
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
