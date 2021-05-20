<?php

namespace Modules\OfferItems\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOfferItemRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|string',
            'offer_id' => 'required|numeric',
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
        if ($this->offerItem->created_by == auth()->user()->id) {
            return true;
        }

        return false;
    }
}
