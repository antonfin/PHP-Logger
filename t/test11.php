<?php
/**
 *  test11.php   - Test Logger class
 *      
 *      Test base methods of Logger class
 *
 *          This script testing general Logger posibility
 *              All main methods called from method ignore_some_level and class SomeClassNew
 * 
 */

//  changed config add multiloging
require_once('common_test_code.php');

$count = 93;
$T->start( $count );



$config_original['appenders']['LOGGER']['log_format'] = "%p %d %m";

Logger::get_logger()->set_config( $config_original );

// init logger
init( $config_original );
general_test();


$LOG = Logger::get_logger();

$l = $LOG->debug( array('Anton Morozov', 'antonfin@mail.ua', 1985) )->get();

$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );
$T->ok( preg_match( "/^debug 20\d{2}\/\d{2}\/\d{2} \d{2}:\d{2}:\d{2} /", $l ), "Hasn't data" );

$l = $LOG->info( array('Anton Morozov', 'antonfin@mail.ua', 1985) )->get();

$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );
$T->ok( preg_match( "/^info 20\d{2}\/\d{2}\/\d{2} \d{2}:\d{2}:\d{2} /", $l ), "Hasn't data" );

$l = $LOG->warn( "Warn Message" )->get();

$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );
$T->ok( preg_match( "/^warn 20\d{2}\/\d{2}\/\d{2} \d{2}:\d{2}:\d{2} /", $l ), "Hasn't data" );

$T->can_ok( $class, 'destroy' );
Logger::get_logger()->destroy();

/*****************************************************************/

$config_original['appenders']['LOGGER']['log_format'] = "%p %t %m";

Logger::get_logger()->set_config( $config_original );

// init logger
init( $config_original );
general_test();

$LOG = Logger::get_logger();

$l = $LOG->debug( array('Anton Morozov', 'antonfin@mail.ua', 1985) )->get();

$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );
$T->ok( preg_match( "/^debug \d{2}\.\d{4} /", $l ), "Hasn't data" );

$l = $LOG->info( array('Anton Morozov', 'antonfin@mail.ua', 1985) )->get();

$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );
$T->ok( preg_match( "/^info \d{2}\.\d{4} /", $l ), "Hasn't data" );

$l = $LOG->warn( "Warn Message" )->get();

$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );
$T->ok( preg_match( "/^warn \d{2}\.\d{4} /", $l ), "Hasn't data" );

$T->can_ok( $class, 'destroy' );
Logger::get_logger()->destroy();

/*****************************************************************/

$config_original['appenders']['LOGGER']['log_format'] = "%p %d (%t) %m";

Logger::get_logger()->set_config( $config_original );

// init logger
init( $config_original );
general_test();

$LOG = Logger::get_logger();

$l = $LOG->debug( array('Anton Morozov', 'antonfin@mail.ua', 1985) )->get();

$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );
$T->ok( preg_match( "/^debug 20\d{2}\/\d{2}\/\d{2} \d{2}:\d{2}:\d{2} \(\d{2}\.\d{4}\) /", $l ), "Hasn't data" );

$l = $LOG->info( array('Anton Morozov', 'antonfin@mail.ua', 1985) )->get();

$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );
$T->ok( preg_match( "/^info 20\d{2}\/\d{2}\/\d{2} \d{2}:\d{2}:\d{2} \(\d{2}\.\d{4}\) /", $l ), "Hasn't data" );

$l = $LOG->warn( "Warn Message" )->get();

$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );
$T->ok( preg_match( "/^warn 20\d{2}\/\d{2}\/\d{2} \d{2}:\d{2}:\d{2} \(\d{2}\.\d{4}\) /", $l ), "Hasn't data" );

$T->can_ok( $class, 'destroy' );
Logger::get_logger()->destroy();

$T->finish();
