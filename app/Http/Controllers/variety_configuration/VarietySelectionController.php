<?php
namespace App\Http\Controllers\variety_configuration;

use App\Http\Controllers\RootController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Helpers\CommonHelper;
use App\Helpers\TaskHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Illuminate\Validation\Rule;


class VarietySelectionController extends RootController
{
    public $api_url = 'variety-configuration/selection';
    public $permissions;
    public $cropInfo;

    public function __construct()
    {
        parent::__construct();
        $cropId = \Route::current()->parameter('cropId', 0);
        $this->cropInfo = DB::table(TABLE_CROPS)->find($cropId);
        if ($this->cropInfo) {
            $this->permissions = TaskHelper::getPermissions($this->api_url, $this->user);
        } else {
            $this->permissions = TaskHelper::getAllPermissions(false);
        }
    }

    public function initialize(Request $request, $cropId, $year): JsonResponse
    {
        if ($this->permissions->action_0 == 1) {
            $crop_types = DB::table(TABLE_CROP_TYPES)
                ->select('id', 'name')
                ->orderBy('ordering', 'ASC')
                ->where('status', SYSTEM_STATUS_ACTIVE)
                ->where('crop_id', $cropId)
                ->get();

            return response()->json(
                ['error' => '', 'permissions' => $this->permissions,
                    'hidden_columns' => TaskHelper::getHiddenColumns($this->api_url, $this->user,),
                    'cropInfo' => $this->cropInfo,
                    'crop_types' => $crop_types,
                ]);
        } else {
            return response()->json(['error' => 'ACCESS_DENIED', 'messages' => __('You do not have access on this page')]);
        }
    }
    public function getItems(Request $request, $cropId, $year): JsonResponse
    {
        if ($this->permissions->action_0 == 1) {
            $results=DB::table(TABLE_PRINCIPALS)->get();
            $principals=[];
            foreach ($results as $result){
                $principals[$result->id]=$result;
            }
            $results=DB::table(TABLE_COMPETITORS)->get();
            $competitors=[];
            foreach ($results as $result){
                $competitors[$result->id]=$result;
            }

            $varieties=DB::table(TABLE_VARIETIES.' as varieties')
                ->select('varieties.*')
                ->join(TABLE_CROP_TYPES.' as crop_types', 'crop_types.id', '=', 'varieties.crop_type_id')
                ->addSelect('crop_types.name as crop_type_name','crop_types.code as crop_type_code')
                ->leftJoin(TABLE_SELECTED_VARIETIES.' as selected_varieties',function($join) use($year){
                     $join->on('selected_varieties.variety_id', '=', 'varieties.id')
                         ->on('selected_varieties.year',DB::raw($year));
                })
                ->addSelect('selected_varieties.rnd_ordering','selected_varieties.season_ids','selected_varieties.created_at','selected_varieties.updated_at')
                ->orderBy('selected_varieties.rnd_ordering', 'DESC')
                ->orderBy('varieties.id', 'DESC')
                ->where('varieties.status', SYSTEM_STATUS_ACTIVE)
                ->where('crop_types.crop_id', $cropId)
                ->get();

            $items=[];
            foreach ($varieties as $variety){
                $variety->crop_code=$this->cropInfo->code;
                $variety->principal_info=$variety->principal_id>0?$principals[$variety->principal_id]:[];
                $variety->competitor_info=$variety->competitor_id>0?$competitors[$variety->competitor_id]:[];
                $variety->rnd_code=CommonHelper::get_rnd_code($variety,$year,true);
                $variety->num_seasons=substr_count($variety->season_ids,',');
                if($variety->num_seasons>0){
                    $variety->num_seasons--;
                }
                $items[]=$variety;
            }
            return response()->json(['error'=>'','items'=> ['data'=>$items]]);
        } else {
            return response()->json(['error' => 'ACCESS_DENIED', 'messages' => __('You do not have access on this page')]);
        }
    }
    public function getItem(Request $request, $cropId, $year, $itemId): JsonResponse
    {
        //$itemId==varietyid
        if ($this->permissions->action_0 == 1) {
            $query=DB::table(TABLE_VARIETIES.' as varieties');
            $query->select('varieties.*');
            $query->join(TABLE_CROP_TYPES.' as crop_types', 'crop_types.id', '=', 'varieties.crop_type_id');
            $query->addSelect('crop_types.name as crop_type_name');
            $query->where('varieties.id','=',$itemId);
            $query->leftJoin(TABLE_PRINCIPALS.' as principals', 'principals.id', '=', 'varieties.principal_id');
            $query->addSelect('principals.name as principal_name');
            $query->leftJoin(TABLE_COMPETITORS.' as competitors', 'competitors.id', '=', 'varieties.competitor_id');
            $query->addSelect('competitors.name as competitor_name');
            $result = $query->first();
            if (!$result) {
                return response()->json(['error' => 'ITEM_NOT_FOUND', 'messages' => __('Invalid Id ' . $itemId)]);
            }
            return response()->json(['error'=>'','item'=>$result]);
        } else {
            return response()->json(['error' => 'ACCESS_DENIED', 'messages' => $this->permissions]);
        }
    }
}



