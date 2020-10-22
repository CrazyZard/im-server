<?php

namespace App\Http\Requests;


class UserEducationRequest extends FormRequest
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
                'client_id' => 'required|unique:user_education,client_id',
                'name' => 'required',
                'vcc_id' => 'required',
                //'project' => 'required',
                'phone' => 'required',
                'client_num' => 'required',
            ];
        }elseif($this->method() == 'PUT'){
            return [
                //'project' => 'required'
            ];
        }

    }

    public function messages()
    {
        return [
            'client_id.required' => '学员id必填',
            'client_id.unique' => '学员id已有编号',
            'vcc_id.required' => '公司编号缺失',
            'project.required' => '项目必填',
            'accid.required' => '通讯账号缺失',
            'token.required' => '通讯密码缺失',
        ];
    }

}
