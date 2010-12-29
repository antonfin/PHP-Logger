<?php
/**
 *  test9.php   - Test Logger class
 *      
 *      Test base methods of Logger class
 *
 *          This script testing general Logger posibility
 *              All main methods called from method ignore_some_level and class SomeClassNew
 * 
 */

$LOG_FORMAT = array( 
    array( "%p %F:%M:%L - %m",     __FILE__ . ":ignore_some_level",                                 "" ), 
    array( "%p %f:%P %M:%L - %m",  basename( __FILE__ ) . ":" . getmypid() . " ignore_some_level",  "" ), 
    array( "%p %f %C:%M:%L - %m",  basename( __FILE__ ) . " SomeClassNew:ignore_some_level",        "" ), 
);

require_once('common_test_code.php');

$count = 1 + ( 12 + 9 + 10 * count( $TESTS_MESSAGES ) + 1 ) * count( $ADD_VAR ) * count( $LOG_FORMAT ) * count( $LEVEL_MESSAGE );
$T->start( $count );

$T->class_ok( $class );
//----------------------------------

foreach( $LEVEL_MESSAGE as $level ){

    $config = $config_original;
    if( ! is_null( $level ) ){ $config['appenders']['LOGGER']['min_log_level'] = $level; }

    foreach( $LOG_FORMAT as $log_format ){
        $start_m = $log_format[1];
        $end_m   = $log_format[2] . "\n";

        foreach( $ADD_VAR as $add_var ){

            $config['appenders']['LOGGER']['log_format'] = $log_format[0];

            $var_m   = '$VAR1 = ';

            if( ! is_null( $add_var ) ){ $config['appenders']['LOGGER']['add_var'] = $add_var; } 
            // init logger
            init( $config );
            general_test();

            foreach( $TESTS_MESSAGES as $msg ){

                if( isset( $config['appenders']['LOGGER']['add_var'] ) and ! $config['appenders']['LOGGER']['add_var'] ) 
                    $var_m = '';

                $message = $var_m . $msg[1] . $end_m;
                $obj = new SomeClassNew( array( "LOGGER" ), $start_m, $msg[0], $message );
                $obj->ignore_some_level( $level );
            }

            $T->can_ok( $class, 'destroy' );
            Logger::get_logger()->destroy();

        }
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

    public function ignore_some_level( $level, $add_indent = true ){

        $T = TestSimple::get_test_object();
        $L = Logger::get_logger();

        foreach( $this->apps as $app_name ){

            foreach( array("debug", "info", "warn", "error", "fatal" ) as $m ){
                $msg = "";
                if( self::$levels[ $level ] >= self::$levels[ $m ] ){ 
                    $msg = $add_indent ? 
                        "$m {$this->start_m}:" . (__LINE__ + 4) . " - {$this->message}" : 
                        $this->start_m . ":" . (__LINE__ + 3) . " - " . $this->message;
                }

                $lmes = $L->$m($this->log)->get( $app_name );
                $T->ok( $lmes ? $lmes : isset($lmes), "Message was not returned" );
                $T->is( $lmes, $msg, " Now:\n{$lmes} Must be:\n$msg" );
            }
        }
    } 
}

