<?php

namespace App\Http\Requests;


class UserTempRequest extends FormRequest
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
        if ($this->method() == 'POST'){
            return [
                'vcc_id' => 'required',
                'group_id' => 'required',
                'ip' => 'required',
                'opt_platform' => 'required',
                'action_url' => 'required',
                'land_page' => 'required',
                'land_page_title' => 'required',
            ];
        }else{
            return [

            ];
        }
    }

    public function messages()
    {
        return [
            'vcc_id.required' => '企业编码缺失',
            'group_id.required' => '学员id必填',
            'ip.unique' => '学员id已有编号',
            'opt_platform.required' => '平台类型缺失',
            'action_url.required' => '访问页必填',
            'land_page.required' => '落地页缺失',
            'land_page_title.required' => '落地页标题缺失',
            'search_term.required' => '搜索缺失',
        ];
    }

}
