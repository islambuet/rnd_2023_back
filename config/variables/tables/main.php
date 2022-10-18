<?php

$db_main=env('DB_DATABASE','rnd_2023');

//users
define('TABLE_USERS', $db_main.'.users');
define('TABLE_PRINCIPALS', $db_main.'.principals');
define('TABLE_COMPETITORS', $db_main.'.competitors');
define('TABLE_DESIGNATIONS', $db_main.'.designations');
define('TABLE_SEASONS', $db_main.'.seasons');


