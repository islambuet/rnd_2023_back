<?php
namespace App\Helpers;

use Illuminate\Support\Facades\DB;
class TaskHelper
{
    public static $MAX_MODULE_ACTIONS = 9;
    public static $TRAIL_FORM_SETUP_ID = 17;
    public static $TRAIL_DATA_ID = 18;
    public static $VARIETY_SELECTION_ID = 23;

    public static function getUserGroupRole($user_group_id): object
    {
        $role = (object)[];
        $query = DB::table(TABLE_USER_GROUPS);
        $query->where('id', $user_group_id);
        for ($i = 0; $i < self::$MAX_MODULE_ACTIONS; $i++) {
            $query->addselect('action_' . $i);
            $role->{'action_' . $i} = ',';
        }
        $userGroup = $query->first();
        if ($userGroup) {
            for ($i = 0; $i < self::$MAX_MODULE_ACTIONS; $i++) {
                $role->{'action_' . $i} = $userGroup->{'action_' . $i};
            }
        }
        return $role;
    }
    public static function getPermissions($url, $user): object
    {
        $permissions = (object)[];
        $task = DB::table(TABLE_TASKS)->where('url', $url)->select('id')->first();
        $taskId = $task ? $task->id : 0;
        for ($i = 0; $i < self::$MAX_MODULE_ACTIONS; $i++) {
            if ($user && (strpos($user->userGroupRole->{'action_' . $i}, ',' . $taskId . ',') !== false)) {
                $permissions->{'action_' . $i} = 1;
            } else {
                $permissions->{'action_' . $i} = 0;
            }
        }
        return $permissions;
    }
    public static function getAllPermissions($access){
        $permissions = (object)[];
        for ($i = 0; $i < self::$MAX_MODULE_ACTIONS; $i++) {
            if ($access) {
                $permissions->{'action_' . $i} = 1;
            } else {
                $permissions->{'action_' . $i} = 0;
            }
        }
        return $permissions;
    }
    public static function getUserGroupTasks($userGroupRole): array
    {
        $crops=DB::table(TABLE_CROPS)
            ->select('id', 'name','ordering','status')
            ->orderBy('ordering', 'ASC')
            ->orderBy('id', 'ASC')
            ->where('status', SYSTEM_STATUS_ACTIVE)
            ->get();
        $results=DB::table(TABLE_TRIAL_FORMS)
            ->select('id', 'name','crop_id','ordering','status')
            ->orderBy('ordering', 'ASC')
            ->orderBy('id', 'ASC')
            ->where('status', SYSTEM_STATUS_ACTIVE)
            ->get();
        $forms=[];
        foreach ($results as $result){
            $forms[$result->crop_id][]=$result;
        }
        $role = [];
        if (strlen($userGroupRole->action_0) > 1) {
            $role = explode(',', trim($userGroupRole->action_0, ','));
        }
        $tasks = DB::table(TABLE_TASKS)
            ->select('id', 'name', 'type', 'parent', 'url', 'ordering', 'status')
            ->orderBy('ordering', 'ASC')
            ->where('status', SYSTEM_STATUS_ACTIVE)
            ->get();
        $children = [];
        foreach ($tasks as $task) {
            if ($task->type == 'TASK') {
                if (in_array($task->id, $role)) {
                    if(($task->id==Self::$TRAIL_FORM_SETUP_ID) ||($task->id==Self::$VARIETY_SELECTION_ID)){
                        $task->type='MODULE';
                        $children[$task->parent][$task->id] = $task;
                        foreach ($crops as $crop){
                            $subtask=(object)[
                                'id'=>$task->id.'_'.$crop->id,
                                'name'=>$crop->name,
                                'type'=>'TASK',
                                'parent'=>$task->id,
                                'url'=>$task->url.'/'.$crop->id,
                                'ordering'=>$crop->ordering,
                                'status'=>$crop->status
                            ];
                            $children[$subtask->parent][$subtask->id] = $subtask;
                        }
                    }

                    else if($task->id==Self::$TRAIL_DATA_ID){
                        $task->type='MODULE';
                        $children[$task->parent][$task->id] = $task;
                        foreach ($crops as $crop){
                            $subModule=(object)[
                                'id'=>$task->id.'_'.$crop->id,
                                'name'=>$crop->name,
                                'type'=>'MODULE',
                                'parent'=>$task->id,
                                'url'=>'',
                                'ordering'=>$crop->ordering,
                                'status'=>$crop->status
                            ];
                            $children[$subModule->parent][$subModule->id] = $subModule;
                            if(isset($forms[$crop->id])){
                                foreach ($forms[$crop->id] as $form){
                                    $subtask=(object)[
                                        'id'=>$subModule->id.'_'.$form->id,
                                        'name'=>$form->name,
                                        'type'=>'TASK',
                                        'parent'=>$subModule->id,
                                        'url'=>$task->url.'/'.$crop->id.'/'.$form->id,
                                        'ordering'=>$form->ordering,
                                        'status'=>$form->status
                                    ];
                                    $children[$subtask->parent][$subtask->id] = $subtask;
                                }
                            }

                        }
                    }
                    else{
                        $children[$task->parent][$task->id] = $task;
                    }
                }
            }
            else {
                $children[$task->parent][$task->id] = $task;
            }
        }
        $tree = [];
        $max_level = 0;

        if (isset($children[0])) {
            $tree = self::getUserGroupSubTasks(1, $max_level, '', '', $children, $children[0]);
        }
        return ['max_level' => $max_level, 'tasksTree' => $tree];
    }
    //$list=children=full lists, $parent =current level items
    public static function getUserGroupSubTasks($level, &$max_level, $parent_class, $prefix, $list, $parent): array
    {
        $tree = [];
        foreach ($parent as $element) {
            $element->level = $level;
            $element->parent_class = $parent_class;
            $element->prefix = $prefix;
            if (isset($list[$element->id])) {
                $children = self::getUserGroupSubTasks($level + 1, $max_level, $parent_class . ' parent_' . $element->id, $prefix . '- ', $list, $list[$element->id]);
                if ($children) {
                    $element->children = $children;
                    $tree[] = $element;
                }
            } else {
                if ($element->type == 'TASK') {
                    $tree[] = $element;
                    if ($level > $max_level) {
                        $max_level = $level;
                    }
                }
            }
        }
        return $tree;
    }
    public static function getHiddenColumns($url, $user, $method = 'list')//forApi
    {
        $hidden_columns = [];
        $result = DB::table(TABLE_USER_HIDDEN_COLUMNS . ' as hc')
            ->join(TABLE_TASKS . ' as task', 'task.url', '=', 'hc.url')
            ->select('hc.hidden_columns')
            ->where('task.url', $url)
            ->where('hc.method', $method)
            ->where('hc.user_id', $user->id)
            ->first();
        if ($result) {
            $hidden_columns = json_decode($result->hidden_columns);
        }
        return $hidden_columns;
    }
    public static function getTasksTree(): array
    {
        $tasks = DB::table(TABLE_TASKS)
            ->select('id', 'name', 'type', 'parent', 'url', 'ordering', 'status')
            ->orderBy('ordering', 'ASC')
            ->where('status','!=', SYSTEM_STATUS_DELETE)
            ->get();
        $children = [];
        foreach ($tasks as $task) {
            $children[$task->parent][$task->id] = $task;
        }
        $tree = [];
        $max_level = 0;

        if (isset($children[0])) {
            $tree = self::getSubTasks(1, $max_level, '', '', $children, $children[0]);
        }
        return ['max_level' => $max_level, 'tasksTree' => $tree];
    }
    //$list=children=full lists, $parent =current level items
    public static function getSubTasks($level, &$max_level, $parent_class, $prefix, $list, $parent): array
    {
        $tree = [];
        foreach ($parent as $element) {
            $element->level = $level;
            $element->parent_class = $parent_class;
            $element->prefix = $prefix;
            if (isset($list[$element->id])) {
                $children = self::getSubTasks($level + 1, $max_level, $parent_class . ' parent_' . $element->id, $prefix . '- ', $list, $list[$element->id]);
                if ($children) {
                    $element->children = $children;
                    $tree[] = $element;
                }
            } else {
                $tree[] = $element;
                if ($level > $max_level) {
                    $max_level = $level;
                }
            }
        }
        return $tree;
    }
}
