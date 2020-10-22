<?php

namespace App\Http\Requests;


class ProbeRequest extends FormRequest
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
                'title' => 'required',
                'group_id' => 'required',
            ];
        }elseif ($this->method() == 'PUT'){
            return [

            ];
        }
    }

    public function messages()
    {
        return [
            'title.required' => '名字必填',
            'dept_id.required' => '部门必填',
            'group_id.required' => '组必填',
        ];
    }

}
