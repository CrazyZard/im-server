<?php

namespace App\Http\Requests;


class LeaveMessageRequest extends FormRequest
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
            'ip' => 'required',
            'phone' => 'required',
            'opt_platform' => 'required',
            'action_url' => 'required',
            'land_page' => 'required',
            'land_page_title' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'ip.required' => 'ip丢失',
            'opt_platform.required' => '平台类型缺失',
            'action_url.required' => '访问页必填',
            'land_page.required' => '落地页缺失',
            'land_page_title.required' => '落地页标题缺失',
            'phone.required' => '手机号缺失'
        ];
    }

}
