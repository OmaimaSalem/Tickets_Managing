<?php

namespace App\Http\Requests\Attribute;

use Illuminate\Foundation\Http\FormRequest;

class EmailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;

        if (auth()->user()->isAdmin()) {
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'attribute_id'    => '',
            'attribute_name'  => '',
            'email_content'   => '',
            'email_time'      => '',
            'mail_start'      => '',
            'attribute_value' => '',
            'user_type'       => '',
            'next_run_time'   => ''
        ];
    }
}
