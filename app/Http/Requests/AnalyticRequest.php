<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-07-17
 */

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\CheckIDRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class AnalyticRequest extends FormRequest
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
                case 'index':
                    return $this->getCustomRule();
                default:
                    return [];
          }
    }

    public function getCustomRule()
    {
        if (Route::getCurrentRoute()->getActionMethod() == 'index') {
            return [
                'start_date' => 'nullable|date_format:Y-m-d',
                'end_date' => 'nullable|date_format:Y-m-d',
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
