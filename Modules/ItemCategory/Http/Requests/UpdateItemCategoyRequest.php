<?php

namespace Modules\ItemCategory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateItemCategoyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
      return [
          'name' => 'required|string|unique:item_categories,name,'.$this->item_category->id,
          'description' => 'string',
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
      if ($this->item_category->created_by == auth()->user()->id) {
          return true;
      }

      return false;
    }
}
