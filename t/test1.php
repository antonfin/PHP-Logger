<?php
/**
 *  test1.php   - Test Logger class
 *      
 *      Test base methods of Logger class
 *
 *          This script testing:
 *              @var        version
 *              @method     get_version
 *              @method     get_logger
 *              @method     set_config
 */

//  Load Logger and class for testing
require_once ('../lib/Logger.php');
require_once ('TestSimple.php');

$class      = 'Logger';
$version    = '0.90_32b';

//  config
$config = array(
    'use'       => array('LOGGER'),
    'common'    => array( 'space' => 4 ),
    'appenders' => array(
            'LOGGER'    => array( 'type' => 'stdout' )
        )
    );

//  config
$config2 = array(
    'use'       => array( 'LOGGER1', 'LOGGER2' ),
    'common'    => array( 'add_var' => false ),
    'appenders' => array(
            'LOGGER1'   => array( 'type' => 'stdout', 'format' => '%f:%L:%m' ),
            'LOGGER2'   => array( 'type' => 'file', 'filepath' => '/home/anton/tmp/php.test.log' ),
            'LOGGER3'   => array( 'type' => 'send' )
        )
    );


//////////////////////////////////////////////////////////////

$T = TestSimple::get_test_object( 29 ) or die( "Can't create test object" );
$T->class_ok( $class );

//////////////////////  Classes' methods    //////////////////

//  Check get_version method

$T->can_ok( $class, 'get_version' );
$Version = Logger::get_version();
$T->is( $Version, $version,         "Bad version 1" );
$T->is( Logger::$version, $version, "Bad version 2" );
$T->is( $Version, Logger::$version, "Bad version 3" );

//  Check get_logger method

$T->can_ok( $class, 'get_logger' );
$LOG1 = Logger::get_logger();
$T->isa_ok( $LOG1, $class );

$LOG2 = Logger::get_logger( $config );
$T->isa_ok( $LOG2, $class );

$T->is( $LOG1, $LOG2, "check singlton 0" );

$LOG3 = Logger::get_logger();
$T->isa_ok( $LOG3, $class );

$T->is( $LOG1, $LOG2, "check singlton 1" );
$T->is( $LOG1, $LOG3, "check singlton 2" );
$T->is( $LOG2, $LOG3, "check singlton 3" );

////////////////////////  Object's Methods  //////////////////

//  check set_config and appender_list methods
$T->can_ok( $class, 'set_config' );
$T->can_ok( $LOG1, 'set_config' );
$T->can_ok( $LOG2, 'set_config' );
$T->can_ok( $LOG3, 'set_config' );

$T->is( $LOG1, $LOG2, "check singlton 4" );
$T->is( $LOG1, $LOG3, "check singlton 5" );
$T->is( $LOG2, $LOG3, "check singlton 6" );

$appender_list = $LOG1->appender_list();

$T->is( $appender_list, array_values( $config['use'] ), "check appender_list && set_config - 1" );

$LOG1->set_config( $config2 );

$T->is( $LOG1, $LOG2, "check singlton 4" );
$T->is( $LOG1, $LOG3, "check singlton 5" );
$T->is( $LOG2, $LOG3, "check singlton 6" );

$appender_list2 = $LOG1->appender_list();

$T->isnt( $appender_list, $appender_list2, "check appender_list && set_config - 2" );
$T->is( $appender_list2, array_values( $config2['use'] ), "check appender_list && set_config - 3" );

$T->is( $LOG1, $LOG2, "check singlton 7" );
$T->is( $LOG1, $LOG3, "check singlton 8" );
$T->is( $LOG2, $LOG3, "check singlton 9" );

$LOG1->set_config( $config );

$T->finish();

?>
