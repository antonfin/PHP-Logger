<?php
/**
 *  common_test_code.php    - Test Logger class
 *      
 *      In this file main data for testing
 * 
 */

$config_original = array( 
    "use" => array("LOGGER"),
    "appenders" => array(
        "LOGGER" => array( "type" => "stdout", "log_format" => '' ),
        )
    );

$TESTS_MESSAGES = array(
    array( "",                                                  "\"\"" ),
    array( NULL,                                                "NULL" ),
    array( false,                                               "FALSE" ),
    array( true,                                                "TRUE" ),
    array( "Anton",                                             "Anton" ),
    array( 1,                                                   "1" ),
    array( 123.12,                                              "123.12" ),
    array( array( "Anton", "Morozov" ),                         "array (\n  0 => 'Anton',\n  1 => 'Morozov',\n)" ),
    array( array( 'email' => "antonfin@mail.ua", 'old' => 24 ), "array (\n  'email' => 'antonfin@mail.ua',\n  'old' => 24,\n)" ),
//    array( new TestClass(),                                     "TestClass::__set_state(array(\n  'pr1' => 1,\n  'pr2' => 'two',\n))" ),
);

$ADD_VAR = array( null, true, false );

$LEVEL_MESSAGE = array( null, 'debug', 'info', 'warn', 'error', 'fatal' );

require_once ('TestSimple.php');

// init test object
$T = TestSimple::get_test_object() or die( "Can't create test object" );
$T->fail_format = "test\t%N\t fail - %f:%L%nMessage:%n%m%n";
$T->start( 5 );

//  Load Logger and class for testing

$class = 'Logger';
require_once ('../lib/Logger.php');

/*
 *  void init( array $config )      - initialize logger
 *      @param  array   $config     - configuration parameters
 *      @return void
 */
function init( array $config ){         // 12 tests
    global $class, $T;
    try{

        $T->can_ok( $class, "get_logger" );

        $LOGGER = Logger::get_logger();

        $T->ok( $LOGGER, "Logger is undefined" );
        $T->isa_ok( $LOGGER, $class );
        $T->can_ok( $class, "set_config" );
        $T->can_ok( $LOGGER, "set_config" );

        check_main_log_methods( $class );

        $LOGGER->set_config( $config );

    }
    catch( Exception $e ){
        print "Exception: " . $e->getMessage();
    }

}

/*
 *  void general_test()         - test by exists main logger methods
 *      @return     void
 */
function general_test(){    // 9 tests
    global $class, $T;

    $L = Logger::get_logger();
    $T->ok( $L, "Logger is undefined" );
    $T->isa_ok( $L, $class );

    check_main_log_methods( $L );
    
}

//-----------------------------------       UTILS       ---------------------------------

/*
 *  void check_main_log_methods( mix $l ) - check methods existence in the class or object
 *      @param  mix $l      - it Logger class name or Logger object
 */
function check_main_log_methods( $l ){
    
    global $T;
    foreach( array("debug", "info", "warn", "error", "fatal", "log", "get" ) as $m ){
        $T->can_ok( $l, $m );
    }
    
}

//  class for test logger
class TestClass
{
    private $pr1 = 1;
    private $pr2 = "two";
    function __construct(){}
}
?>
