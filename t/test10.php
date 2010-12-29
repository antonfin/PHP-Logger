<?php
/**
 *  test10.php   - Test Logger class
 *      
 *      Test base methods of Logger class
 *
 *          This script testing general Logger posibility
 *              All main methods called from method ignore_some_level and class SomeClassNew
 * 
 */

$LOG_FORMAT = array( 
    array( "%p %F:%M:%L - %m",     __FILE__ . ":smart_level_analizer",                                 "" ), 
    array( "%p %f:%P %M:%L - %m",  basename( __FILE__ ) . ":" . getmypid() . " smart_level_analizer",  "" ), 
    array( "%p %f %C:%M:%L - %m",  basename( __FILE__ ) . " SomeClassNew:smart_level_analizer",        "" ), 
);

//  changed config add multiloging
require_once('common_test_code.php');

$config_original["use"] = array("LOGGER","LOGGER1");
$config_original["appenders"]["LOGGER1"] = array("type" => "stdout", "log_format" => '', 'min_log_level' => 'error');

Logger::get_logger()->set_config( $config_original );
$count = 1 + ( 12 + 9 + 10 * count( $TESTS_MESSAGES ) * count($config_original["use"]) + 1 ) * count( $LOG_FORMAT ) * count( $LEVEL_MESSAGE );
$T->start( $count );

$T->class_ok( $class );

foreach( $LEVEL_MESSAGE as $level ){

    $config = $config_original;
    if( ! is_null( $level ) ){ 
        $config['appenders']['LOGGER']['min_log_level'] = $level; 
    }

    foreach( $LOG_FORMAT as $log_format ){
        $start_m = $log_format[1];
        $end_m   = $log_format[2] . "\n";

        $var_m = '$VAR1 = ';

        $config['appenders']['LOGGER']['log_format'] = $log_format[0];
        $config['appenders']['LOGGER1']['log_format'] = $log_format[0];

        // init logger
        init( $config );
        general_test();

        foreach( $TESTS_MESSAGES as $msg ){

            $message = $var_m . $msg[1] . $end_m;
            $obj = new SomeClassNew( $config['use'], $start_m, $msg[0], $message );
            $obj->smart_level_analizer( $level );
        }

        $T->can_ok( $class, 'destroy' );
        Logger::get_logger()->destroy();

    }
}
//--------------------------------

//  End of test
$T->finish();

/*
 *  Class for testing Logger
 */
class SomeClassNew
{
    public $apps; 
    public $log; 
    public $start_m;
    public $message;
    const debug = 4;
    const info  = 3;
    const warn  = 2;
    const error = 1;
    const fatal = 0;

    private static $levels = array(
        null    => self::debug,
        "debug" => self::debug, 
        "info"  => self::info,
        "warn"  => self::warn, 
        "error" => self::error, 
        "fatal" => self::fatal,
        );

    function __construct( $apps, $start_m, $log, $message ){
        $this->apps     = $apps;
        $this->start_m  = $start_m;
        $this->log      = $log;
        $this->message  = $message;
    } 

    public function smart_level_analizer( $level, $add_indent = true ){
        global $config_original;
        $T = TestSimple::get_test_object();
        $L = Logger::get_logger();

        foreach( array("debug", "info", "warn", "error", "fatal" ) as $m ){
            
            $L->$m($this->log);

            foreach( $this->apps as $app_name ){
                $msg = "";
                $lmes = $L->get( $app_name );
                $_level = $app_name != 'LOGGER1' ? $level : 'error';

                if( self::$levels[ $_level ] >= self::$levels[ $m ] ){ 
                    $msg = $add_indent ? 
                        "$m {$this->start_m}:" . 105 . " - {$this->message}" : 
                        $this->start_m . ":" . 105 . " - " . $this->message;
                }
                $T->ok( $lmes ? $lmes : isset($lmes), "Message was not returned" );
                $T->is( $lmes, $msg, " Now:\n{$lmes} Must be:\n$msg" );
            }
        }
    }
}

