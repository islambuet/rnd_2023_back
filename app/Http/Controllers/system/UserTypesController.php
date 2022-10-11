<?php
namespace App\Http\Controllers\system;

use App\Http\Controllers\RootController;
use Illuminate\Http\JsonResponse;



class UserTypesController extends RootController
{
    public function initialize(): JsonResponse
    {
        if ($this->permissions->action_0 == 1){
            $response= [];
            $response['error'] = '';
            $response['permissions'] = $this->permissions;
            return response()->json($response);
        }
        else{
            return response()->json(['error'=>'ACCESS_DENIED','messages'=>__('You do not have access on this page')]);
        }
    }
}

