<?php

namespace App\Http\Requests;

class NoticeRequest extends FormRequest
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
        switch($this->method()){
                case 'POST':
                     return [
                         'name' => 'required',
                         'app_id' => 'required',
                         'account_id' => 'required',
                         'start_at' => 'required',
                         'end_at' => 'required',
                         'content' => 'required',
                     ];
                  break;
            default:
                return [];
                break;
        }

    }

    public function messages()
    {
        return [
            'name.required' => '公告题目必填',
            'app_id.required' => '公示应用必填',
            'account_id.required' => '应用账号必填',
            'start_at.required' => '公式期限必填',
            'end_at.required' => '公式期限必填',
            'content.required' => '内容必填',
        ];
    }

}
