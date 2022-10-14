<?php
namespace App\Http\Controllers\users;

use App\Helpers\TaskHelper;
use App\Http\Controllers\RootController;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


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
    public function saveItem(Request $request): JsonResponse{
        $itemId = $request->input('id',0);
        $this->checkSaveToken();
        if($itemId>0){
            return  $this->saveOldItem($request,$itemId);
        }
        else{
            return  $this->saveNewItem($request);
        }
    }
    private function saveNewItem(Request $request): JsonResponse{
        if ($this->permissions->action_1 != 1){
            return response()->json(['error' => 'ACCESS_DENIED', 'messages' => __('You do not have add access')]);
        }
        //$itemId=0;
        $validation_rule = [];
        $validation_rule['employee_id'] = ['required', 'alpha_dash'];
        $validation_rule['username'] = ['required', 'alpha_dash'];
        $validation_rule['password'] = ['required','min:4'];
        $validation_rule['user_group_id'] = ['required'];
        $validation_rule['name'] = ['required'];
        $validation_rule['email'] = ['required','email'];
        $validation_rule['mobile_no'] = ['required'];

        $itemNew =$request->input('item');

        $this->validateInputKeys($itemNew,array_keys($validation_rule));
        $this->validateInputValues($itemNew, $validation_rule);
        //checking super admin group
        if(($itemNew['user_group_id']==ID_USERGROUP_SUPERADMIN) && ($this->user->user_group_id!=ID_USERGROUP_SUPERADMIN)){
            return response()->json(['error'=>'VALIDATION_FAILED','messages'=> 'Invalid user group']);
        }
        //checking username exits
        $result = DB::table(TABLE_USERS)->where('username', $itemNew['username'])->first();
        if ($result) {
            return response()->json(['error'=>'VALIDATION_FAILED','messages'=> 'username exist']);
        }
        //hashing password
        $itemNew['password']=Hash::make($itemNew['password']);
        DB::beginTransaction();
        try{
            $dataHistory=[];
            $dataHistory['table_name']=TABLE_USER_HIDDEN_COLUMNS;
            $dataHistory['controller']=(new \ReflectionClass(__CLASS__))->getShortName();
            $dataHistory['method']=__FUNCTION__;


            $itemNew['created_by']=$this->user->id;
            $itemNew['created_at']=Carbon::now();
            $id = DB::table(TABLE_USERS)->insertGetId($itemNew);
            $itemNew['id']=$id;
            $dataHistory['table_id']=$id;
            $dataHistory['action']=DB_ACTION_ADD;

            $dataHistory['data_old']=json_encode([]);
            $dataHistory['data_new']=json_encode($itemNew);
            $dataHistory['created_at']=Carbon::now();
            $dataHistory['created_by']=$this->user->id;

            $this->dBSaveHistory($dataHistory,TABLE_SYSTEM_HISTORIES);
            $this->updateSaveToken();
            DB::commit();

            return response()->json(['error' => '','messages' =>'User( '.$itemNew['id'].' ) Created Successfully']);
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['error' => 'DB_SAVE_FAILED', 'messages'=>__('Failed to save.')]);
        }
    }
    private function saveOldItem(Request $request,$itemId): JsonResponse{
        return response()->json(['error'=>'ACCESS_DENIED','messages'=>$request->input('id')]);
    }
}
