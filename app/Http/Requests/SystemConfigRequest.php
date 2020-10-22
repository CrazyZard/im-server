<?php

namespace App\Http\Requests;


class SystemConfigRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return  [
            'type' => 'required',
            'name' => 'required',
            'sort' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'type.required' => '类型必填',
            'name.required' => '内容必填',
            'sort.required' => '排序必填',
        ];
    }

}
