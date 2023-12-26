<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-06-07
 */

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\CheckIDRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class ArrivingReportRequest extends FormRequest
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
            case 'update':
                return $this->getCustomRule();
            case 'store':
                return $this->getCustomRule();
            case 'index':
                return $this->getCustomRule();
            default:
                return [];
        }
    }

    public function getCustomRule()
    {
        $type_work = config('analytic.type');
        if (Route::getCurrentRoute()->getActionMethod() == 'update') {
            return [
                'type_date' => 'required|in:' . implode(',', $type_work),
                'in_time' => 'required|date_format:Y-m-d H:i:s',
                'out_time' => 'nullable|required_if:type_date,' . implode(',', array_diff($type_work, [$type_work['work']])) . '|date_format:Y-m-d H:i:s',
            ];
        }
        if (Route::getCurrentRoute()->getActionMethod() == 'store') {
            return [
                'user_id' => ['required', new CheckIDRule(new User())],
                'type_date' => 'required|in:' . implode(',', $type_work),
                'in_time' => 'required|date_format:Y-m-d H:i:s',
                'out_time' => 'nullable|required_if:type_date,' . implode(',', array_diff($type_work, [$type_work['work']])) . '|date_format:Y-m-d H:i:s',
            ];
        }
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
            'out_time.required_if' => trans('api.working_time.out_time'),
            'required' => ':attribute not null'
        ];
    }
}
