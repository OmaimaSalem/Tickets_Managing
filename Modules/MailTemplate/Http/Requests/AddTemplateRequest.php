<?php

namespace Modules\MailTemplate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddTemplateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
      return [
          'title' => 'required|string|unique:mail_templates',
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
        return true;
    }
}
