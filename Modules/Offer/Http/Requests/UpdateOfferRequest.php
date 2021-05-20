<?php

namespace Modules\Offer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOfferRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'number' => 'string',
            'client_id' => 'required|numeric',
            'date' => 'required|date',
            'total' => 'required|numeric',
            'total_tax' => 'required|numeric',
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
            'items' => 'required',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // who can update ??
        // 1- admin
        if (auth()->user()->isAdmin()) {
            return true;
        }

        // 2- creator
        if ($this->offer->created_by == auth()->user()->id) {
            return true;
        }

        return false;
    }
}
