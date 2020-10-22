<?php

namespace App\Http\Requests;

class RoleRequest extends FormRequest
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
            'name' => 'sometimes|unique:system_roles',
            'display_name' => 'require|unique:system_roles',
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => '角色重复',
            'display_name.require' => '角色名重复',
        ];
    }

}
