<?php

namespace app\Services\Temp;

trait Format
{
    public function success($data){
        return [
            'status' => 200,
            'message' => '操作成功',
            'data' => $data
        ];
    }
}
