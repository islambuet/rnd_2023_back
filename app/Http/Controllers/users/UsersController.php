<?php
namespace App\Http\Controllers\users;

use App\Helpers\TaskHelper;
use App\Http\Controllers\RootController;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;


class UsersController extends RootController
{
    public $api_url='users';
    public $permissions;
    public function __construct()
    {
        parent::__construct();
        $this->permissions=TaskHelper::getPermissions($this->api_url,$this->user);
    }

    public function initialize(): JsonResponse
    {
        if ($this->permissions->action_0 == 1){
            $response= [];
            $response['error'] = '';
            $response['permissions'] = $this->permissions;
            $response['hidden_columns'] =TaskHelper::getHiddenColumns($this->api_url,$this->user);
            if($this->user->user_group_id==ID_USERGROUP_SUPERADMIN)
            {
                $response['user_groups']= DB::table(TABLE_USER_GROUPS)->select('id','name')->orderBy('id', 'ASC')->get()->toArray();
            }
            else{
                $response['user_groups']= DB::table(TABLE_USER_GROUPS)->select('id','name')->where('id','!=',ID_USERGROUP_SUPERADMIN)->orderBy('id', 'ASC')->get()->toArray();
            }
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
            /** @noinspection DuplicatedCode */
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
    public function getItem(Request $request,$itemId): JsonResponse
    {
        if ($this->permissions->action_0 == 1){
            /** @noinspection DuplicatedCode */
            $query=DB::table(TABLE_USERS.' as users');
            $query->select('users.id','users.username','users.user_group_id','users.name','users.email','users.mobile_no','users.ordering','users.status','users.created_at');
            $query->join(TABLE_USER_GROUPS.' as user_groups', 'user_groups.id', '=', 'users.user_group_id');
            $query->addSelect('user_groups.name as user_group_name');
            $query->join(TABLE_USER_TYPES.' as user_types', 'user_types.id', '=', 'users.user_type_id');
            $query->addSelect('user_types.name as user_type_name');
            $query->where('users.id','=',$itemId);
            $result = $query->first();
            if(!$result){
                return response()->json(['error'=>'ITEM_NOT_FOUND','messages'=>__('Invalid Id '.$itemId)]);
            }
            $response=[];
            $response['error'] = '';
            $response['item'] = $result;
            return response()->json($response);
        }
        else{
            return response()->json(['error'=>'ACCESS_DENIED','messages'=>$this->permissions]);
        }
    }
}

