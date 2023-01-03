<?php

if(isset($_SESSION)) {
    return;
}

if(file_exists(dirname(__FILE__) . '/sites/conf.php')){
    include_once (dirname(__FILE__) . '/sites/conf.php');
}


if(!defined('session_db') || session_db === false){
    session_cache_limiter('private');
    session_name('mdTimeLine');
    session_start();
    return;
}

try {
    include_once (dirname(__FILE__) . '/classes/PHPSessions.php');
    $handler = new \PHPSessions();
    $handler->setDbDetails(session_db_host, session_db_port,session_db_username, session_db_password, session_db_name, session_db_table);
    session_set_save_handler($handler, true);
    session_start();
} catch (\Exception $e){
    session_cache_limiter('private');
    session_name('mdTimeLine');
    session_start();
    error_log('ERROR CREATING DATABASE SESSION ENTRY');
    error_log($e->getMessage());
}

