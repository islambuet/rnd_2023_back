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
            $query->addSelect('crops.name as crop_name','crops.replica');
            $query->where('trial_varieties.delivery_status', SYSTEM_STATUS_YES);
            $query->where('trial_varieties.sowing_status', SYSTEM_STATUS_YES);
            $query->where('crops.id', $cropId);
            $results = $query->get();
            $items=[];
            foreach ($results as $result){
                $result->num_data=rand(0,5);
                $items[]=$result;
            }
            return response()->json(['error'=>'','items'=> $items]);
        }
        else {
            return response()->json(['error' => 'ACCESS_DENIED', 'messages' => __('You do not have access on this page')]);
        }
    }
}

