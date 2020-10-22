<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class UserTempType extends Enum
{
    const Status_Invalid = 0; //无效 默认
    const Status_Valid = 1; // 有效
    const Status_Abnormal = 2 ; //异常名片
    const Status_Send = 3 ; //已推送名片

    const Session_Open = 1 ; //会话有效状态
    const Session_Close = 0 ; //会话无效状态
}
