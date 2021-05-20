<?php

namespace Modules\Contract\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddContractRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'number' => 'required|string|unique:contracts',
            'client_id' => 'required|numeric',
            'date' => 'required|date',
            'total' => 'required|numeric',
            'greeting' => 'nullable',
            'street' => 'nullable',
            'country' => 'nullable',
            'city' => 'nullable',
            'first_name' => 'nullable',
            'postal_code' => 'nullable',
            'telephone' => 'nullable',
            'mobile' => 'nullable',
            'title' => 'nullable',
            'name' => 'nullable',
            'fax' => 'nullable',
            'email' => 'nullable',
            'others' => 'nullable',
            'notes' => 'nullable',
            'printed_text' => 'nullable',
            'items' => 'nullable',
            'offer' => 'nullable',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
