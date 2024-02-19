<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-07-10
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class PolicyRequest extends FormRequest
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
                case 'getProject':
                    return $this->getCustomRule();
                default:
                    return [];
          }
    }

     public function getCustomRule(){
        if(Route::getCurrentRoute()->getActionMethod() == 'update'){
            return [
                'name' => 'required|unique:policies,name,' . $this->route('policy'). ',id,deleted_at,NULL|max:32|regex:/^[a-zA-Z_][a-zA-Z0-9._-]{0,31}$/',
                'type' => 'required|in:' . implode(',', POLICY_TYPE),
                'instance_id' => 'nullable|string|required_if:type,' . POLICY_TYPE['EC2_admin'] . ',' . POLICY_TYPE['EC2_deploy'],
                'project_name' => 'nullable|string|required_if:type,' . POLICY_TYPE['EC2_deploy'],
                'arn_role' => 'nullable|string|required_if:type,' . POLICY_TYPE['AWS'] . '|regex:/^arn:aws:iam::[0-9]+:role\/[\w+=,.@-]+$/'
            ];
        }
        if(Route::getCurrentRoute()->getActionMethod() == 'store'){
            return  [
                'name' => 'required|unique:policies,name,NULL,id,deleted_at,NULL|max:32|regex:/^[a-zA-Z_][a-zA-Z0-9._-]{0,31}$/',
                'type' => 'required|in:' . implode(',', POLICY_TYPE),
                'instance_id' => 'nullable|string|required_if:type,' . POLICY_TYPE['EC2_admin'] . ',' . POLICY_TYPE['EC2_deploy'],
                'project_name' => 'nullable|string|required_if:type,' . POLICY_TYPE['EC2_deploy'],
                'arn_role' => 'nullable|string|required_if:type,' . POLICY_TYPE['AWS'] . '|regex:/^arn:aws:iam::[0-9]+:role\/[\w+=,.@-]+$/'
            ];
        }
        if(Route::getCurrentRoute()->getActionMethod() == 'getProject'){
             return  [
                 'instance_id' => 'string|required',
             ];
         }
     }

    public function messages()
    {
        return [
            'required' => ':attribute not null',
            'instance_id.required_if' => trans('api.policy.instance_id'),
            'project_name.required_if' => trans('api.policy.project_name'),
            'name.regex' => trans('api.policy.name_regex')
        ];
    }
}
