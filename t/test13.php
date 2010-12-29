<?php
/**
 *  test13.php   - Test Logger class
 *      
 *      Test base methods of Logger class
 *
 *          This script testing general Logger posibility
 *              All main methods called from method ignore_some_level and class SomeClassNew
 *
 *          TEST place_regexp - option
 * 
 */

//  changed config add multiloging
require_once('common_test_code.php');

$count = 499;
$T->start( $count );

$config_original['appenders']['LOGGER']['log_format']   = "%p %d %m";
foreach( array( "/my_log_class/", "/^my_log_class/", "/my_log/", "/my.log.cl[aA]+s{2}/", "/^my_log_class(_suffix)?/", "/[^on]_log_class/" ) as $regexp ){
    $config_original['appenders']['LOGGER']['place_regexp']  = $regexp;

    Logger::get_logger()->set_config( $config_original );

    // init logger
    init( $config_original );
    general_test();

    $o1 = new my_log_class();
    $o1->test();

    $o1_a = new my_log_class_suffix();
    $o1_a->test();


    $o2 = new my_non_log_class();
    $o2->test();
}

class my_log_class 
{
    public function test(){
        global $T;

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
    }
}

class my_log_class_suffix 
{
    public function test(){
        global $T;

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
    }
}

class my_non_log_class 
{
    public function test(){
        global $T;

        $LOG = Logger::get_logger();

        $l = $LOG->debug( array('Anton Morozov', 'antonfin@mail.ua', 1985) )->get();
        $T->is( $l, '', "Must be empty" );

        $l = $LOG->info( array('Anton Morozov', 'antonfin@mail.ua', 1985) )->get();
        $T->is( $l, '', "Must be empty" );

        $l = $LOG->warn( "Warn Message" )->get();
        $T->is( $l, '', "Must be empty" );
    }
}


$T->can_ok( $class, 'destroy' );
Logger::get_logger()->destroy();


/*****************************************************************/
$config_original['appenders']['LOGGER']['log_format'] = "%p %t %m";
$qf = preg_quote(  __FILE__, '/' ); 

foreach ( array( "/$qf/", "/^$qf$/", "/^$qf/", "/$qf$/" ) as $regexp ){
    $config_original['appenders']['LOGGER']['place_regexp'] = $regexp;

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

    Logger::get_logger()->destroy();
}

/*****************************************************************/

$config_original['appenders']['LOGGER']['log_format'] = "%p %d (%t) %m";
'/' . 
$bad_fq = preg_quote( '/tmp' . __FILE__, '/' );
foreach ( array( "/$bad_fq/", "/^$bad_fq/", "/AAAAA$bad_fq/i" ) as $regexp ){
    $config_original['appenders']['LOGGER']['place_regexp'] = $regexp;
    print var_export( $config_original );
    Logger::get_logger()->set_config( $config_original );

    // init logger
    init( $config_original );
    general_test();

    $LOG = Logger::get_logger();

    $l = $LOG->debug( array('Anton Morozov', 'antonfin@mail.ua', 1985) )->get();

    $T->is( $l, '', "Must be empty" );

    $l = $LOG->info( array('Anton Morozov', 'antonfin@mail.ua', 1985) )->get();

    $T->is( $l, '', "Must be empty" );

    $l = $LOG->warn( "Warn Message" )->get();

    $T->is( $l, '', "Must be empty" );

    Logger::get_logger()->destroy();

}

/*****************************************************************/
$config_original['appenders']['LOGGER']['log_format'] = "%p %t %m";
$config_original['appenders']['LOGGER']['place_regexp']        = "/" . basename( __FILE__ ) . "$/";
print var_export( $config_original );

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

Logger::get_logger()->destroy();

/*****************************************************************/

$config_original['appenders']['LOGGER']['log_format'] = "%p %d (%t) %m";
$config_original['appenders']['LOGGER']['place_regexp']        = '/other_' . basename( __FILE__ ) . '/';
print var_export( $config_original );
Logger::get_logger()->set_config( $config_original );

// init logger
init( $config_original );
general_test();

$LOG = Logger::get_logger();

$l = $LOG->debug( array('Anton Morozov', 'antonfin@mail.ua', 1985) )->get();

$T->is( $l, '', "Must be empty" );

$l = $LOG->info( array('Anton Morozov', 'antonfin@mail.ua', 1985) )->get();

$T->is( $l, '', "Must be empty" );

$l = $LOG->warn( "Warn Message" )->get();

$T->is( $l, '', "Must be empty" );

Logger::get_logger()->destroy();

$T->finish();

