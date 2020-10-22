<?php
namespace App\Http\Controllers\External;

use App\Http\Controllers\Controller;
use App\Services\Temp\TempUserServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


/**
 * Class TempUserController
 * @package App\Http\Controllers\External
 */
class TempUserController extends Controller {

    private  $services;

    public function __construct(TempUserServices $services)
    {
        $this->services = $services;
    }

    /**
     * 注册临时用户
     * @param Request $request
     * @return JsonResponse
     */

    public function register(Request $request)
    {
        $data = $request->all();
        Log::info('-----register-temp-user---',$data);
        $user = $this->services->createdUser($data);
        return $this->sendSuccess('创建临时用户成功！',[
            'uuid' => $user->uuid,
            'token' => $user->api_token
        ]);
    }

}
