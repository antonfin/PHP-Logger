<?php
/**
 *  test6.php   - Test Logger class
 *      
 *      Test base methods of Logger class
 *
 *          This script testing general Logger posibility
 *              all main logs called from class SomeClass and method main_method
 * 
 */

$LOG_FORMAT = array( 
    array( "",              "",                                                         "" ),
    array( "%m",            "",                                                         "" ),
    array( "%f:%m",         basename( __FILE__ ) . ":",                                 "" ),
    array( "%f - %m",       basename( __FILE__ ) . " - ",                               "" ),
    array( "%F => %m",      __FILE__ . " => ",                                          "" ), 
    array( "%F:%M - %m",    __FILE__ . ":main_method - ",                               "" ), 
    array( "%f:%P %M - %m", basename( __FILE__ ) . ":" . getmypid() . " main_method - ","" ), 
    array( "%f %C:%M - %m", basename( __FILE__ ) . " SomeClass:main_method - ",         "" ), 
    array( "Bla-bla: %m",       "Bla-bla: ",                                            "" ), 
    array( "Bla-bla - %% - %m", "Bla-bla - % - ",                                       "" ), 
    array( "%C:%M - %m",        "SomeClass:main_method - ",                             "" ), 
);

require_once('common_test_code.php');

$count = 1 + ( 12 + 9 + 10 * count( $TESTS_MESSAGES ) + 1 ) * count( $ADD_VAR ) * count( $LOG_FORMAT );
$T->start( $count );

$T->class_ok( $class );
//----------------------------------

foreach( $LOG_FORMAT as $log_format ){
    $start_m = $log_format[1];
    $end_m   = $log_format[2] . "\n";

    foreach( $ADD_VAR as $add_var ){

        $config = $config_original;
        $config['appenders']['LOGGER']['log_format'] = $log_format[0];

        $var_m   = '$VAR1 = ';

        if( ! is_null( $add_var ) ){ $config['appenders']['LOGGER']['add_var'] = $add_var; } 
        // init logger
        init( $config );
        general_test();

        foreach( $TESTS_MESSAGES as $msg ){

            if( isset( $config['appenders']['LOGGER']['add_var'] ) and ! $config['appenders']['LOGGER']['add_var'] ) 
                $var_m = '';

            if( preg_match("/%m/", $config['appenders']['LOGGER']['log_format'] ) ){
                $message = $start_m . $var_m . $msg[1] . $end_m;
            }
            else{
                $message = $start_m;        // TODO not right!!!    must be $start_m . $end_m;
            }

            $obj = new SomeClass( array( "LOGGER" ), $msg[0], $message );
            $obj->main_method();
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
class SomeClass
{
    public $apps; 
    public $log; 
    public $message;

    function __construct( $apps, $log, $message ){
        $this->apps     = $apps;
        $this->log      = $log;
        $this->message  = $message;
    } 

    public function main_method(){

        $T = TestSimple::get_test_object();
        $L = Logger::get_logger();

        foreach( $this->apps as $app_name ){

            foreach( array("debug", "info", "warn", "error", "fatal" ) as $m ){
                $lmes = $L->$m($this->log)->get( $app_name );
                $T->ok( $lmes ? $lmes : isset($lmes), "Message was not returned" );
                $T->is( $lmes, $this->message, " Now:\n{$lmes} Must be:\n{$this->message}" );
            }
        }
    } 
}

