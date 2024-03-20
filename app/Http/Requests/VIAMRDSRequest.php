<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-07-10
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class VIAMRDSRequest extends FormRequest
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
        switch (Route::getCurrentRoute()->getActionMethod()) {
            case 'index':
            case 'show':
            case 'store':
            case 'update':
            case 'delete':
                return $this->getCustomRule();
            default:
                return [];
        }
    }

    public function getCustomRule()
    {
        $rule1 = [
            'rds_manager_id' => 'required|exists:rds_manager,id',
            'database_name' => 'required|string|max:100',
        ];
        $rule2 = array_merge($rule1, [
            'permission' => 'required|array',
            'permission.*' => 'required|exists:rds_permission,id'
        ]);

        switch (Route::getCurrentRoute()->getActionMethod()) {
            case 'index':
            case 'show':
            case 'delete':
                return $rule1;
            case 'store':
            case 'update':
                return $rule2;
        }
    }
}
