<?php

$db_main=env('DB_DATABASE','rnd_2023');

//users
define('TABLE_USERS', $db_main.'.users');
define('TABLE_PRINCIPALS', $db_main.'.principals');
define('TABLE_COMPETITORS', $db_main.'.competitors');
define('TABLE_DESIGNATIONS', $db_main.'.designations');
define('TABLE_SEASONS', $db_main.'.seasons');
define('TABLE_CROPS', $db_main.'.crops');
define('TABLE_CROP_TYPES', $db_main.'.crop_types');
define('TABLE_VARIETIES', $db_main.'.varieties');
define('TABLE_TRIAL_FORMS', $db_main.'.trial_forms');
define('TABLE_TRIAL_FORM_INPUTS', $db_main.'.trial_form_inputs');
define('TABLE_TRIAL_DATA', $db_main.'.trial_data');
define('TABLE_TRIAL_REPORTS', $db_main.'.trial_reports');

