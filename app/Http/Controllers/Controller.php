<?php

namespace App\Http\Controllers;

use Dingo\Api\Routing\Helpers;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use Helpers;
    protected $user;

    protected static function error($message){
        throw  new \Exception($message,401);
    }

    protected function sendSuccess($message ="操作成功",$data=array())
    {
        return response()->json([
            'status_code'=>200,
            'message' => $message,
            'data' => $data,
        ]);
    }

    protected function sendData($data=array())
    {
        return response()->json([
            'status_code'=>200,
            'data' => $data,
        ],200);
    }

    protected function sendError($message ="操作失败", $exception=null, $status= 401)
    {
        return response()->json([
            'status_code'=>401,
            'message' => $message,
            'data'=> $exception ? $exception->getMessage() : array(),
        ], $status);
    }

    protected function _filterData(&$data,&$dataName)
    {
        foreach ($data as $key => $value) {
            if (!in_array($key, $dataName)) {
                unset($data[$key]);
            }
            if (is_null($value)) {
                unset($data[$key]);
                continue;
            }
            if ($value == "") {
                unset($data[$key]);
                continue;
            }
        }
    }
}
