<?php

namespace App\Http\Requests\ClientRequest;

use Illuminate\Foundation\Http\FormRequest;

class ListClientRequest extends FormRequest
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

    public function all($keys = null)
    {
        $data = parent::all($keys);
        $data['params'] = json_decode($this->get('queryParams'), true);

        return $data;
    }

    public function rules()
    {
        return [
            "params" => "nullable|array",
            "params.sort" => "array",
            "params.sort.*.name" => "string",
            "params.sort.*.order" => "string",
            "params.filters"  => "array",
            "params.filters.*.type" => "string",
            "params.filters.*.mode" => "string",
            "params.filters.*.selected_options" => "string",
            "params.filters.*.name" => "string",
            "params.filters.*.text" => "string",
            "params.global_search" => "string",
            "params.per_page" => "integer",
            "params.page" => "integer",

        ];
    }
}
