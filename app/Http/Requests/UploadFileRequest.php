<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2023-07-17
 */

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\CheckIDRule;
use App\Rules\checkUserIdRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class UploadFileRequest extends FormRequest
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
            case 'store':
                return $this->getCustomRule();
            default:
                return [];
        }
    }

    public function getCustomRule()
    {
        if (Route::getCurrentRoute()->getActionMethod() == 'store') {
            return [
                'file' => [
                    'required', 'max:3072',
                    function ($attribute, $value, $fail) {
                        $mime = $value->getMimeType();
                        $extension = $value->getClientOriginalExtension();
                        if ($mime != 'text/plain' || $extension != 'pem') {
                            return $fail(trans('api.key_file.extension'));
                        }
                    }],
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
