<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Api\V1\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Resources\Api\V1\AdminResource;
use App\Http\Controllers\AbstractApiController;

class AdminController extends AbstractApiController
{
    //
    public function index()
    {
        //
        $users = Admin::all();
        return AdminResource::make($users);
    }

   

    /**
     * @param LoginRequest $request
     * 
     * @return [type]
     */
    public function login(LoginRequest $request)
    {
        $credentials =$request->validated();
        $token = auth()->attempt($credentials);
        if (empty($token)) {
            return $this->errorResponse('認証エラー', 401000);
        }
        $token = $this->makeJwtToken($token);
        return $this->modelResponse($this->getUserResponse(), $token, 200000);
    }

    /**
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        return $this->modelResponse($this->getUserResponse());
    }
    /**
     * @return [type]
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
    /**
     * @return [type]
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * @param mixed $token
     * 
     * @return [type]
     */
    protected function respondWithToken($token)
    {
        $authUser = auth()->user()->id;
        $success['message'] = 'sucess';
        $success['user'] = auth()->user();
        $success['access_token'] = $token;
        $success['token_type'] =  'Bearer';
        $success['expires_in'] = auth()->factory()->getTTL() * 60;
        return response()->json($success, 200);
       
    }

    /**
     * @param string|bool $token
     * 
     * @return array
     */
    private function makeJwtToken($token) {
        /** @noinspection PhpUndefinedMethodInspection */
        return [
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth()->factory('')->getTTL() * 60
        ];
    }


    /**
     * @return Admin|null
     */
    private function getUserResponse(): ?Admin
    {
        $user = auth()->user();
        if (empty($user)) {
            return null;
        }
        if (!($user instanceof Admin)) {
            return null;
        }
        return $user;
    }

}
