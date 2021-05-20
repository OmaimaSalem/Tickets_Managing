<?php

namespace Modules\OfferConversations\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddOfferConversationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'offer_id' => 'required',
            'offer_mail' => 'required|string',
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
