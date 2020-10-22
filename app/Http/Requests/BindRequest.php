<?php

namespace App\Http\Requests;


class BindRequest extends FormRequest
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
                'accid' => 'required',
                'faccid' => 'required',
//                'project_id' => 'required',
                'project_name' => 'required',
            ];
        }elseif ($this->method() == 'PUT'){
            return [
                'accid' => 'required',
                'faccid' => 'required',
            ];
        }else{
            return [
                'accid' => 'required',
                'faccid' => 'required',
//                'project_id' => 'required',
            ];
        }

    }

    public function messages()
    {
        return [
            'accid.required' => '学员accid缺失',
            'faccid.required' => '老师accid缺失',
            'project_id.required' => '项目id缺失',
            'project_name.required' => '项目名称缺失',
        ];
    }

}
