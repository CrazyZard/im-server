<?php

namespace App\Http\Requests;


class SessionGroupRequest extends FormRequest
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
        if($this->method() == 'POST'){
            return [
                'name' => 'required|unique:session_groups,name',
            ];
        }else{
            return [

            ];
        }

    }

    public function messages()
    {
        return [
            'name.required' => '组名必填',
            'name.unique' => '该名称已存在',
        ];
    }

}
