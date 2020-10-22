<?php

namespace App\Http\Requests;


class SessionGroupUserRequest extends FormRequest
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
        return [
            'group_id' => 'required',
            'user_id' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'group_id.required' => '组必填',
            'user_id.required' => '用户必填',
        ];
    }

}
