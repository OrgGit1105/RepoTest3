<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-06-07
 */

namespace App\Http\Requests;

use App\Rules\SshKeyRule;
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
                 'name' => 'required|unique:users,name,' . $this->route('id'). ',id,deleted_at,NULL|max:32|regex:/^[a-zA-Z][a-zA-Z0-9._-]{0,31}$/',
                 'email' => 'required|email',
                 'gender' => 'nullable|in:0,1',
                 'birthday' => 'nullable|date-format:Y-m-d',
                 'address' => 'nullable|string',
                 'telephone' => 'nullable|string',
                 'entry_date' => 'nullable|date-format:Y-m-d',
                 'slack_id' => 'nullable|string',
                 'skype_id' => 'nullable|string',
                 'github_id' => 'nullable|string',
                 'viam_user_id' => 'required|numeric|exists:viam_users,id',
                 'ssh_public_key' => ['nullable', new SshKeyRule()],
                 'retirement_date' => 'nullable|date-format:Y-m-d',
             ];
         }
         if (Route::getCurrentRoute()->getActionMethod() == 'store') {
             return [
                 'name' => 'required|unique:users,name,NULL,id,deleted_at,NULL|max:32|regex:/^[a-zA-Z][a-zA-Z0-9._-]{0,31}$/',
                 'email' => 'required|unique:users,email,NULL,id,deleted_at,NULL|email',
                 'gender' => 'nullable|in:0,1',
                 'birthday' => 'nullable|date-format:Y-m-d',
                 'address' => 'nullable|string',
                 'telephone' => 'nullable|string',
                 'entry_date' => 'nullable|date-format:Y-m-d',
                 'paid_off_start' => 'nullable|numeric',
                 'slack_id' => 'nullable|string',
                 'skype_id' => 'nullable|string',
                 'github_id' => 'nullable|string',
                 'viam_user_id' => 'required|numeric|exists:viam_users,id',
                 'ssh_public_key' => ['nullable', new SshKeyRule()],
                 'password' => 'required|min:4|confirmed',
             ];
         }
     }

    public function messages()
    {
        return [
            'required' => ':attribute not null',
            'name.regex' => trans('api.user.name_regex')
        ];
    }
}
