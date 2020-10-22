<?php

namespace App\Http\Controllers;

use App\TempUser;
use App\UserTemp;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request) 
    {
        // 验证注册字段
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6']
        ])->validate();

        // 在数据库中创建用户并返回包含 api_token 字段的用户数据
        return UserTemp::create([
            'name' => $request->input('name'),
            'uuid' => Hash::make($request->input('password')),
            'api_token' => Str::random(60)
        ]);

      
    }
}