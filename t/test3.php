<?php
/**
 *  test3.php   - Test Logger class
 *      
 *      Test base methods of Logger class
 *
 *          This script testing:
 *              @info
 */

//  Load Logger and class for testing
require_once ('../lib/Logger.php');
require_once ('TestSimple.php');

$class      = 'Logger';
$version    = '0.01';

//  config
$config = array(
    'use'       => array('LOGGER'),
    'common'    => array(),
    'appenders' => array(
            'LOGGER'    => array( 'type' => 'stdout', 'log_format' => '%f:%m' )
        )
    );
$cur_appender = 'LOGGER';

$T = TestSimple::get_test_object(6) or die( "Can't create test object" );

$T->class_ok( $class );

//////////////////////  Classes' methods    //////////////////

//  Check get_version method

$T->can_ok( $class, 'info' );
//  need for testig
$T->can_ok( $class, 'get' );

$LOG = Logger::get_logger( $config );
//  testing log_format  "%f:%m"
$l = $LOG->info( null )->get( $cur_appender );
$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );
$T->is( $l, basename( __FILE__ ) . ":\$VAR1 = NULL\n", "Is \"" . basename(__FILE__) . ':$VAR1 = NULL"' );

$T->finish();
