<?php

namespace Modules\MailTemplate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteTemplateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
      // who can delete Item?

      // 1- admin
      if (auth()->user()->isAdmin()) {
          return true;
      }

      // 2- creator
      if ($this->template->created_by == auth()->user()->id) {
          return true;
      }

      return false;
    }
}
