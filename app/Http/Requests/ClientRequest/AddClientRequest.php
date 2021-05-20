<?php

namespace App\Http\Requests\ClientRequest;

use Illuminate\Foundation\Http\FormRequest;

class AddClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $rules = [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users',
            'password' => 'required|string|min:6',
            'roles' => 'required|array',
            'roles.*.id' => 'required|integer',
            'roles.*.name' => 'required|string',
            'vacation' => 'integer',
            'skip_vacation_limit' => 'boolean',
            'dynamic_attributes' => 'array',
            'dynamic_attribute.*.id' => 'required|integer',
            'dynamic_attribute.*.value' => 'required|string',
        ];
    }
}
