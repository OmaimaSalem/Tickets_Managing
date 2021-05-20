<?php

namespace Modules\MailTemplate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTemplateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
      return [
          'title' => 'required|string',
          'subject' => 'required|string',
          'body' => 'required|string',
          'mail_template_category_id' => 'required|integer'
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
