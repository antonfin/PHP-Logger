<?php
/**
 *  test4.php   - Test Logger class
 *      
 *      Test base methods of Logger class
 *
 *          This script testing Exceptions
 * 
 */

//  Load Logger and class for testing
require_once ('../lib/Logger.php');
require_once ('TestSimple.php');

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
            'LOGGER1'   => array( 'type' => 'stdout', 'log_format' => '%f:%L:%m' ),
            'LOGGER2'   => array( 'type' => 'file', 'filepath' => '/home/anton/tmp/php.test.log' ),
            'LOGGER3'   => array( 'type' => 'send' )
        )
    );
$class = 'Logger';

$T = TestSimple::get_test_object( 25 ) or die( "Can't create test object" );
$T->class_ok( $class );

try{
    $LOG = Logger::get_logger( $config );
    $T->ok( $LOG, "Var Log exists" );
    $T->isa_ok( $LOG, $class );
    $LOG->destroy();
}
catch( Exception $e ){
    $msg = $e->getMessage();
}


try{
    $LOG = Logger::get_logger( $config2 );
    $T->ok( $LOG, "Var Log exists" );
    $T->isa_ok( $LOG, $class );
    $LOG->destroy();
}
catch( Exception $e ){
    $msg = $e->getMessage();
}

#----------         error configs
// common isn't array
$e_config1 = array(
    'use'       => array('LOGGER'),
    'common'    => '',
    'appenders' => array(
            'LOGGER'    => array( 'type' => 'stdout' )
        )
    );
// 'use' doesn't exists
$e_config2 = array(
    'common'    => array( 'add_var' => false ),
    'appenders' => array(
            'LOGGER1'   => array( 'type' => 'stdout', 'log_format' => '%f:%L:%m' ),
            'LOGGER2'   => array( 'type' => 'file', 'filepath' => '/home/anton/tmp/php.test.log' ),
            'LOGGER3'   => array( 'type' => 'send' )
        )
    );

// 'appenders' doesn't exist
$e_config3 = array(
    'use'       => array('LOGGER'),
    'common'    => array( 'add_var' => false ),
    );

// no use's appender in the appenders
$e_config4 = array(
    'use'       => array('LOGGER'),
    'common'    => array( 'add_var' => false ),
    'appenders' => array(
            'LOGGER1'   => array( 'type' => 'stdout', 'log_format' => '%f:%L:%m' ),
            'LOGGER2'   => array( 'type' => 'file', 'filepath' => '/home/anton/tmp/php.test.log' ),
            'LOGGER3'   => array( 'type' => 'send' )
        )
    );

// no use's appender in the appenders
$e_config5 = array(
    'use'       => array('LOGGER', 'LOGGER4'),
    'common'    => array( 'add_var' => false ),
    'appenders' => array(
            'LOGGER1'   => array( 'type' => 'stdout', 'log_format' => '%f:%L:%m' ),
            'LOGGER2'   => array( 'type' => 'file', 'filepath' => '/home/anton/tmp/php.test.log' ),
            'LOGGER3'   => array( 'type' => 'send' )
        )
    );

// file doesn't exist
$e_config6 = array(
    'use'       => array('LOGGER2', 'LOGGER1' ),
    'common'    => array( 'add_var' => false ),
    'appenders' => array(
            'LOGGER1'   => array( 'type' => 'stdout', 'log_format' => '%f:%L:%m' ),
            'LOGGER2'   => array( 'type' => 'file', 'filepath' => '/var/log/tmp/php.test.log' ),
            'LOGGER3'   => array( 'type' => 'send' )
        )
    );




try{
    $LOG = Logger::get_logger( $e_config1 );
    $LOG->destroy();
}
catch( Exception $e ){
    $msg = $e->getMessage();
    $T->ok( $msg, 'message exists 1' );
    $T->ok( is_string($msg), 'message exists 1' );
    // print "$e\n";
}

try{
    $LOG = Logger::get_logger( $e_config2 );
    $LOG->destroy();
}
catch( Exception $e ){
    $msg = $e->getMessage();
    $T->ok( $msg, 'message exists 2' );
    $T->ok( is_string($msg), 'message exists 2' );
    //print "$e\n";
}

try{
    $LOG = Logger::get_logger( $e_config3 );
    $LOG->destroy();
}
catch( Exception $e ){
    $msg = $e->getMessage();
    $T->ok( $msg, 'message exists 2' );
    $T->ok( is_string($msg), 'message exists 2' );
    //print "$e\n";
}

try{
    $LOG = Logger::get_logger( $e_config4 );
    $LOG->destroy();
}
catch( Exception $e ){
    $msg = $e->getMessage();
    $T->ok( $msg, 'message exists 3' );
    $T->ok( is_string($msg), 'message exists 3' );
    //print "$e\n";
}

try{
    $LOG = Logger::get_logger( $e_config5 );
    $LOG->destroy();
}
catch( Exception $e ){
    $msg = $e->getMessage();
    $T->ok( $msg, 'message exists 3' );
    $T->ok( is_string($msg), 'message exists 3' );
    //print "$e\n";
}

try{
    $LOG = Logger::get_logger( $e_config6 );
    $LOG->destroy();
}
catch( Exception $e ){
    $msg = $e->getMessage();
    $T->ok( $msg, 'message exists 3' );
    $T->ok( is_string($msg), 'message exists 3' );
    //print "$e\n";
}

$e_config7 = array(
    'use'       => array('LOGGER2', 'LOGGER1' ),
    'common'    => array( 'add_var' => false ),
    'appenders' => array(
            'LOGGER1'   => array( 'type' => 'stdout', 'log_format' => '%f:%L:%m' ),
            'LOGGER2'   => array( 'type' => 'file', 'filepath' => './test.log' ),
        )
    );

try{
    
    if( is_file( $e_config7['appenders']['LOGGER2']['filepath'] ) ){
        unlink( $e_config7['appenders']['LOGGER2']['filepath'] );
    }
    
    $LOG = Logger::get_logger( $e_config7 );
    
    $T->ok( $LOG, "Var Log exists" );
    
    $T->isa_ok( $LOG, $class );
    
    $T->ok( is_file( $e_config7['appenders']['LOGGER2']['filepath'] ), "File autocrteate" );
    
    $LOG->info( "Bla-bla" );

    clearstatcache();   # clean info about file
    
    $T->ok( filesize( $e_config7['appenders']['LOGGER2']['filepath'] ), "File has size more then 0" );

    unlink( $e_config7['appenders']['LOGGER2']['filepath'] );
    
    $LOG->destroy();
}
catch( Exception $e ){
    $msg = $e->getMessage();
}

//  config
$_config = array(
    'use'       => array( 'LOGGER1', 'LOGGER2' ),
    'common'    => array( 'add_var' => true ),
    'appenders' => array(
            'LOGGER1'   => array( 'type' => 'syslog', 'log_format' => '%f:%L:%m' ),
            'LOGGER2'   => array( 'type' => 'file', 'filepath' => '/home/anton/tmp/php.test.log' ),
        )
    );

try{
    $LOG = Logger::get_logger( $_config );
    $T->ok( $LOG, "Var Log exists" );
    $T->isa_ok( $LOG, $class );
    $LOG->destroy();
}
catch( Exception $e ){
    $msg = $e->getMessage();
}

//  config
$_config = array(
    'use'       => array( 'LOGGER1', 'LOGGER2' ),
    'appenders' => array(
            'LOGGER1'   => array( 'type' => 'syslog', 'log_format' => '%f:%L:%m' ),
            'LOGGER2'   => array( 'type' => 'file', 'filepath' => '/home/anton/tmp/php.test.log' ),
        )
    );

try{
    $LOG = Logger::get_logger( $_config );
    $T->ok( $LOG, "Var Log exists" );
    $T->isa_ok( $LOG, $class );
    $LOG->destroy();
}
catch( Exception $e ){
    $msg = $e->getMessage();
}

$T->finish();

?>
