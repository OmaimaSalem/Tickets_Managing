<?php

namespace App\Http\Requests\UserRequest;

use Illuminate\Foundation\Http\FormRequest;

class AddUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (auth()->user()->isAdmin() || auth()->user()->can('user-create')) {
            return true;
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
        $rules = [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users',
            'password' => 'required|string|min:6',
            'type' => 'required|string',
            'roles' => 'required|array',
            'roles.*.id' => 'required|integer',
            'roles.*.name' => 'required|string',
            'vacation' => 'integer',
            'skip_vacation_limit' => 'boolean',
            'dynamic_attributes' => 'array',
            'dynamic_attribute.*.id' => 'required|integer',
            'dynamic_attribute.*.value' => 'required|string',
        ];

        if(request()->type == "client") {
            unset($rules['roles']);
            unset($rules['roles.*.id']);
            unset($rules['roles.*.name']);
        }

        return $rules;
    }
}
