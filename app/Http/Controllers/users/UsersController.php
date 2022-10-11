<?php
namespace App\Http\Controllers\users;

use App\Helpers\TaskHelper;
use App\Http\Controllers\RootController;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;


class UsersController extends RootController
{
    public function initialize(): JsonResponse
    {
        if ($this->permissions->action_0 == 1){
            $response= [];
            $response['error'] = '';
            $response['permissions'] = $this->permissions;
            $response['hidden_columns'] =TaskHelper::getHiddenColumns($this->api_url,$this->user);
            return response()->json($response);
        }
        else{
            return response()->json(['error'=>'ACCESS_DENIED','messages'=>__('You do not have access on this page')]);
        }
    }
    public function getItems(Request $request): JsonResponse
    {
        if ($this->permissions->action_0 == 1){
            $response=[];
            $response['error'] = '';
            $perPage=$request->input('perPage',2);

            $query=DB::table(TABLE_USERS.' as users');
            $query->select('users.id','users.username','users.user_group_id','users.name','users.email','users.mobile_no','users.ordering','users.status','users.created_at');
            $query->join(TABLE_USER_GROUPS.' as user_groups', 'user_groups.id', '=', 'users.user_group_id');
            $query->addSelect('user_groups.name as user_group_name');
            $query->join(TABLE_USER_TYPES.' as user_types', 'user_types.id', '=', 'users.user_type_id');
            $query->addSelect('user_types.name as user_type_name');
            $query->orderBy('users.ordering', 'ASC');
            $query->orderBy('users.id', 'DESC');
            $query->where('users.status','!=',SYSTEM_STATUS_DELETE);//
            $results=$query->paginate($perPage)->toArray();
            $response['items'] = $results;
            return response()->json($response);
        }
        else{
            return response()->json(['error'=>'ACCESS_DENIED','messages'=>__('You do not have access on this page')]);
        }
    }
}

