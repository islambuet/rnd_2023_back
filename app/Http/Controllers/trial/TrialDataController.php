<?php
namespace App\Http\Controllers\trial;

use App\Http\Controllers\RootController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Helpers\TaskHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Illuminate\Validation\Rule;


class TrialDataController extends RootController
{
    public $api_url = 'trial/data';
    public $permissions;
    public $cropInfo;
    public $formInfo;

    public function __construct()
    {
        parent::__construct();
        $cropId=\Route::current()->parameter('cropId',0);
        $formId=\Route::current()->parameter('formId',0);
        $this->cropInfo = DB::table(TABLE_CROPS)->find($cropId);
        $this->formInfo = DB::table(TABLE_TRIAL_FORMS)->where('crop_id',$cropId)->find($formId);
        if($this->cropInfo && $this->formInfo){
            $this->permissions = TaskHelper::getPermissions($this->api_url, $this->user);
        }
        else{
            $this->permissions = TaskHelper::getAllPermissions(false);
        }
    }

    public function initialize(Request $request,$cropId,$formId): JsonResponse
    {
        if ($this->permissions->action_0 == 1) {
            $inputFields=DB::table(TABLE_TRIAL_FORM_INPUTS)
                ->where('trial_form_id', $formId)
                ->orderBy('ordering', 'ASC')
                ->orderBy('id', 'ASC')
                ->where('status', SYSTEM_STATUS_ACTIVE)
                ->get();

            $trial_stations=DB::table(TABLE_TRIAL_STATIONS)
                ->select('id', 'name')
                ->orderBy('ordering', 'ASC')
                ->where('status', SYSTEM_STATUS_ACTIVE)
                ->get();
            $seasons=DB::table(TABLE_SEASONS)
                ->select('id', 'name')
                ->orderBy('ordering', 'ASC')
                ->where('status', SYSTEM_STATUS_ACTIVE)
                ->get();

            return response()->json(
                ['error'=>'','permissions'=>$this->permissions,
                    'cropInfo'=>$this->cropInfo,
                    'formInfo'=>$this->formInfo,
                    'inputFields'=>$inputFields,
                    'trial_stations' => $trial_stations,
                    'seasons'=>$seasons
                ]);
        } else {
            return response()->json(['error' => 'ACCESS_DENIED', 'messages' => __('You do not have access on this page')]);
        }
    }

    public function getItems(Request $request, $cropId,$formId,$trialStationId, $year,$seasonId): JsonResponse
    {
        if ($this->permissions->action_0 == 1) {
            $results=DB::table(TABLE_TRIAL_DATA)
                ->where('trial_station_id',$trialStationId)
                ->where('year',$year)
                ->where('season_id',$seasonId)
                ->where('trial_form_id', $formId)
                ->select(DB::raw('GROUP_CONCAT(entry_no) as entries'))
                ->addSelect('variety_id')
                ->groupBy('variety_id')
                ->get();
            $trial_data=[];
            foreach ($results as $result){
                $trial_data[$result->variety_id]=$result;
            }

            $query=DB::table(TABLE_TRIAL_VARIETIES.' as trial_varieties');
            $query->select('trial_varieties.variety_id','trial_varieties.rnd_ordering','trial_varieties.rnd_code','trial_varieties.replica','trial_varieties.delivered_date','trial_varieties.sowing_date');
            $query->where('trial_varieties.trial_station_id',$trialStationId);
            $query->where('trial_varieties.year',$year);
            $query->where('trial_varieties.season_id', $seasonId);
            $query->join(TABLE_VARIETIES.' as varieties', 'varieties.id', '=', 'trial_varieties.variety_id');
            $query->addSelect('varieties.name as variety_name','varieties.crop_type_id');
            $query->join(TABLE_CROP_TYPES.' as crop_types', 'crop_types.id', '=', 'varieties.crop_type_id');
            $query->addSelect('crop_types.name as crop_type_name','crop_types.crop_id');
            $query->join(TABLE_CROPS.' as crops', 'crops.id', '=', 'crop_types.crop_id');
            $query->addSelect('crops.name as crop_name');
            $query->where('trial_varieties.delivery_status', SYSTEM_STATUS_YES);
            $query->where('trial_varieties.sowing_status', SYSTEM_STATUS_YES);
            $query->where('crops.id', $cropId);
            $results = $query->get();
            $items=[];
            foreach ($results as $result){
                if(isset($trial_data[$result->variety_id])){
                    $result->entries=explode(',',$trial_data[$result->variety_id]->entries);
                    $result->num_entry=count($result->entries);
                }
                else{
                    $result->entries=[];
                    $result->num_entry=0;
                }

                $items[]=$result;
            }
            return response()->json(['error'=>'','items'=> $items]);
        }
        else {
            return response()->json(['error' => 'ACCESS_DENIED', 'messages' => __('You do not have access on this page')]);
        }
    }
    public function saveItem(Request $request,$cropId,$formId): JsonResponse{
//        $year=$request->input('year');
//        $trial_station_id=$request->input('trial_station_id');
//        $season_id=$request->input('season_id');
//        $variety_id=$request->input('variety_id');
//        $entry_no=$request->input('entry_no');
//        $data_1=$request->input('item');
//        $data_2=$request->input('data_2');
        $time = Carbon::now();
        $itemNew=$request->input('item');
        $itemNew['trial_form_id']=$formId;
        $itemNew['created_by'] = $this->user->id;
        $itemNew['created_at'] = $time;
//        $itemNew['year']=$year;
//        $itemNew['trial_station_id']=$trial_station_id;
//        $itemNew['season_id']=$season_id;
//        $itemNew['variety_id']=$variety_id;
//        $itemNew['entry_no']=$entry_no;
        $itemNew['data_1']=json_encode($itemNew['data_1']);
        if(isset($itemNew['data_2'])){
            $itemNew['data_2']=json_encode($itemNew['data_2']);
        }
        DB::table(TABLE_TRIAL_DATA)->insertGetId($itemNew);

        return response()->json(['error' => '', 'messages' =>'Saved']);
    }
}

