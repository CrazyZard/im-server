<?php
namespace App\Http\Requests;

use Symfony\Component\HttpKernel\Exception\HttpException; // 注意是抛出这个类型的异常。
use Illuminate\Contracts\Validation\Validator;
use \Illuminate\Foundation\Http\FormRequest as BaseFormRequest;
class FormRequest extends BaseFormRequest{

    //主要是重写这个方法。
    protected function failedValidation(Validator $validator)
    {
        throw new HttpException(401, $validator->errors()->first());
    }
}
