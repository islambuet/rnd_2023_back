<?php
namespace App\Helpers;

use Illuminate\Support\Facades\DB;
class TaskHelper
{
    public static $MAX_MODULE_ACTIONS = 7;
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
    public static function getPermissions($url, $userGroupRole): object
    {
        $permissions = (object)[];
        $task = DB::table(TABLE_TASKS)->where('url', $url)->select('id')->first();
        $taskId = $task ? $task->id : 0;
        for ($i = 0; $i < self::$MAX_MODULE_ACTIONS; $i++) {
            if (strpos($userGroupRole->{'action_' . $i}, ',' . $taskId . ',') !== false) {
                $permissions->{'action_' . $i} = 1;
            } else {
                $permissions->{'action_' . $i} = 0;
            }
        }
        return $permissions;
    }
    public static function getUserGroupTasks($userGroupRole): array
    {
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
                    $children[$task->parent][$task->id] = $task;
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
}
