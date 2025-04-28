<?php

if (!defined('USER_DEFAULT_PASSWORD')) {
    define('USER_DEFAULT_PASSWORD', 'password');
}
if (!defined('USER_DEFAULT_STATUS')) {
    define('USER_DEFAULT_STATUS', 1);
}
if (!defined('USER_STATUS_VALUES')) {
    define('USER_STATUS_VALUES', [
        "Active" => 1,
        "Disabled" => 0,
        "Deleted" => -1
    ]);
}
