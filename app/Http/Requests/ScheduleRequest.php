<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-07-17
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class ScheduleRequest extends FormRequest
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
                case 'scheduleOneDay':
                    return $this->getCustomRule();
                default:
                    return [];
          }
    }

     public function getCustomRule(){
        if(Route::getCurrentRoute()->getActionMethod() == 'index'){
            return [
                'year_month' => 'required|date_format:Y-m',
            ];
        }
        if(Route::getCurrentRoute()->getActionMethod() == 'scheduleOneDay'){
            return  [
                'year_month' => 'required|date_format:Y-m-d',
            ];
        }
     }

    public function messages()
    {
        return [
            'required' => ':attribute not null',
            'date_format' => trans('validation.date_format')
        ];
    }
}
